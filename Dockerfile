FROM php:8.2-fpm

# Встановлення системних залежностей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Встановлення Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Встановлення робочої директорії
WORKDIR /var/www/html

# Копіювання файлів проєкту
COPY . .

# Встановлення залежностей (у dev-режимі без --no-dev)
RUN composer install --optimize-autoloader

# Встановлення прав доступу
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
