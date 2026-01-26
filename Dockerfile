FROM php:8.2-fpm

# Install system dependencies & library pendukung ekstensi
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip

# Clear cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (Sekarang sudah termasuk intl dan zip)
RUN docker-php-ext-configure intl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy semua file project
COPY . /var/www

# Install dependensi PHP (Laravel)
# Gunakan --ignore-platform-reqs jika masih ada masalah versi minor
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions untuk Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]