# Deployment Hostinger — PPID Poltekkes Kemenkes Banten

## Struktur target

```text
/home/USER/
├── ppid-poltekkesbanten/          ← package APP-ROOT
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── artisan
│   ├── composer.json
│   ├── composer.lock
│   └── .env                      ← buat di server, JANGAN dari zip
│
└── public_html/                  ← package PUBLIC-HTML
    ├── index.php                 ← dari deployment/hostinger/index.php
    ├── .htaccess                 ← dari deployment/hostinger/.htaccess
    ├── build/
    ├── images/
    ├── favicon / robots.txt
    └── (aset publik lain)
```

**Verifikasi path:** `public_html` dan `ppid-poltekkesbanten` harus saudara kandung.
Jika nama folder app berbeda, edit `$appFolder` di `public_html/index.php`.

## Yang TIDAK boleh di public_html

- `.env`
- `vendor/`
- `app/`, `config/`, `database/`, `resources/`, `routes/`, `storage/`
- `node_modules/`, `.git/`

## Langkah singkat

1. Di lokal: jalankan `scripts\package-hostinger.ps1`
2. Unggah `dist/hostinger/app-root.zip` → ekstrak ke `ppid-poltekkesbanten/`
3. Unggah `dist/hostinger/public-html.zip` → ekstrak ke `public_html/` (ganti `index.php` dengan versi Hostinger)
4. Buat `.env` production di `ppid-poltekkesbanten/`
5. SSH / Terminal Hostinger:

```bash
cd ~/ppid-poltekkesbanten
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force   # hanya jika perlu akun awal
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

6. Pastikan permission `storage/` dan `bootstrap/cache/` writable (775/775 atau sesuai Hostinger)

## Storage tanpa symlink

Jika `storage:link` gagal:

```bash
# Buat junction/symlink manual (jika Hostinger izinkan):
# public_html/storage  ->  ../ppid-poltekkesbanten/storage/app/public
```

Atau unggah file ke `public_html/storage/...` dan sesuaikan `FILESYSTEM_DISK` / dokumentasi di `docs/DEPLOYMENT_HOSTINGER.md`.

## index.php lokal vs Hostinger

| File | Dipakai untuk |
|------|----------------|
| `public/index.php` | Development lokal (`php artisan serve`) |
| `deployment/hostinger/index.php` | Disalin ke `public_html/index.php` di server |

Jangan menimpa `public/index.php` lokal dengan path Hostinger.
