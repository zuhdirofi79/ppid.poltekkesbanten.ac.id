# Peta Upload `public_html` — PPID Poltekkes Kemenkes Banten

**Sistem asset:** Laravel **Vite** (bukan Mix, bukan `asset('css/app.css')` manual).

Layout memakai:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Browser **tidak** membaca `resources/css` / `resources/js`. Yang dilayani adalah hasil `npm run build` di `public/build/`.

---

## SOURCE LOKAL → TARGET HOSTINGER

| SOURCE LOKAL | TARGET HOSTINGER | WAJIB | KETERANGAN |
|--------------|------------------|-------|------------|
| `deployment/hostinger/index.php` | `~/public_html/index.php` | **Ya** | Bukan `public/index.php` lokal. Harus path `../ppid-poltekkesbanten` + `usePublicPath` |
| `deployment/hostinger/.htaccess` | `~/public_html/.htaccess` | **Ya** | Rewrite Laravel; jangan sisa WordPress |
| `public/build/` (seluruh folder) | `~/public_html/build/` | **Ya** | Berisi `manifest.json` + `assets/*.css` + `assets/*.js` + font |
| `public/images/` | `~/public_html/images/` | **Ya** | Hero, logo, maklumat, dll. |
| `public/robots.txt` | `~/public_html/robots.txt` | Disarankan | SEO; ada juga route Laravel `/robots.txt` |
| `public/favicon.ico` | `~/public_html/favicon.ico` | Opsional | Saat ini file lokal 0 byte; favicon praktis dari logo PNG |
| Symlink `storage` | `~/public_html/storage` → `../ppid-poltekkesbanten/storage/app/public` | Jika upload dipakai | Buat lewat `php artisan storage:link` di server |

### Jangan diunggah ke `public_html`

| SOURCE | Alasan |
|--------|--------|
| `public/index.php` (lokal) | Path `../vendor` salah untuk struktur split Hostinger |
| `resources/css`, `resources/js` | Source build saja, bukan runtime browser |
| `vite.config.js`, `package.json`, `node_modules` | Hanya untuk build lokal |
| `app`, `vendor`, `.env`, `storage` (isi privat) | Harus di `~/ppid-poltekkesbanten` |

---

## Yang tetap di `~/ppid-poltekkesbanten`

```text
app/  bootstrap/  config/  database/  resources/  routes/
storage/  vendor/  artisan  composer.json  composer.lock  .env
package.json  vite.config.js   (opsional; tidak dibutuhkan runtime)
```

---

## Hasil build lokal (setelah `npm run build`)

Entry Vite:

| Entry | File hashed (contoh build terkini) |
|-------|-------------------------------------|
| `resources/css/app.css` | `/build/assets/app-BTOns6dL.css` |
| `resources/js/app.js` | `/build/assets/app-DzKMzAPq.js` |
| Fonts CSS (plugin) | `/build/assets/fonts-b8Hbc_Xj.css` + file `.woff`/`.woff2` |

Manifest wajib ada:

```text
public/build/manifest.json  →  ~/public_html/build/manifest.json
```

**Hash nama file berubah setiap build.** Selalu unggah **seluruh** folder `build/` bersama `manifest.json`. Jangan hardcode nama `app-xxxxx.css` di Blade.

---

## URL asset yang benar di production

Dengan `APP_URL=https://ppid.poltekkesbanten.ac.id` dan `ASSET_URL` kosong:

```text
https://ppid.poltekkesbanten.ac.id/build/assets/app-XXXXX.css
https://ppid.poltekkesbanten.ac.id/build/assets/app-XXXXX.js
https://ppid.poltekkesbanten.ac.id/images/...
```

**Salah:**

```text
.../public/build/assets/...
.../ppid-poltekkesbanten/public/build/...
.../wp-content/...
http://localhost/build/...
```

---

## Cara paling aman mengunggah publik

1. Lokal: `npm run build`
2. Pakai package: `scripts\package-hostinger.ps1` → `dist/hostinger/public-html.zip` (sudah berisi `index.php` Hostinger + `build/` + `images/`)
3. Atau salin manual seluruh isi `public/` **kecuali** ganti `index.php` dengan `deployment/hostinger/index.php`

---

## Verifikasi DevTools (Network)

1. Buka `https://ppid.poltekkesbanten.ac.id`
2. F12 → Network → filter CSS / JS
3. Cek request `/build/assets/app-….css`

| Status | Artinya |
|--------|---------|
| **200** + `Content-Type: text/css` | Build terunggah benar |
| **404** | Folder `public_html/build` belum ada / tidak lengkap |
| **200** + `Content-Type: text/html` | File fisik tidak ada; rewrite mengirim ke `index.php` |
| **403** | Permission folder/file |

Cache browser (Ctrl+F5) **bukan** solusi jika status 404/HTML.
