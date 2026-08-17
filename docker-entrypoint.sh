#!/usr/bin/env bash
# سكربت بداية تشغيل حاوية Odai
set -e

WORKDIR="/var/www/html"
cd "$WORKDIR"

# في بيئة Render، تُحقن المتغيّرات مباشرة (DATABASE_URL, APP_KEY, ...).
# ملف .env المُنسوخ من .env.example يحوي قيم mysql/homestead قديمة تطغى
# على متغيّرات البيئة، لذلك نحذفه لنسمح لمتغيّرات Render بالتطبيق مباشرة.
if [ -n "$DATABASE_URL" ]; then
    rm -f .env
fi

# إن لم يوجد .env ولم تكن هناك DATABASE_URL، ننشئه من .env.example (للتطوير المحلي)
if [ ! -f .env ] && [ -z "$DATABASE_URL" ]; then
    cp .env.example .env
fi

# توليد APP_KEY إن لم يكن مُضبوطاً (Render يولّده تلقائياً عبر generateValue)
if [ -z "$APP_KEY" ] && [ ! -f .env ]; then
    : # في بيئة Render، APP_KEY يُحقن من المتغيّرات البيئية
fi
if [ -f .env ] && ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
fi

# مسح الكاش القديم (قد يحوي قيم .env قديمة مجمّدة)
php artisan config:clear 2>/dev/null || true
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
