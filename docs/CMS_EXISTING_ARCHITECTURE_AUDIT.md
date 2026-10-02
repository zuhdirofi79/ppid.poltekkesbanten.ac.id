# Audit arsitektur CMS existing

Tanggal audit: 2026-10-02. Project: `C:\projects\ppid.poltekkesbanten.ac.id`.
Dump `database/u708283844_ppid.sql` dibaca hanya untuk struktur tabel. Dump berisi hash kredensial dan tidak boleh di-commit atau diimpor ke production.

## Auth

- Login admin tetap `GET/POST /admin/login`. Middleware grup: `auth` + `EnsureUserCanAccessAdmin`.
- Role ada di `users.role` (`super-admin`, `admin-ppid`, `editor`, `operator`) lewat enum `App\Enums\UserRole`.
- Kemampuan (`manage-pages`, `manage-documents`, `manage-menus`, dan seterusnya) dicek di enum, lalu di-Gate di `AppServiceProvider`. Tidak ada paket permission terpisah.
- Akun admin existing tidak diubah oleh audit ini. Autentikasi tidak diganti.

## Tabel existing (dump + migrasi)

`users`, `pages`, `menus`, `menu_items`, `public_documents`, `public_information_categories`, `document_categories`, `posts`, `announcements`, `faqs`, `service_links`, `units`, `site_settings`, `activity_logs`, `contacts`, `download_logs`, `visitor_statistics`, plus tabel framework (`cache`, `jobs`, `sessions`).

Tidak ada tabel `media_assets`, `attachments`, `saq_items`, atau `saq_evidences` pada dump.

`pages`: title, slug unik, excerpt, body, status (`draft`/`published`), `published_at`, soft delete, `created_by` / `updated_by`. Meta title/description sudah ada.

`menu_items`: parent, type (`route`/`url`/`page`), sort, target, active. Maksimal kedalaman tidak dipaksa di database.

`public_documents`: file PDF di disk `public`, URL eksternal, atau halaman internal. Katalog Informasi Berkala memakai tabel ini.

## Model dan fitur yang dipakai ulang

| Kebutuhan | Yang sudah ada |
|---|---|
| Halaman | `Page`, `PageController`, `/halaman/{slug}`, `HtmlSanitizer` |
| Dokumen | `PublicDocument`, upload PDF di `PublicDocumentController` |
| Kategori informasi | `PublicInformationCategory` |
| Menu admin | `Menu`, `MenuItem`, `MenuController` |
| Jejak audit | `ActivityLog::record()` |
| Pengaturan | `SiteSetting` + cache `site_settings_map` |
| Navbar publik | `config('ppid.navigation')` di header desktop dan mobile (sumber yang sama) |

## Kesenjangan

- Editor halaman adalah textarea HTML, bukan WYSIWYG. Sanitizer membuang `script`, event handler, dan `src`.
- Tidak ada pustaka media. Upload dokumen publik hanya PDF.
- Navbar publik tidak membaca tabel `menus`. Isi `menu_items` di dump lebih lama daripada `config/ppid.php` (belum memuat menu Diumumkan Berkala dan alur layanan yang baru).
- `MenuSeeder` menghapus seluruh item lalu menyalin config. Menjalankannya di production menimpa susunan menu admin.
- `AdminPpid` belum punya ability `manage-menus` (hanya super-admin).
- Tidak ada pelacak indikator SAQ, data dukung, atau ringkasan layanan per tahun.
- `storage:link` mengarah ke `public/storage`, sementara document root Hostinger adalah `public_html`.

## Risiko

- Menyalakan navbar dari database tanpa salinan menu yang mutakhir akan menghilangkan menu yang sekarang hidup dari config.
- `db:seed` penuh menjalankan `MenuSeeder` (menghapus item) dan seeder akun. Jangan dijalankan di production.
- Seeder halaman/dokumen yang `updateOrCreate` dan menimpa `body` atau `file_path` akan menghapus suntingan admin. Registry SAQ harus `firstOrCreate` berdasarkan kode dan tidak menyentuh berkas.
- Dump SQL tidak boleh masuk git.

## Rencana perubahan minimal

1. Biarkan auth, akun, dan navbar config sebagai default.
2. Tambah tabel baru hanya untuk media, lampiran, SAQ, dan ringkasan layanan.
3. Perketat menu admin (URL, halaman draft, dua tingkat) dan beri sakelar opt-in agar navbar publik memakai database.
4. Tambahkan ability `manage-menus` ke peran `admin-ppid` tanpa mengubah password atau guard.
5. Registry SAQ idempotent. Jangan membuat halaman indikator satu per satu di Blade.
