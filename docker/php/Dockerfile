# Gunakan image dasar resmi PHP 8.2-FPM Alpine
FROM php:8.2-fpm-alpine

# Set direktori kerja di dalam kontainer
WORKDIR /var/www/html

# Instal dependensi yang dibutuhkan untuk ekstensi PHP
RUN apk add --no-cache \
    build-base \
    libpng-dev \
    jpeg-dev \
    # png-dev DIHAPUS DARI SINI KARENA SALAH \
    zip \
    unzip \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm

# Instal ekstensi PHP yang umum digunakan oleh Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip

# Instal Composer (dependency manager untuk PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ubah kepemilikan direktori ke user www-data
RUN chown -R www-data:www-data /var/www/html

# Salin konfigurasi kustom untuk upload
COPY uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Ganti user ke www-data
USER www-data

# Expose port 9000 untuk FPM
EXPOSE 9000