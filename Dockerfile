FROM php:8.2-apache

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public \
    COMPOSER_ALLOW_SUPERUSER=1 \
    PATH="/var/www/html/vendor/bin:${PATH}"

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
        libicu-dev \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        intl \
        mbstring \
        opcache \
        pcntl \
        pdo \
        pdo_pgsql \
        xml \
        zip \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && a2enmod rewrite headers expires \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/99-development.ini
COPY docker/apache/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN groupmod -o -g "${GROUP_ID}" www-data \
    && usermod -o -u "${USER_ID}" -g www-data www-data \
    && chown -R www-data:www-data /var/www

WORKDIR /var/www/html
CMD ["apache2-foreground"]
