# Sử dụng PHP 8.3
FROM php:8.3-fpm

# Cài đặt các extension PHP cần thiết
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục làm việc
WORKDIR /var/www

# Copy toàn bộ mã nguồn vào container
COPY . .

# Cài đặt các package PHP qua Composer
RUN composer install

# Phân quyền
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Cấu hình supervisor
COPY ./docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 9000
CMD ["php-fpm"]
