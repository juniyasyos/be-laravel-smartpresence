FROM php:8.3-fpm

WORKDIR /var/www

# install system dependencies (INI PENTING)
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring xml zip gd

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-interaction --prefer-dist

RUN php artisan storage:link

EXPOSE 9000

CMD ["php-fpm"]