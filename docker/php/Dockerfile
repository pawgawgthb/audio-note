FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git unzip icu-dev oniguruma-dev libxml2-dev linux-headers $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql intl opcache \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
CMD ["php-fpm"]
