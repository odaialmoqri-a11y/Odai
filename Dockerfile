FROM php:8.2-apache

WORKDIR /var/www/html

# نسخ الملفات
COPY . .

# تثبيت dependencies النظام
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# تثبيت Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# تثبيت dependencies المشروع
RUN composer install --no-dev --optimize-autoloader

# إعدادات Apache
RUN a2enmod rewrite

# تعيين الصلاحيات
RUN chown -R www-data:www-data /var/www/html/storage
RUN chmod -R 775 /var/www/html/storage

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
