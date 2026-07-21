# Hostinger realtime logging — PPID Poltekkes Kemenkes Banten

## Konteks

- Laravel root: `~/ppid-poltekkesbanten`
- Document root: `~/public_html` (`index.php` dari `deployment/hostinger/index.php`)
- File log wajib: `~/ppid-poltekkesbanten/storage/logs/laravel.log`
- Hostinger **File Manager tidak realtime** — kolom “Last modified” tidak otomatis berubah; folder harus di-refresh manual. Jangan andalkan File Manager sebagai log viewer.

## Setup permission & diagnose

```bash
cd ~/ppid-poltekkesbanten

mkdir -p storage/logs
touch storage/logs/laravel.log
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chmod 664 storage/logs/laravel.log

php artisan optimize:clear
php artisan ppid:logging-diagnose
```

Opsional (CLI only, tidak crash website):

```bash
php artisan ppid:test-error-logging
```

## Pantau realtime via SSH

```bash
cd ~/ppid-poltekkesbanten
tail -f storage/logs/laravel.log
```

1. Biarkan terminal `tail -f` terbuka.
2. Refresh browser: `https://ppid.poltekkesbanten.ac.id`
3. Baris baru muncul di terminal jika proses PHP dapat menulis ke file.
4. Hentikan dengan `Ctrl+C`.

## Apa yang tercatat di laravel.log

| Sumber | Marker / channel |
|--------|------------------|
| Laravel `Log::*` / exception reporting | channel `single` → `storage/logs/laravel.log` |
| PHP `error_log` setelah `index.php` Hostinger berjalan | diarahkan ke file yang sama via `ini_set('error_log', …)` |
| Fatal / parse setelah `index.php` mulai | `[HOSTINGER-BOOTSTRAP]` via shutdown handler |
| Missing `vendor/autoload.php` / `bootstrap/app.php` | `[HOSTINGER-BOOTSTRAP]` + HTTP 500 generik |
| Uncaught `Throwable` yang lolos lifecycle | `[HOSTINGER-BOOTSTRAP]` + HTTP 500 generik |

## Upload index.php

Salin:

```text
deployment/hostinger/index.php
```

menjadi:

```text
~/public_html/index.php
```

Jangan menimpa `public/index.php` lokal development.

## `.env` saat investigasi

Sementara:

```env
APP_ENV=production
APP_DEBUG=true
LOG_CHANNEL=single
LOG_LEVEL=debug
```

Setelah akar masalah ditemukan, kembalikan:

```env
APP_DEBUG=false
LOG_LEVEL=error
```

Lalu:

```bash
php artisan optimize:clear
php artisan config:cache
```

## Keterbatasan (tetap cek PHP error log Hostinger)

Error berikut **tidak** selalu bisa ditangkap oleh kode di `public_html/index.php`:

- Parse error **di dalam** `public_html/index.php` sendiri
- PHP startup / web server fatal sebelum skrip dieksekusi
- OOM yang menghentikan proses sebelum shutdown handler jalan
- Permission yang mencegah PHP menulis ke `storage/logs` sama sekali

Untuk kasus itu, periksa **PHP error log** di panel Hostinger (bukan hanya `laravel.log`).
