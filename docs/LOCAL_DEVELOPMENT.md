# Local Development

## Requirements

- PHP 8.3+, Composer 2, Node 20+, npm

## Setup

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Database lokal (disarankan SQLite):

```env
DB_CONNECTION=sqlite
```

```bash
# pastikan database/database.sqlite ada
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Vite watch (opsional):

```bash
composer run dev
```

## Admin lokal

- `/admin/login`
- Super admin: `SUPER_ADMIN_EMAIL` / `SUPER_ADMIN_PASSWORD` di `.env`

## Catatan

- Jangan pakai `migrate:fresh` kecuali di DB lokal disposable
- `public/index.php` tetap path Laravel standar (bukan Hostinger)
