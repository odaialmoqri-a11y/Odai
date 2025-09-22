FROM php:8.2-apache

# تثبيت جميع extensions المطلوبة
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install intl zip pdo pdo_mysql

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN chmod -R 775 storage

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
