# Odai

نظام إدارة مدرسية (School ERP) عربي ومفتوح المصدر مبني على **Laravel 10** و**Filament** و**Livewire** و**Vue**.

يدعم إدارة القبول والحضور والامتحانات والرسوم والمكتبة والمواصلات والموارد البشرية والمزيد.
الواجهة عربية (RTL) بشكل افتراضي، وقابلة للنشر عبر Docker أو Render.

---

## المميزات

- 🌐 **عربي بالكامل افتراضياً** (RTL) مع دعم الإنجليزية كلغة احتياطية.
- 🐘 بنية Laravel 10 قابلة للتوسّع، API-first ومعيارية.
- 🐳 جاهز للنشر عبر **Docker** و**Docker Compose** و**Render**.
- 🎛️ أدوار وصلاحيات، تقارير، ومراجعة (Audits).
- 📦 رخصة MIT.

---

## المتطلبات

- PHP **8.1+** مع الإضافات: `intl`, `zip`, `pdo_mysql`, `mbstring`, `xml`, `gd`
- Composer 2
- MySQL 8
- Node.js و npm (لبناء الأصول الأمامية فقط)

---

## التشغيل المحلي (الطريقة المختصرة — Docker)

أسرع طريقة لتشغيل المشروع كاملاً (التطبيق + قاعدة بيانات MySQL):

```bash
# 1) انسخ ملف البيئة واضبط قيمه
cp .env.example .env

# 2) شغّل الحاويات (التطبيق + قاعدة البيانات)
docker compose up -d --build

# 3) افتح المتصفح
#    http://localhost:8080
```

سيقوم سكربت بدء التشغيل (`docker-entrypoint.sh`) تلقائياً بـ:
- توليد `APP_KEY` إن لم يكن موجوداً.
- بناء كاش الإعدادات والمسارات والعروض.
- تنفيذ ترحيلات قاعدة البيانات (`php artisan migrate`).

> لإنشاء بيانات أولية (Seeders): `docker compose exec app php artisan db:seed`

---

## التشغيل اليدوي (بدون Docker)

```bash
# 1) تثبيت اعتماديات PHP
composer install --no-dev --optimize-autoloader

# 2) إعداد البيئة
cp .env.example .env
php artisan key:generate

# 3) اضبط إعدادات قاعدة البيانات في .env ثم:
php artisan migrate --force
php artisan db:seed            # اختياري: بيانات أولية

# 4) بناء الأصول الأمامية
npm install
npm run prod

# 5) تشغيل خادم التطوير
php artisan serve
```

افتح `http://localhost:8000`.

---

## النشر على Render

المشروع يحتوي على `render.yaml` يصف خدمتي:

- `odai` (web): التطبيق مبني عبر `Dockerfile`.
- `odai-db` (pserv): قاعدة بيانات MySQL 8.

للنشر:

1. اربط مستودع GitHub على [Render](https://render.com).
2. اختر **Blueprint** وحدّد ملف `render.yaml`.
3. اضبط قيم `APP_URL` و`APP_KEY` و`DB_PASSWORD` من لوحة متغيرات البيئة.
4. اضغط **Deploy**.

---

## ضبط اللغة والاتجاه

الإعدادات الافتراضية عربية عبر متغيرات البيئة (في `.env`):

```env
APP_NAME=Odai
APP_LOCALE=ar
APP_FALLBACK_LOCALE=ar
APP_FAKER_LOCALE=ar_JO
TIMEZONE=Asia/Riyadh
```

ملفات الترجمة العربية في `lang/ar/` و `lang/ar.json`.
الاتجاه (RTL/LTR) يُضبط تلقائياً حسب اللغة الحالية في قوالب التخطيط.

---

## الاختبار

```bash
composer test                       # عبر phpunit
php artisan dusk                    # اختبارات المتصفح (Dusk)
```

انظر `test.md` و`api.md` لتفاصيل أكثر.

---

## الترخيص

رخصة MIT — راجع ملف `LICENSE`.
