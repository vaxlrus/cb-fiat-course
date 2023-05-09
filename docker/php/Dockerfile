# Build image based on php 8.1 fpm
FROM php:8.1-cli

# Arguments defined in docker-compose.yml
ARG tz

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl nano grep zip unzip\
    libpq-dev \
    tzdata \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install and enable xdebug
RUN pecl install xdebug pecl redis \
    && docker-php-ext-enable xdebug redis

# Install Postgre PDO
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql
RUN docker-php-ext-enable pdo pdo_pgsql pgsql

# Install latest composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set timezone based on .env
RUN echo "$tz" > /etc/timezone

# Set working directory
WORKDIR /var/www/

# Change files and folders ownership
RUN chown -R www-data:www-data .
