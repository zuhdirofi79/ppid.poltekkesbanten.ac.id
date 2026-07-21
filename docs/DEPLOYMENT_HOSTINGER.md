# Deployment Hostinger — PPID Poltekkes Kemenkes Banten

Dokumentasi lengkap untuk memisahkan **app root** dan **public_html** di shared hosting.

## Prinsip

Hanya direktori publik yang menjadi web root. Core Laravel (`app`, `vendor`, `.env`, `storage`) berada di luar `public_html`.

Struktur:

```text
/home/USER/
├── ppid-poltekkesbanten/     ← Laravel root
└── public_html/             ← isi public/ + index.php Hostinger
```

Sumber lokal tetap:

```text
C:\projects\ppid.poltekkesbanten.ac.id
C:\projects\ppid.poltekkesbanten.ac.id\public
```

## Persiapan lokal

```bat
scripts\build-production.bat
powershell -ExecutionPolicy Bypass -File scripts\package-hostinger.ps1
```

Atau package tanpa rebuild (jika build sudah ada):

```powershell
powershell -ExecutionPolicy Bypass -File scripts\package-hostinger.ps1 -SkipBuild
```

Output:

```text
dist/hostinger/app-root.zip
dist/hostinger/public-html.zip
```

Package **tidak** berisi: `.env`, `node_modules`, `.git`, (default) `tests`.

## Upload

1. Ekstrak `app-root.zip` → `~/ppid-poltekkesbanten/`
2. Ekstrak `public-html.zip` → `~/public_html/` (backup dulu isi lama bila ada)
3. Pastikan `public_html/index.php` adalah file dari `deployment/hostinger/index.php`

### Verifikasi path

Di File Manager Hostinger, `public_html` dan `ppid-poltekkesbanten` harus satu level.

Jika nama folder app berbeda, edit di `public_html/index.php`:

```php
$appFolder = 'ppid-poltekkesbanten';
```

File deployment memanggil:

- `../ppid-poltekkesbanten/vendor/autoload.php`
- `../ppid-poltekkesbanten/bootstrap/app.php`
- `$app->usePublicPath(__DIR__)` agar `public_path()` mengarah ke `public_html`

## Konfigurasi `.env` di server

Buat `~/ppid-poltekkesbanten/.env` (jangan taruh di `public_html`):

```env
APP_NAME="PPID Poltekkes Kemenkes Banten"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://ppid.poltekkesbanten.ac.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

FILESYSTEM_DISK=public
CACHE_STORE=file
QUEUE_CONNECTION=database
LOG_CHANNEL=stack
LOG_LEVEL=error

SUPER_ADMIN_EMAIL=qowiyrofi@poltekkesbanten.ac.id
SUPER_ADMIN_PASSWORD=
```

Isi DB sesuai panel Hostinger (MySQL).

## Perintah di server (SSH / Terminal)

```bash
cd ~/ppid-poltekkesbanten
composer install --no-dev --optimize-autoloader   # jika vendor tidak ikut terunggah utuh
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force   # opsional / sekali
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Permission:

```bash
chmod -R ug+rwx storage bootstrap/cache
```

## Storage link di shared hosting

### Opsi A — symlink (utama)

```bash
php artisan storage:link
```

Target yang diharapkan:

`public_html/storage` → `../ppid-poltekkesbanten/storage/app/public`

Karena `usePublicPath` mengarah ke `public_html`, `storage:link` biasanya membuat link di situ.

### Opsi B — symlink manual (File Manager / SSH)

```bash
ln -s ../ppid-poltekkesbanten/storage/app/public ~/public_html/storage
```

### Opsi C — tanpa symlink

Unggah berkas publik ke `public_html/storage/...` dan dokumentasikan prosedur admin (hindari duplikasi tanpa kontrol).

Variabel opsional di `.env.example`:

```env
# PUBLIC_DISK_ROOT=
# PUBLIC_ASSET_URL=
FILESYSTEM_DISK=public
```

## Domain & SSL

- Parkirkan domain `ppid.poltekkesbanten.ac.id` ke `public_html`
- Aktifkan SSL (Let's Encrypt) di hPanel
- `APP_URL` harus `https://...`
- `SESSION_SECURE_COOKIE=true`

## Troubleshooting

| Kode | Penyebab umum | Perbaikan |
|------|----------------|-----------|
| 500 | Path `$appFolder` salah / vendor hilang / permission | Cek `index.php`, unggah vendor, `chmod storage` |
| 404 route | `.htaccess` / rewrite off | Pastikan `.htaccess` Hostinger terpasang |
| 403 | Permission / file sensitif | Cek ownership & FilesMatch |
| CSS 404 | `build/` belum di `public_html` | Unggah ulang package public |
| Login session hilang | `SESSION_DOMAIN` / HTTPS | Sesuaikan cookie & `APP_URL` |
| Gambar upload 404 | Symlink storage | Opsi A/B di atas |

Log: `~/ppid-poltekkesbanten/storage/logs/laravel.log`

## Asset Vite & public_html (audit CSS hilang)

Jika HTML tampil tanpa styling:

1. Pastikan `~/public_html/build/manifest.json` dan `~/public_html/build/assets/` ada.
2. Pastikan `index.php` = versi Hostinger (`deployment/hostinger/index.php`), bukan WP / bukan `public/index.php` lokal.
3. Bersihkan sisa WordPress — lihat `docs/CLEAN_OLD_WORDPRESS_PUBLIC_HTML.md`.
4. Peta upload lengkap — lihat `docs/PUBLIC_HTML_UPLOAD_MAP.md`.

Setelah upload:

```bash
cd ~/ppid-poltekkesbanten
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

Jangan `config:cache` sebelum `APP_URL` dan DB terverifikasi.

## Referensi file

| File | Fungsi |
|------|--------|
| `deployment/hostinger/index.php` | Front controller production |
| `deployment/hostinger/.htaccess` | Rewrite Apache |
| `deployment/hostinger/DEPLOYMENT.md` | Ringkas |
| `deployment/hostinger/production-checklist.md` | Checklist QA |
| `scripts/build-production.bat` | Build aman |
| `scripts/package-hostinger.ps1` | Zip app + public |
