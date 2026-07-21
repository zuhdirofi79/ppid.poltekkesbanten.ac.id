# Production Checklist — Hostinger

## Sebelum upload

- [ ] `npm run build` sukses (`public/build` ada)
- [ ] `composer install --no-dev --optimize-autoloader` (atau lewat script package)
- [ ] Tidak ada credential di file yang di-zip
- [ ] `.env` tidak ikut package
- [ ] Tidak ada referensi “Poltekkes Semarang” di konten final
- [ ] Hero & logo ada di `public/images/`

## Setelah upload

- [ ] Folder app = `ppid-poltekkesbanten` (atau `$appFolder` disesuaikan)
- [ ] `public_html/index.php` = versi Hostinger (`usePublicPath` aktif)
- [ ] `public_html/.htaccess` terpasang
- [ ] `.env` production di app root:

```env
APP_NAME="PPID Poltekkes Kemenkes Banten"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ppid.poltekkesbanten.ac.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

FILESYSTEM_DISK=public
CACHE_STORE=file
QUEUE_CONNECTION=database

SUPER_ADMIN_EMAIL=qowiyrofi@poltekkesbanten.ac.id
SUPER_ADMIN_PASSWORD=
```

- [ ] `php artisan key:generate` (jika APP_KEY kosong)
- [ ] `php artisan migrate --force`
- [ ] Seed admin hanya jika perlu
- [ ] `storage` writable + link/symlink
- [ ] `config:cache` / `route:cache` / `view:cache`
- [ ] HTTPS aktif (Force HTTPS di hPanel bila perlu)

## Uji fungsional

- [ ] Beranda `/` 200
- [ ] Asset CSS/JS load (tanpa 404 `/build/...`)
- [ ] Login `/admin/login`
- [ ] Upload dokumen (jika dipakai)
- [ ] `/sitemap.xml` & `/robots.txt`
- [ ] 404 custom
- [ ] Tidak ada error 500 dengan `APP_DEBUG=false` (cek `storage/logs`)

## Troubleshooting

| Gejala | Cek |
|--------|-----|
| 500 blank | `storage/logs/laravel.log`, permission storage, path `$appFolder` |
| 404 semua route | `.htaccess`, `mod_rewrite`, AllowOverride |
| CSS hilang | `public/build` terunggah? `APP_URL` benar? |
| 403 | permission folder, file sensitif |
| Login gagal | session table migrated? `SESSION_DRIVER`? |
| Gambar storage 404 | symlink `public_html/storage` |
