## Maklumat Pelayanan — 21 Juli 2026

### Dilakukan

- Asset WebP/PNG di `public/images/ppid/`
- Halaman `/profil/maklumat-pelayanan` + unduh aman
- Menu Profil → Maklumat Pelayanan
- Section beranda + lightbox Alpine
- Test `MaklumatPelayananTest`

---

### Dilakukan

- `deployment/hostinger/index.php` (path `../ppid-poltekkesbanten` + `usePublicPath`)
- `deployment/hostinger/.htaccess`
- `deployment/hostinger/DEPLOYMENT.md` + `production-checklist.md`
- `scripts/build-production.bat`
- `scripts/package-hostinger.ps1` → `dist/hostinger/{app-root,public-html}.zip`
- Dokumentasi: DEPLOYMENT_HOSTINGER, ARCHITECTURE, LOCAL_DEVELOPMENT, TESTING
- README diperbarui untuk deploy Hostinger
- `public/index.php` lokal **tidak** diubah

### Command package

```bat
scripts\build-production.bat
powershell -ExecutionPolicy Bypass -File scripts\package-hostinger.ps1
```

---

### Dilakukan

- Pencarian publik `/cari` (throttle)
- Dokumen publik: filter q/kategori/jenis/tahun/sort + unduh + counter
- Berita, pengumuman, FAQ, informasi publik (listing/detail)
- SEO: canonical/OG, Organization schema, sitemap.xml, robots.txt, 404/500
- Security headers middleware (CSP realistis, XFO, nosniff, Referrer-Policy, Permissions-Policy)
- Super-admin seeder: `qowiyrofi@poltekkesbanten.ac.id` (password via env/seeder)

### Command

```bash
php artisan db:seed --class=AdminUserSeeder
php artisan test
```

---

### Dilakukan

- CRUD admin: halaman, berita, pengumuman, dokumen (upload + URL), FAQ, unit, layanan, menu, settings, pengguna
- Otorisasi per kemampuan via middleware (bukan hanya UI)
- Validasi upload dokumen (MIME/ukuran), slug unik, activity log
- Sidebar admin dinamis sesuai role
- Halaman publik `/halaman/{slug}` membaca konten published dari DB

### Command

```bash
php artisan test
php artisan serve
# buka /admin/login
```

---

### Dilakukan

- Migrations modul PPID (users.role, pages, menus, posts, announcements, documents, services, faqs, units, contacts, settings, logs)
- Models + `UserRole` enum + Gates
- Middleware `EnsureUserCanAccessAdmin` (server-side)
- Auth admin `/admin/login` + logout + dashboard
- Rate limit login
- Seeders development (label CONTOH) — tidak dijalankan otomatis di production
- `HomeContentService` membaca DB dengan fallback config
- Activity log pada login/logout

### Login development (setelah `db:seed`)

| Email | Password | Role |
|-------|----------|------|
| admin@ppid.local | password | super-admin |
| editor@ppid.local | password | editor |
| operator@ppid.local | password | operator |

**Ganti password segera di lingkungan non-lokal.**

### Command

```bash
php artisan migrate
php artisan db:seed
php artisan test
```

### Belum (FASE 4)

- CRUD penuh halaman, berita, pengumuman, dokumen, FAQ, unit, menu, settings

---

### Dilakukan

- Beranda lengkap: hero, kategori, layanan, shortcut, berita, pengumuman, statistik, unit PPID, peta/kontak
- `HomeController` dengan data config + shell CONTOH untuk berita/pengumuman
- Unit khusus Banten: Direktorat, Keperawatan, Kebidanan, TLM (tanpa jurusan Semarang)
- Halaman kontak & detail layanan
- Aksesibilitas: sembunyikan sesi, Escape, localStorage, target sentuh lebih besar
- Validasi redirect tautan eksternal layanan (hanya http/https)

### File utama

- `app/Http/Controllers/HomeController.php`
- `config/ppid.php` (services, units, statistics, map, categories)
- `resources/views/components/home/*`
- `resources/views/home.blade.php`
- `resources/views/pages/contact.blade.php`
- `resources/views/pages/service.blade.php`

### Command

```bash
npm run build
php artisan test
php artisan serve
```

### TODO / risiko

- Kontak & embed peta masih placeholder
- `external_url` formulir masih `null` — isi setelah ada Google Form resmi
- Berita/pengumuman contoh diganti query DB di FASE 3

---

## FASE 1 — 21 Juli 2026

### Dilakukan

- Audit greenfield → `docs/PROJECT_AUDIT.md`
- Scaffold **Laravel 13.20** + Vite 8 + Tailwind 4 + Alpine.js di folder project
- Design tokens navy / hijau Kemenkes / gold
- Layout global Blade (`layouts/app.blade.php`)
- Header dua tingkat + dropdown desktop/keyboard + accordion mobile
- Footer 4 kolom dengan placeholder kontak
- Floating aksesibilitas + back-to-top
- Hero Gedung Direktorat (WebP lokal) + 4 kartu kategori
- Routing placeholder menu utama
- Aset logo & hero di `public/images/`
- Dokumentasi aset & konten

### File utama

- `config/ppid.php`
- `resources/views/layouts/app.blade.php`
- `resources/views/components/layout/*`
- `resources/views/home.blade.php`
- `resources/css/app.css`
- `resources/js/app.js`
- `routes/web.php`
- `public/images/hero/gedung-direktorat-poltekkes-banten.webp`
- `public/images/logo/logo-poltekkes-banten.png`

### Command

```bash
npm install
npm run build
php artisan test
php artisan serve
```

### Belum (fase berikutnya)

- Homepage modul penuh (berita, unit, peta, statistik) → **selesai FASE 2**
- Database/migrations modul PPID
- Panel admin + roles
- Deployment Hostinger package
