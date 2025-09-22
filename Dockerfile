FROM php:8.2-apache

# تثبيت الاعتماديات النظام
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl zip pdo pdo_mysql mbstring xml gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعدادات PHP
RUN a2enmod rewrite
RUN echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/memory.ini

WORKDIR /var/www/html

# نسخ الملفات
COPY . .

# تثبيت dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# إعداد الصلاحيات
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# تنظيف
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
