FROM php:8.2-apache

# تثبيت اعتماديات النظام
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) intl zip pdo pdo_mysql pdo_pgsql mbstring xml gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعدادات Composer لمتانة التحميل (تلافي أخطاء 504 العابرة من GitHub)
ENV COMPOSER_HTTP_TIMEOUT=120
ENV COMPOSER_MIRROR_PATH_REPOS=1
ENV COMPOSER_NO_INTERACTION=1

# إعدادات PHP للإنتاج
RUN echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/app.ini

# تفعيل mod_rewrite و mod_headers لـ Apache
RUN a2enmod rewrite headers

# توجيه DocumentRoot إلى مجلد public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# السماح بـ .htaccess في DocumentRoot
RUN printf '<Directory ${APACHE_DOCUMENT_ROOT}>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
    > /etc/apache2/conf-available/document-root.conf \
    && a2enconf document-root

WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# التحقق من سلامة composer.json قبل التثبيت (تشخيص واضح لأي فساد)
# التحقق من صحة composer.json
RUN composer validate --no-check-publish

# تثبيت اعتماديات PHP: install يحترم composer.lock المتزامن (أسرع وأكثر حتمية)
# مع update كاحتياط لإعادة حل القيود إن كان الـlock غير متزامن
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts --no-interaction --no-audit \
    || composer update --prefer-dist --no-dev --optimize-autoloader --no-scripts --no-interaction --no-audit \
    || (echo "======== فشل تثبيت اعتماديات composer ========" && exit 1)

# إعداد الصلاحيات لمجلدات التخزين والكاش
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 80

# سكربت بدء التشغيل: إنشاء المفتاح، الكاش، الترحيل، ثم تشغيل Apache
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

CMD ["docker-entrypoint.sh"]
