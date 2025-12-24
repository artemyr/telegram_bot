#!/bin/bash
set -e

# Переходим в директорию скрипта
cd "$(dirname "${BASH_SOURCE[0]}")"

export COMPOSE_COMMAND="docker-compose"
export LOCAL_VENDOR_PATH="/var/www/html/local/php_interface/vendor"
if ! command -v ${COMPOSE_COMMAND} 2>&1 >/dev/null
  then
    COMPOSE_COMMAND="docker compose"
fi

# Проверка зависимостей
check_dependencies() {
    local dependencies=("${COMPOSE_COMMAND}" "sshpass" "rsync" "git")
    for dep in "${dependencies[@]}"; do
        if ! command -v $dep &> /dev/null; then
            echo -e "Ошибка: $dep не установлен."
            exit 1
        fi
    done
}

# Устанавливаем дефолтные значения для пользователя и группы
export DEFAULT_USER="1000"
export DEFAULT_GROUP="1000"

# Получаем ID пользователя и группы
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
export USER=$USER
export COMPOSER_AUTH=

# Если ID пользователя или группы равен 0, устанавливаем дефолтные значения
if [ "$USER_ID" == "0" ]; then
    USER_ID=$DEFAULT_USER
fi

if [ "$GROUP_ID" == "0" ]; then
    GROUP_ID=$DEFAULT_GROUP
fi

# Проверяем наличие .env файла и создаем его, если он отсутствует
test -e "./.env" || { cp .env.example .env; echo -e "Создан файл .env из .env.example"; }

# Загружаем переменные окружения из .env файла
if [ -f "./.env" ]; then
    source ./.env
else
    echo -e "Файл .env не найден"
    exit 1
fi

# Загружаем доступы к реестру композера из composer.auth.json если он существует
if [ -f composer.auth.json ]; then
  COMPOSER_AUTH=$(cat composer.auth.json)
fi

# Функция для выполнения команд в контейнере PHP
runInPhp() {
    local command=$@
    echo -e "Выполнение команды в контейнере PHP: $command"
    docker exec -i "${PROJECT_PREFIX}"_php bash -c "cd /var/www/html/; $command"
    return $?
}

# Функция для выполнения команд в контейнере MySQL
runInMySql() {
    local command=$@
    docker exec -i ${PROJECT_PREFIX}_mysql bash -c "$command"
    return $?
}

# Функция для применения дампа базы данных
applyDump() {
    cat $1 | docker exec -i ${PROJECT_PREFIX}_mysql mysql -u $MYSQL_USER -p"$MYSQL_PASSWORD" $MYSQL_DATABASE
    return $?
}

# Функция для создания дампа базы данных
makeDump() {
    runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE" > $1
    return $?
}

function enterInPhp {
    docker exec -u www-data -it "${PROJECT_PREFIX}"_php bash
    return $?
}

# Функция для синхронизации файлов с сервера
syncFiles() {
    sshpass -p $REMOTE_SSH_PASS rsync -rzclEt -e 'ssh -p $REMOTE_SSH_PORT' --progress --delete-after --exclude='web_release' --exclude='backup' --exclude='cache' --exclude='cache' --exclude='.settings_extra.php' --exclude='.settings.php' --exclude='php_interface' $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_BITRIX_URL $LOCAL_BITRIX_URL
    return $?
}

# Функция для синхронизации базы данных с сервера
syncDb() {
    makeDumpOnServer
    getDumpFromServer
    removeDumpFromServer
    loadDumpToDocker
}

# Функция для создания дампа на сервере
makeDumpOnServer() {
    sshpass -p $REMOTE_SSH_PASS ssh -p$REMOTE_SSH_PORT -o StrictHostKeyChecking=no -T $REMOTE_SSH_USER@$REMOTE_SSH_HOST <<-SSH
        if [ -f dump.sql.gz ]; then
            rm $REMOTE_MYSQL_DUMP_PATH
        fi
        mysqldump -h$REMOTE_MYSQL_HOST -u$REMOTE_MYSQL_USER -p'$REMOTE_MYSQL_PASS' $REMOTE_MYSQL_DB_NAME | gzip - > $REMOTE_MYSQL_DUMP_PATH
SSH
    return $?
}

# Функция для удаления дампа с сервера
removeDumpFromServer() {
    sshpass -p $REMOTE_SSH_PASS ssh -p $REMOTE_SSH_PORT -o StrictHostKeyChecking=no -T $REMOTE_SSH_USER@$REMOTE_SSH_HOST <<-SSH
        if [ -f dump.sql.gz ]; then
            rm $REMOTE_MYSQL_DUMP_PATH
        fi
SSH
    return $?
}

# Функция для получения дампа с сервера
getDumpFromServer() {
    sshpass -p "$REMOTE_SSH_PASS" rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_MYSQL_DUMP_PATH dump.sql.gz
    return $?
}

# Функция для загрузки дампа в Docker
loadDumpToDocker() {
    if [ -f dump.sql.gz ]; then
        docker/dctl.sh db import containers/mysql/drop_all_tables.sql
        gunzip -c dump.sql.gz | docker/dctl.sh db import -
    fi
    return $?
}

# Функция для просмотра логов
showLogs() {
    ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" logs -f "$@"
}

# Функция для перезапуска контейнеров
restartContainers() {
    ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" restart "$@"
}

# Функция для проверки статуса контейнеров
checkStatus() {
    ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" ps
}

# Функция для выполнения composer install
composerInstall() {
    echo "Выполнение composer install в /local/php_interface/"
    runInPhp "cd /var/www/html/local/php_interface/ && composer install"
    return $?
}

# Функция для выполнения composer update
composerUpdate() {
    echo "Выполнение composer update -W в /local/php_interface/"
    runInPhp "cd /var/www/html/local/php_interface/ && composer update -W"
    return $?
}

# Интерактивный режим
# shellcheck disable=SC2120
interactive_mode() {
    echo -e "Добро пожаловать в интерактивный режим!"

    while true; do
        read -r -e -p "Введите команду (help для справки, exit для выхода): " cmd
        case $cmd in
            help)
                echo -e "\n\033[1mПОМОЩЬ:\033[0m"
                echo -e "\n\033[1;34mDocker:\033[0m"
                echo "  build          - собрать Docker-образы"
                echo "  up             - запустить контейнеры"
                echo "  down           - остановить контейнеры"
                echo "  down full      - остановить все контейнеры"
                echo "  restart        - перезапустить контейнеры"
                echo "  status         - показать статус контейнеров"
                echo "  logs [SERVICE] - просмотр логов (php/nginx/mysql)"

                echo -e "\n\033[1;34mБаза данных:\033[0m"
                echo "  db             - подключиться к MySQL"
                echo "  db export      - экспорт базы данных"
                echo "  db import FILE - импорт SQL-файла"
                echo "  db renew       - обновить базу из репозитория"
                echo "  make dump      - создать дамп базы"

                echo -e "\n\033[1;34mПроект:\033[0m"
                echo "  init           - инициализировать проект"
                echo "  make env       - создать .env файл"
                echo "  in             - войти в PHP-контейнер"
                echo "  run CMD        - выполнить команду в PHP-контейнере"
                echo "  sync files     - синхронизировать файлы с сервера"
                echo "  sync db        - синхронизировать базу данных с сервера"

                echo -e "\n\033[1;34mComposer:\033[0m"
                echo "  composer install - установка зависимостей"
                echo "  composer update  - обновление зависимостей"
                ;;

            make\ env)
                cp .env.example .env
                echo -e "Скопирован .env.example в .env"
                ;;

            init)
                if [ ! -d "../${PROJECT_PREFIX}" ]; then
                    git clone "$PROJECT_REPO" ../"${PROJECT_PREFIX}" || echo "Project repo not found"
                fi

                if [ ! -d "../${PROJECT_PREFIX}/bitrix" ]; then
                    git clone "$BITRIX_REPO" ../"${PROJECT_PREFIX}"/bitrix/ || echo "Bitrix repo not found"
                fi
                ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" build
                ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" up -d
                if [ ! -d "../${PROJECT_PREFIX}/${LOCAL_VENDOR_PATH}" ]; then
                    runInPhp "cd local/php_interface/ && composer install"
                fi
                if [ ! -f "../${PROJECT_PREFIX}/local/php_interface/auth.json" ]; then
                    echo "Файла нет — условие выполнилось"
                    echo ${PROJECT_PREFIX}
                    cp ./composer.auth.json.example ../${PROJECT_PREFIX}/local/php_interface/auth.json
                else
                    echo "Файл существует — условие не сработало"
                fi
                ;;

            make\ dump)
                git clone "$DATABASE_REPO" ../docker/tmp/dump || echo "not clone repo"
                makeDump ../docker/tmp/dump/database.sql
                cd ../docker/tmp/dump
                git add database.sql
                git commit -a -m 'update database'
                git push origin master
                echo "PUSH SUCCESS"
                ;;

            db\ import\ *)
                file=$(echo "$cmd" | awk '{print $3}')
                applyDump "$file"
                ;;

            db\ renew)
                rm -rf "../docker/tmp/dump" || echo "old dump not found"
                git clone "$DATABASE_REPO" ../docker/tmp/dump
                applyDump "../docker/containers/mysql/drop_all_tables.sql"
                applyDump "../docker/tmp/dump/database.sql"
                ;;

            db)
                docker exec -it "${PROJECT_PREFIX}"_mysql mysql -u $MYSQL_USER -p"$MYSQL_PASSWORD" $MYSQL_DATABASE
                ;;

            db\ export)
                runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE"
                ;;

            build)
                ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" build
                ;;

            up)
                ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" up -d
                ;;

            down)
                ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" down
                ;;

            down\ full)
                docker stop $(docker ps -q)
                ;;

            run\ *)
                command=$(echo "$cmd" | awk '{print $2}')
                runInPhp "$command"
                ;;

            logs\ *)
                service=$(echo "$cmd" | awk '{print $2}')
                showLogs $service
                ;;

            restart)
                restartContainers "${@:2}"
                ;;

            status)
                checkStatus
                ;;

            in)
                enterInPhp
                ;;

            composer\ install)
                composerInstall
                ;;

            composer\ update)
                composerUpdate
                ;;

            sync\ files)
                syncFiles
                ;;

            sync\ db)
                syncDb
                ;;

            exit)
                echo -e "Выход из интерактивного режима."
                break
                ;;

            *)
                echo -e "Неизвестная команда: $cmd"
                ;;
        esac
    done
}

# Обрабатываем команды
if [ $# -eq 0 ]; then
    interactive_mode
else
    case "$1" in
        "make")
            case "$2" in
                "env")
                    cp .env.example .env
                    echo -e "Скопирован .env.example в .env"
                    ;;
                "dump")
                    git clone "$DATABASE_REPO" ../docker/tmp/dump || echo "not clone repo"
                    makeDump ../docker/tmp/dump/database.sql
                    cd ../docker/tmp/dump
                    tar -cjvf database.tar.bz2 database.sql
                    rm database.sql
                    git add database.tar.bz2
                    git commit -a -m 'update database'
                    git push origin master
                    echo "PUSH SUCCESS"
                    ;;
                *)
                    echo -e "Неизвестная подкоманда: $2"
                    ;;
            esac
            ;;
        "db")
            case "$2" in
                "")
                    docker exec -it "${PROJECT_PREFIX}"_mysql mysql -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"
                    ;;
                "export")
                    runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE"
                    ;;
                "import")
                    applyDump $3
                    ;;
                "renew")
                    rm -rf "../docker/tmp/dump" || echo "old dump not found"
                    git clone "$DATABASE_REPO" ../docker/tmp/dump
                    tar -xvf ../docker/tmp/dump/database.tar.bz2 -C ../docker/tmp/dump/
                    applyDump "../docker/containers/mysql/drop_all_tables.sql"
                    applyDump "../docker/tmp/dump/database.sql"
                    ;;
                *)
                    echo -e "Неизвестная подкоманда: $2"
                    ;;
            esac
            ;;
        "build")
            ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" build
            ;;
        "init")
            if [ ! -d "../${PROJECT_PREFIX}" ]; then
                git clone "$PROJECT_REPO" ../"${PROJECT_PREFIX}" || echo "Project repo not found"
            fi

            if [ ! -d "../${PROJECT_PREFIX}/bitrix" ]; then
                git clone "$BITRIX_REPO" ../"${PROJECT_PREFIX}"/bitrix/ || echo "Bitrix repo not found"
            fi
            ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" build
            ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" up -d
            if [ ! -d "../${PROJECT_PREFIX}/${LOCAL_VENDOR_PATH}" ]; then
                runInPhp "cd local/php_interface/ && composer install"
            fi
            if [ ! -f "../${PROJECT_PREFIX}/local/php_interface/auth.json" ]; then
                cp ./composer.auth.json.example ../${PROJECT_PREFIX}/local/php_interface/auth.json
            fi
            ;;
        "up")
            ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" build
            ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" up -d
            ;;
        "in")
            enterInPhp "${@:2}"
            ;;
        "down")
            case "$2" in
                "full")
                    docker stop $(docker ps -q)
                    ;;
                "")
                    ${COMPOSE_COMMAND} -p "${PROJECT_PREFIX}" down
                    ;;
                *)
                    echo -e "Неизвестная подкоманда: $2"
                    ;;
            esac
            ;;
        "run")
            if [ "$2" == "" ]; then
                docker exec -u www-data -it "${PROJECT_PREFIX}"_php bash
            else
                runInPhp "${@:2}"
            fi
            ;;
        "logs")
            showLogs "${@:2}"
            ;;
        "restart")
            restartContainers "${@:2}"
            ;;
        "status")
            checkStatus
            ;;
        "composer")
            case "$2" in
                "install")
                    composerInstall
                    ;;
                "update")
                    composerUpdate
                    ;;
                *)
                    echo -e "Неизвестная подкоманда: $2"
                    echo "Доступные команды:"
                    echo "  composer install - установка зависимостей"
                    echo "  composer update - обновление зависимостей"
                    ;;
            esac
            ;;
        "sync")
            case "$2" in
                "files")
                    syncFiles
                    ;;
                "db")
                    syncDb
                    ;;
                *)
                    echo -e "Неизвестная подкоманда: $2"
                    echo "Доступные команды:"
                    echo "  sync files - синхронизировать файлы"
                    echo "  sync db - синхронизировать базу данных"
                    ;;
            esac
            ;;
        *)
            echo -e "Неизвестная команда: $1"
            ;;
    esac
fi
