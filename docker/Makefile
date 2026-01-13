PROJECT_PREFIX=telegram

up:
	docker compose -p $(PROJECT_PREFIX) up -d

down:
	docker compose -p $(PROJECT_PREFIX) down

build:
	docker compose -f docker-compose.yml -f docker-compose.build.yml build

in:
	docker exec -u www-data -it $(PROJECT_PREFIX)_php bash

restart: down up in
