FROM php:8.3.6-fpm-alpine3.19

ARG USER_ID='1000'
ARG USER_ID=${USER_ID}
ENV USER_ID=${USER_ID}

ARG GROUP_ID='1000'
ARG GROUP_ID=${GROUP_ID}
ENV GROUP_ID=${GROUP_ID}

ARG PROJECT_PREFIX='web'
ARG PROJECT_PREFIX=${PROJECT_PREFIX}
ENV PROJECT_PREFIX=${PROJECT_PREFIX}

RUN apk --no-cache add shadow && \
    groupmod -o -g ${GROUP_ID} www-data && \
    usermod -o -u ${USER_ID} -g www-data www-data

RUN apk add --update linux-headers
RUN apk add --no-cache curl bash vim ssmtp autoconf build-base zlib-dev oniguruma-dev

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions openssl pdo_mysql mbstring tokenizer xml mysqli imagick redis

RUN apk add supervisor

ADD ./conf/php.ini /usr/local/etc/php/php.ini
ADD ./conf/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./conf/ssmtp.conf /etc/ssmtp/ssmtp.conf
COPY ./supervisord.conf /etc/supervisord.conf


RUN mkdir -p /var/log/supervisor /var/run/supervisor

WORKDIR /var/www/html
RUN export HOME="/var/www/html"

CMD ["/usr/bin/supervisord", "-n"]
