# Panduan Admin PPID

## Akses

- URL: `/admin/login`
- Super Admin utama: email yang dikonfigurasi di `SUPER_ADMIN_EMAIL` (default seeder: `qowiyrofi@poltekkesbanten.ac.id`)
- Password: nilai `SUPER_ADMIN_PASSWORD` di `.env`, atau password yang di-set saat `php artisan db:seed`

Setelah mengubah `.env`, jalankan ulang:

```bash
php artisan db:seed --class=AdminUserSeeder
```

## Role

| Role | Ringkas |
|------|---------|
| super-admin | Semua fitur termasuk pengguna |
| admin-ppid | Konten + settings (tanpa kelola user) |
| editor | Halaman, berita, pengumuman, FAQ |
| operator | Dokumen & layanan/formulir |

Otorisasi dicek di middleware server-side.

## Modul

- **Halaman** — profil/konten statis (`/halaman/{slug}` publik jika `published`)
- **Berita / Pengumuman** — tampil di beranda jika published
- **Dokumen** — unggah lokal atau URL/Google Drive; filter publik di `/dokumen-publik`
- **Layanan** — isi `external_url` untuk Google Form
- **Unit** — PPID pelaksana
- **Menu / Settings / Pengguna** — sesuai role

## Storage upload

```bash
php artisan storage:link
```
