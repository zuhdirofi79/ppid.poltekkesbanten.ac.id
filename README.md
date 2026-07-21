# PPID Poltekkes Kemenkes Banten

Portal Pejabat Pengelola Informasi dan Dokumentasi (PPID) Politeknik Kesehatan Kementerian Kesehatan Banten.

Aplikasi **Laravel custom** (bukan WordPress). Production Hostinger memisahkan app root dan `public_html`.

## Requirements

- PHP 8.3+
- Composer 2
- Node.js 20+ / npm
- MySQL/MariaDB (production) atau SQLite (local)

## Instalasi lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan storage:link
php artisan serve
```

Detail: `docs/LOCAL_DEVELOPMENT.md`

## Setup database lokal

Disarankan SQLite:

```env
DB_CONNECTION=sqlite
```

MySQL: isi `DB_*` lalu `php artisan migrate && php artisan db:seed`.

## Test

```bash
php artisan test
```

## Login admin

- URL: `/admin/login`
- Super admin: sesuai `SUPER_ADMIN_EMAIL` / `SUPER_ADMIN_PASSWORD` di `.env`
- Default seeder: `qowiyrofi@poltekkesbanten.ac.id` (password di `.env` / seeder)

## Deploy Hostinger

Struktur:

```text
~/ppid-poltekkesbanten/   ← root Laravel
~/public_html/            ← isi public + index.php Hostinger
```

```bat
scripts\build-production.bat
powershell -ExecutionPolicy Bypass -File scripts\package-hostinger.ps1
```

Hasil: `dist/hostinger/app-root.zip` dan `dist/hostinger/public-html.zip`

Panduan lengkap:

- `docs/DEPLOYMENT_HOSTINGER.md`
- `deployment/hostinger/DEPLOYMENT.md`
- `deployment/hostinger/production-checklist.md`

**Penting:** `public/index.php` lokal tetap untuk development. File Hostinger ada di `deployment/hostinger/index.php`.

### Troubleshooting ringkas

| Masalah | Perbaikan |
|---------|-----------|
| 500 | Path `$appFolder`, vendor, permission `storage` |
| 404 semua | `.htaccess` + mod_rewrite |
| CSS hilang | Unggah `public/build` |
| Storage 404 | `php artisan storage:link` / symlink manual |

## Dokumentasi

| Dokumen | Isi |
|---------|-----|
| `docs/PROJECT_AUDIT.md` | Audit awal |
| `docs/ARCHITECTURE.md` | Arsitektur |
| `docs/ADMIN_GUIDE.md` | Panel admin |
| `docs/CONTENT_REQUIREMENTS.md` | Konten TODO |
| `docs/ASSET_REQUIREMENTS.md` | Gambar/logo |
| `docs/SECURITY.md` | Keamanan |
| `docs/TESTING.md` | Test |
| `docs/DEPLOYMENT_HOSTINGER.md` | Deploy |
| `docs/CHANGELOG_IMPLEMENTATION.md` | Riwayat fase |

## Identitas

- Logo: `public/images/logo/logo-poltekkes-banten.png`
- Hero: `public/images/hero/gedung-direktorat-poltekkes-banten.webp`
