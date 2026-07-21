# Bersihkan Sisa WordPress di `public_html`

**Peringatan:** buat backup `public_html` (zip/download) sebelum menghapus apa pun.

Project Laravel PPID **tidak** mengandung referensi `wp-content`, `wp-includes`, WordPress, atau Elementor di source. Jika CSS tidak muncul setelah migrasi, penyebab umum adalah:

1. folder `build/` belum diunggah ke `public_html`; dan/atau
2. file/folder WordPress lama masih bertabrakan dengan `index.php` / `.htaccess`.

Dokumen ini **bukan** script penghapusan otomatis.

---

## Checklist hapus (jika masih ada di `~/public_html`)

| Item | Aksi |
|------|------|
| `wp-admin/` | Hapus |
| `wp-content/` | Hapus |
| `wp-includes/` | Hapus |
| `wp-config.php` | Hapus |
| `wp-login.php` | Hapus |
| `wp-cron.php` | Hapus |
| `wp-*.php` lain | Hapus |
| `xmlrpc.php` | Hapus |
| `license.txt` | Hapus |
| `readme.html` | Hapus |
| `index.php` WordPress lama | **Ganti** dengan `deployment/hostinger/index.php` |
| `.htaccess` berisi `# BEGIN WordPress` | **Ganti** dengan `deployment/hostinger/.htaccess` |
| `style.css` tema WP di root | Hapus |
| plugin/theme folder sisa | Hapus |

---

## Setelah membersihkan

Pastikan `public_html` berisi minimal:

```text
index.php          ← Hostinger Laravel
.htaccess          ← Laravel rewrite
build/
  manifest.json
  assets/
images/
robots.txt         (opsional)
```

Lalu di SSH app root (`~/ppid-poltekkesbanten`):

```bash
php artisan optimize:clear
php artisan view:clear
php artisan config:clear
```

Pastikan `.env` production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ppid.poltekkesbanten.ac.id
# ASSET_URL=   (kosongkan)
```

Hard refresh browser: Ctrl+F5, lalu cek Network untuk `/build/assets/*.css`.

---

## Jangan

- Jangan menghapus `~/ppid-poltekkesbanten` saat membersihkan WP
- Jangan menjalankan script mass-delete tanpa backup
- Jangan mengandalkan “clear cache” saja jika CSS 404
