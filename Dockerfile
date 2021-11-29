FROM composer:latest as deps
COPY composer.json composer.lock ./
RUN composer install -o --apcu-autoloader

FROM php:8.0.13-fpm-alpine3.13
RUN apk add --update $PHPIZE_DEPS
RUN pecl install apcu && docker-php-ext-enable apcu
RUN docker-php-ext-install opcache
WORKDIR /app
COPY jit.ini ${PHP_INI_DIR}/conf.d/jit.ini
RUN addgroup php-fpm-users && adduser -D -G php-fpm-users php-fpm
USER php-fpm

ENV PHP_OPCACHE_ENABLE_CLI="1"
ENV PHP_OPCACHE_JIT="on"
ENV PHP_OPCACHE_JIT_BUFFER_SIZE="128M"
COPY --from=deps /app/vendor ./vendor
COPY *.php .
CMD php -r 'var_dump(opcache_get_status()["jit"]);'; php index.php
