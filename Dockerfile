FROM php:7.2-cli-alpine

RUN apk add --virtual build-deps $PHPIZE_DEPS \
    && apk add --no-cache \
        autoconf \
        make \
        gcc \
        g++ \
        git

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN apk del build-deps $PHPIZE_DEPS && rm -rf /var/cache/apk/*

COPY . /var/www

COPY ./docker/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www
