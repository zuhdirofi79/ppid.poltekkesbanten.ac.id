# Project Audit — PPID Poltekkes Kemenkes Banten

**Tanggal audit:** 21 Juli 2026  
**Path:** `C:\projects\ppid.poltekkesbanten.ac.id`  
**Status:** Greenfield (belum ada aplikasi Laravel)

---

## 1. Versi stack (lingkungan lokal)

| Komponen | Versi terdeteksi |
|----------|------------------|
| PHP CLI | 8.3.30 (Laragon, ZTS) |
| Composer | 2.9.7 |
| Node.js | v24.14.1 |
| npm | 11.11.0 |
| Laravel (project) | **13.20.0** (skeleton `laravel/laravel` v13.8.0) — PHP ^8.3 |
| Frontend | Vite 8, Tailwind CSS 4, Alpine.js, Blade |
| Database target | MySQL/MariaDB (Hostinger); lokal default SQLite |

---

## 2. Struktur project saat audit

```text
C:\projects\ppid.poltekkesbanten.ac.id\
├── app, bootstrap, config, database, public, resources, routes, storage, tests, vendor
├── docs\
├── logo dan gedung\          (sumber aset brand)
├── artisan, composer.json, package.json, vite.config.js
└── public/images/{hero,logo}
```

- Setelah audit: Laravel 13 di-scaffold di folder yang sama (greenfield).
- Tidak ada WordPress / Elementor / CMS pihak ketiga.
- Identitas visual: logo + Gedung Direktorat Serang.

---

## 3. Package yang digunakan

Belum ada. Setelah scaffold Laravel, baseline yang diharapkan:

- `laravel/framework` (existing major, tanpa upgrade major otomatis setelah terpasang)
- Vite + Blade
- Tailwind CSS (default Laravel Breeze/skeleton modern) atau setara
- Alpine.js untuk interaksi ringan
- Autentikasi native Laravel (Breeze Blade) bila diperlukan

---

## 4. Temuan

1. **Greenfield murni** — seluruh aplikasi PPID harus dibangun dari nol di folder ini.
2. **Aset brand tersedia** — logo Poltekkes Banten dan foto Gedung Direktorat Serang sudah ada di `logo dan gedung/`.
3. **Deployment target** — pemisahan `ppid-poltekkesbanten` (app root) dan `public_html` (isi `public/`).
4. **Tidak ada fitur existing** yang perlu dilestarikan selain aset gambar.
5. **Bahasa UI** — wajib Bahasa Indonesia; identitas khusus Poltekkes Kemenkes Banten (bukan Semarang).

---

## 5. Risiko

| Risiko | Mitigasi |
|--------|----------|
| Shared hosting tanpa symlink | Dokumentasi `storage:link` + fallback disk/public path |
| Path `index.php` Hostinger salah | File khusus `deployment/hostinger/index.php`; lokal tetap standar |
| Conteng/placeholder kosong terlihat publik | Label CONTOH + docs CONTENT_REQUIREMENTS |
| Gambar hero besar (PNG ~3.3 MB) | Konversi WebP + lazy/preload bijak |
| Role/authorization hanya di UI | Middleware + policy server-side |
| Credential di repo | `.env` di `.gitignore`; hanya `.env.example` |

---

## 6. Gap terhadap requirement

| Area | Status |
|------|--------|
| Layout global, header 2 tingkat, footer | **Selesai FASE 1** |
| Homepage + hero + kategori + layanan | Hero + kategori partial; layanan/unit/peta → FASE 2 |
| Database/migrations/models PPID | Belum ada |
| Panel admin `/admin` + roles | Belum ada |
| Berita, pengumuman, dokumen, FAQ | Routing placeholder saja |
| Aksesibilitas floating | **Selesai FASE 1** (dasar) |
| SEO, security headers, tests | Meta dasar + HomePageTest; sisanya FASE 5 |
| Deployment Hostinger package | Belum ada (FASE 6) |
| Dokumentasi lengkap | Audit, aset, konten, changelog FASE 1 |

---

## 7. Rencana implementasi (bertahap)

### FASE 1 (langsung setelah audit)
- Scaffold Laravel di folder project
- Design tokens (CSS variables navy/green/gold)
- Layout Blade global
- Header (top bar + nav + dropdown)
- Footer
- Asset logo & hero lokal
- `docs/PROJECT_AUDIT.md` (file ini)

### FASE 2–6
Sesuai prompt utama: homepage → database/admin → CRUD → SEO/security/tests → Hostinger deploy + dokumentasi.

---

## 8. Command aman yang akan dijalankan

```bash
composer create-project laravel/laravel ...   # scaffold (bukan di folder lain permanen)
composer require ...                          # package kompatibel saja
php artisan migrate                           # incremental, BUKAN migrate:fresh
php artisan db:seed                           # hanya lokal/dev
npm install && npm run build
php artisan test
php artisan route:list
```

**Tidak dijalankan tanpa izin:** `migrate:fresh`, `db:wipe`, hapus storage/database production.

---

## 9. Informasi blocking

Tidak ada. Scaffold Laravel dapat dilanjutkan di folder project yang sama.
