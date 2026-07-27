# پنل پشتیبانی مشتریان

سیستم ساده مدیریت مشتریان، پروژه‌ها و تیکت‌ها با **Laravel 13** و **Blade**.

## امکانات

### مدیر

- مدیریت مشتریان با وضعیت فعال و غیرفعال
- ایجاد چند پروژه برای هر مشتری
- مدیریت وضعیت پروژه‌ها
- مشاهده، فیلتر و پاسخ به تمام تیکت‌ها
- تغییر وضعیت تیکت بین باز، درحال بررسی و بسته
- مشاهده و دریافت فایل‌های پیوست خصوصی
- داشبورد آماری
- ثبت لاگ عملیات مهم

### مشتری

- ورود با ایمیل و رمز عبور
- جلوگیری از ورود حساب غیرفعال
- مشاهده پروژه‌های متعلق به خود
- ثبت تیکت فقط برای پروژه فعال خود
- مشاهده و پاسخ به تیکت‌های خود
- عدم امکان پاسخ به تیکت بسته
- بازیابی و تغییر رمز عبور

## نیازمندی‌ها

- PHP 8.3 یا جدیدتر
- Composer 2
- SQLite، MySQL یا PostgreSQL
- افزونه‌های PHP موردنیاز Laravel

## نصب

```bash
git clone https://github.com/yekta-kalantary/support-panel.git
cd support-panel

cp .env.example .env
composer install

php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed

php artisan serve
```

سپس آدرس زیر را باز کنید:

```text
http://127.0.0.1:8000
```

## حساب مدیر اولیه

مقادیر حساب مدیر از `.env` خوانده می‌شوند:

```dotenv
ADMIN_FIRST_NAME=مدیر
ADMIN_LAST_NAME=سیستم
ADMIN_EMAIL=admin@example.com
ADMIN_MOBILE=09120000000
ADMIN_PASSWORD="ChangeMe123!"
```

پیش از اجرای Seeder در محیط واقعی، این مقادیر را تغییر دهید.

## اجرای تست‌ها

```bash
composer test
```

بررسی استایل کد:

```bash
vendor/bin/pint --test
```

## ایمیل و Queue

در محیط توسعه، ایمیل‌ها به‌صورت پیش‌فرض در فایل لاگ نوشته می‌شوند:

```dotenv
MAIL_MAILER=log
QUEUE_CONNECTION=sync
```

در محیط عملیاتی، Mailer واقعی و Queue مبتنی بر Database یا Redis تنظیم شود.

## فایل‌های پیوست

پیوست‌ها در فضای خصوصی ذخیره می‌شوند و فقط از طریق Controller دارای Authorization قابل دریافت هستند. محدودیت پیش‌فرض:

- حداکثر 5 فایل در هر پیام
- حداکثر 10 مگابایت برای هر فایل
- فرمت‌های JPG، JPEG، PNG، WEBP، PDF، TXT و ZIP

## ساختار اصلی

```text
app/
├── Enums
├── Http
│   ├── Controllers
│   │   ├── Admin
│   │   ├── Auth
│   │   └── Portal
│   ├── Middleware
│   └── Requests
├── Models
├── Notifications
├── Policies
└── Services

resources/views/
├── admin
├── auth
├── layouts
├── portal
├── profile
└── tickets
```

## مستند نیازمندی‌ها

نسخه کامل PRD در مسیر زیر قرار دارد:

```text
docs/PRD.md
```

## نکات عملیاتی

- `APP_DEBUG` در محیط عملیاتی باید `false` باشد.
- استفاده از HTTPS الزامی است.
- Queue Worker برای اعلان‌های ایمیلی فعال شود.
- از پایگاه داده و `storage/app/private` نسخه پشتیبان تهیه شود.
- رمز حساب مدیر اولیه بلافاصله تغییر کند.
