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

# تحديث الكاش للإنتاج (تجاهل الأخطاء إن لم تكن قاعدة البيانات جاهزة بعد)
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# ضمان صلاحيات التخزين
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# ترحيل قاعدة البيانات إن كانت متاحة (يتطلب DB_* مضبوطاً)
php artisan migrate --force 2>/dev/null || echo "[entrypoint] DB غير متاح بعد، تم تخطي الترحيل"

# تشغيل Apache في المقدمة
exec apache2-foreground
