#!/usr/bin/env bash
# سكربت بدائي تشغيل حاوية Odai
set -e

WORKDIR="/var/www/html"
cd "$WORKDIR"

# ===== تشخيص صريح يظهر دائماً في سجلّ Render =====
echo "========================================"
echo "[entrypoint] بدء تشغيل الحاوية"
echo "[entrypoint] DATABASE_URL=${DATABASE_URL:+مضبوط}${DATABASE_URL:- غير مضبوط! تحقق من قاعدة البيانات على Render}"
echo "[entrypoint] DB_CONNECTION=${DB_CONNECTION:-غير مضبوط}"
echo "[entrypoint] APP_ENV=${APP_ENV:-غير مضبوط}"
echo "[entrypoint] APP_KEY=${APP_KEY:+مضبوط}${APP_KEY:- غير مضبوط}"
echo "========================================"

# حذف أي .env أو كاش قديم قد يحتوي قيم mysql/homestead مجمدة
rm -f .env bootstrap/cache/config.php bootstrap/cache/routes.php bootstrap/cache/events.php
rm -rf storage/framework/cache/data/* storage/framework/sessions/* storage/framework/views/*

if [ -n "$DATABASE_URL" ]; then
    # بيئة Render: اكتب .env صريحاً يحتوي على المتغيّرات الأساسية فقط من البيئة،
    # لضمان قراءتها بغضّ النظر عن أي .env.example منسوخ سابقاً.
    cat > .env <<EOF
APP_NAME=${APP_NAME:-Odai}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_LOCALE=${APP_LOCALE:-ar}
TIMEZONE=${TIMEZONE:-Asia/Riyadh}
DATABASE_URL=${DATABASE_URL}
DB_CONNECTION=${DB_CONNECTION:-pgsql}
CACHE_DRIVER=${CACHE_DRIVER:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
QUEUE_DRIVER=${QUEUE_DRIVER:-database}
LOG_CHANNEL=daily
FILESYSTEM_DRIVER=${FILESYSTEM_DRIVER:-local}
EOF
    echo "[entrypoint] تم إنشاء .env من متغيّرات Render (DATABASE_URL موجود)"
else
    # تطوير محلي: انسخ .env.example
    echo "[entrypoint] DATABASE_URL غير موجود — استخدام .env.example (تطوير محلي)"
    [ -f .env.example ] && cp .env.example .env
fi

# توليد APP_KEY إن لم يكن مضبوطاً
if [ -f .env ] && ! grep -q "^APP_KEY=base64:" .env; then
    php artisan key:generate --force || true
fi

# ضمان صلاحيات مجلدات التخزين والكاش والسجلّات
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# الترحيل + تهيئة قاعدة البيانات (مرة واحدة عند التشغيل الأول عبر ملف علم)
if [ ! -f storage/app/.db-initialized ]; then
    echo "[entrypoint] تهيئة قاعدة البيانات..."
    php artisan migrate --force 2>&1 || echo "[entrypoint] تحذير: فشل الترحيل — تأكد من ضبط DATABASE_URL على Render"
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

echo "[entrypoint] اكتمل التشغيل، إطلاق Apache"
# تشغيل Apache في المقدمة
exec apache2-foreground
