# Deploy CMS PPID ke Hostinger

Jangan menjalankan `migrate:fresh`, `db:wipe`, atau `php artisan db:seed` tanpa `--class`. `DatabaseSeeder` ikut menjalankan `MenuSeeder`, yang menghapus item menu lalu menyalin ulang dari config.

## Sebelum migrasi

1. Cadangkan database dari panel Hostinger. Catat waktu cadangan.
2. Cadangkan folder aplikasi `~/domains/ppid.poltekkesbanten.ac.id/ppid-poltekkesbanten`.
3. Cadangkan `~/domains/ppid.poltekkesbanten.ac.id/public_html`.
4. Jangan impor `database/u708283844_ppid.sql` ke production. File itu snapshot lama dan berisi hash kredensial.

## Peta unggah

| Lokal | Server |
|---|---|
| Kode aplikasi (tanpa `vendor` bila sudah ada di server, tanpa `.env`) | `~/domains/ppid.poltekkesbanten.ac.id/ppid-poltekkesbanten` |
| `public/build/` | `~/domains/ppid.poltekkesbanten.ac.id/public_html/build/` |
| `public/images/` bila ada berkas baru | `~/domains/ppid.poltekkesbanten.ac.id/public_html/images/` |

Jangan mengunggah `resources/` ke `public_html`.

Navbar publik tetap membaca `config/ppid.php` sampai admin memilih sumber database di Menu. Jangan mengganti sumber itu sebelum menu database disamakan dengan menu yang sedang tampil.

## Perintah di server

```bash
cd ~/domains/ppid.poltekkesbanten.ac.id/ppid-poltekkesbanten
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed --class=SaqItemSeeder --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan ppid:saq-audit --year=2026
```

`storage:link` membuat `public/storage` di dalam aplikasi. Document root production adalah `public_html`, jadi pastikan tautan yang dipakai situs adalah:

`public_html/storage` → `ppid-poltekkesbanten/storage/app/public`

Jika tautan itu sudah ada dan unduhan PDF lama masih terbuka, jangan dihapus. Berkas publik baru lewat `/media/{uuid}` dilayani controller, bukan dengan membuka path privat.

URL media internal tidak dibuat. Hanya admin yang mengunduhnya lewat `/admin/media/{id}/file`.

Build frontend tidak wajib untuk rilis ini bila stylesheet yang sudah terpasang tetap dipakai. Jika tampilan admin baru kehilangan jarak, jalankan `npm run build` di mesin lokal, lalu unggah `public/build/` ke `public_html/build/`.

## Rollback

1. Kembalikan cadangan folder aplikasi dan `public_html` bila unggahan kode bermasalah.
2. Migrasi ini hanya menambah tabel `media_assets`, `attachments`, `saq_items`, `saq_evidences`, dan `information_service_summaries`. Rollback skema:

```bash
php artisan migrate:rollback --step=1 --force
```

Itu menghapus tabel baru, bukan tabel `pages`, `users`, atau `public_documents`.
3. Kembalikan cadangan database hanya jika migrasi atau seeder merusak data lama. `SaqItemSeeder` tidak mengosongkan halaman atau berkas.
4. Setelah rollback, jalankan `php artisan optimize:clear`.
