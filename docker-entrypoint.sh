#!/usr/bin/env bash
# سكربت بدائي تشغيل حاوية Odai
set -e

WORKDIR="/var/www/html"
cd "$WORKDIR"

# طباعة التشخيص أولاً (للمراقبة في سجلّ Render)
echo "[entrypoint] DATABASE_URL=${DATABASE_URL:+مضبوط}${DATABASE_URL:- غير مضبوط!}"
echo "[entrypoint] DB_CONNECTION=${DB_CONNECTION:-غير مضبوط}"
echo "[entrypoint] APP_ENV=${APP_ENV:-غير مضبوط} APP_KEY=${APP_KEY:+مضبوط}"

# في بيئة Render تُحقن المتغيّرات مباشرة. نحذف أي .env قديم (قد يحتوي قيم mysql/homestead
# تطغى على المتغيّرات) ونحذف الكاش المجمد كلياً قبل أي شيء.
rm -f .env bootstrap/cache/config.php

# إن لم تكن DATABASE_URL مضبوطة، ننشئ .env محلي من .env.example (للتطوير المحلي فقط).
if [ -z "$DATABASE_URL" ]; then
    echo "[entrypoint] تحذير: DATABASE_URL غير مضبوط! تحقق من قاعدة البيانات على Render"
    [ -f .env.example ] && cp .env.example .env
fi

# توليد APP_KEY إن لم يكن مضبوطاً (Render يولّده تلقائياً عبر generateValue)
if [ -f .env ] && ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
fi

# مسح أي بقايا كاش قديم (لا نعتمد على config:clear لأنه قد يقرأ كاشاً معطوباً)
rm -f bootstrap/cache/config.php bootstrap/cache/routes.php bootstrap/cache/events.php
rm -rf storage/framework/cache/data/* storage/framework/sessions/* storage/framework/views/*
php artisan cache:clear 2>/dev/null || true

# ضمان صلاحيات مجلدات التخزين والكاش والسجلّات
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# الترحيل + تهيئة قاعدة البيانات (مرة واحدة عند التشغيل الأول عبر ملف علم)
if [ ! -f storage/app/.db-initialized ]; then
    echo "[entrypoint] تهيئة قاعدة البيانات..."
    php artisan migrate --force 2>&1 || echo "[entrypoint] تحذير: فشل الترحيل — تأكد من ضبط DB_*"
    php artisan db:seed --force 2>&1 || echo "[entrypoint] تحذير: فشل التهيئة (seeders)"
    touch storage/app/.db-initialized || true
else
    echo "[entrypoint] قاعدة البيانات مهيّأة مسبقاً، تشغيل الترحيل التحديثي فقط..."
    php artisan migrate --force 2>&1 || true
fi

# بناء الكاش للإنتاج (بعد ضبط .env والترحيل)
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# تشغيل Apache في المقدمة
exec apache2-foreground
