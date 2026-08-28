# راهنمای تست نسخه ۱۱

ابزارهای PHPUnit، Mockery، Collision، Pint، Debugbar و Telescope در بخش
`require-dev` پروژه ثبت شده‌اند. این ابزارها در محیط توسعه نصب می‌شوند و هنگام
انتشار با دستور `composer install --no-dev` روی سرور Production نصب نخواهند شد.

## نصب در ویندوز

از PowerShell و داخل پوشه پروژه اجرا کنید:

```powershell
Set-ExecutionPolicy -Scope Process Bypass
.\scripts\install-test-tools.ps1
```

اسکریپت، پکیج‌های قفل‌شده در `composer.lock` را نصب می‌کند، فایل
`.env.testing` را می‌سازد و دیتابیس مستقل `database/testing.sqlite` را آماده
می‌کند.

## اجرای تست با دیتابیس مستقل

```powershell
composer test:database
```

این دستور ابتدا فقط migrationهای اجرا‌نشده را با `migrate` اعمال می‌کند و سپس
همه تست‌ها را اجرا می‌کند. هیچ `migrate:fresh` یا پاک‌سازی کلی دیتابیس انجام
نمی‌شود.

## اجرای تست روی دیتابیس فعلی توسعه

اگر می‌خواهید تست‌ها روی MySQL/MariaDB فعلی پروژه اجرا شوند، مقادیر `DB_*` را
از `.env` به `.env.testing` منتقل کنید و نام دیتابیس را عیناً تأیید کنید:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=نام_دیتابیس_توسعه
DB_USERNAME=root
DB_PASSWORD=
TEST_DATABASE_CONFIRMATION=نام_دیتابیس_توسعه
```

سپس اجرا کنید:

```powershell
php artisan optimize:clear
composer test:database
```

تمام تست‌های دیتابیسی از Transaction استفاده می‌کنند و داده‌های ساخته‌شده پس
از هر تست Rollback می‌شوند. بااین‌حال هنگام اجرای تست نباید برنامه دیگری روی
همین دیتابیس عملیات هم‌زمان انجام دهد. برای نتیجه تکرارپذیر، دیتابیس مستقل تست
انتخاب بهتری است.

## سایر دستورها

```powershell
composer test
composer test:unit
composer test:feature
composer lint
composer lint:fix
composer security:audit
```

## هنگام انتشار Production

فعلاً ابزارها در پروژه باقی می‌مانند. در زمان انتشار نهایی باید نصب با
`composer install --no-dev --optimize-autoloader` انجام شود و فایل
`.env.testing`، دیتابیس تست، Debugbar و Telescope روی سرور عمومی فعال نباشند.
