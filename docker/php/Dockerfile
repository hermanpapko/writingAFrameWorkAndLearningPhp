FROM php:8.4-fpm-alpine

RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www/html/var && chown -R www-data:www-data /var/www/html/var

WORKDIR /var/www/html