FROM php:8.3-fpm

# Gerekli paketleri yükle
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install zip pdo_pgsql

# Composer ekle
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html