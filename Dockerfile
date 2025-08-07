FROM php:8.4-fpm

# Install required extensions (adjust as needed)
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libpq-dev libicu-dev acl file gettext \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql intl zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files and install dependencies first (for layer caching)
COPY composer.* symfony.* ./

COPY . ./

RUN composer install --prefer-dist --no-progress --no-scripts

RUN mkdir -p var/cache var/log && chown -R www-data:www-data var
