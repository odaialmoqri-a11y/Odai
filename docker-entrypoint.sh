#!/usr/bin/env bash
# سكربت بدء تشغيل حاوية Odai
set -e

WORKDIR="/var/www/html"
cd "$WORKDIR"

# إنشاء ملف .env من .env.example إن لم يكن موجوداً
if [ ! -f .env ]; then
    cp .env.example .env
fi

# توليد APP_KEY إن لم يكن مضبوطاً
if ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
fi

# ضمان صلاحيات التخزين
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# ترحيل + تهيئة قاعدة البيانات (قبل بناء الكاش لتفادي تجميد قيم .env)
# يُنفّذ مرة واحدة عند التشغيل الأول عبر ملف علم
if [ ! -f storage/app/.db-initialized ]; then
    echo "[entrypoint] تهيئة قاعدة البيانات..."
    php artisan migrate --force 2>&1 || echo "[entrypoint] تحذير: فشل الترحيل — تأكد من ضبط DB_*"
    php artisan db:seed --force 2>&1 || echo "[entrypoint] تحذير: فشل التهيئة (seeders)"
    touch storage/app/.db-initialized || true
else
    echo "[entrypoint] قاعدة البيانات مهيأة مسبقاً، تشغيل الترحيل التحديثي فقط..."
    php artisan migrate --force 2>&1 || true
fi

# تحديث الكاش للإنتاج (بعد ضبط .env والترحيل)
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# تشغيل Apache في المقدمة
exec apache2-foreground
