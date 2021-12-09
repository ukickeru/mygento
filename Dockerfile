FROM php:8.0.2-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    postgresql-dev

RUN docker-php-ext-install \
    ctype \
    iconv \
    pdo \
    pdo_mysql \
    pdo_pgsql

WORKDIR /srv/www/api

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./
RUN set -eux; \
	composer install --prefer-dist --no-scripts --no-progress --no-suggest; \
	composer clear-cache

COPY bin bin/
COPY config config/
COPY public public/
COPY src src/
COPY .env.local .env.local

RUN set -eux; \
	mkdir -p var/cache var/log; \
	chmod +x bin/console; sync
