# Asset Requirements — PPID Poltekkes Kemenkes Banten

## Hero

| Item | Path |
|------|------|
| Hero Gedung Direktorat | `public/images/hero/gedung-direktorat-poltekkes-banten.webp` |
| Sumber lokal asli | `logo dan gedung/Untitled design (4).png` |
| Alt text | Gedung Direktorat Poltekkes Kemenkes Banten di Serang |

**Status FASE 1:** file WebP sudah digenerate dari PNG lokal (lebar max 1920px, ~117 KB).

URL di Blade wajib memakai:

```blade
{{ asset('images/hero/gedung-direktorat-poltekkes-banten.webp') }}
```

Fallback: jika file tidak ada, hero memakai gradient navy–hijau (lihat `resources/views/home.blade.php`).

## Logo

| Item | Path |
|------|------|
| Logo Poltekkes Banten | `public/images/logo/logo-poltekkes-banten.png` |
| Sumber lokal asli | `logo dan gedung/Logo Poltekkes Banten (1).png` |

## Maklumat Pelayanan

| Item | Path |
|------|------|
| WebP (production) | `public/images/ppid/maklumat-pelayanan-poltekkes-banten.webp` |
| PNG (arsip) | `public/images/ppid/maklumat-pelayanan-poltekkes-banten.png` |
| Sumber upload | `public/images/maklumat-pelayanan.png` |
| Alt / caption | Maklumat Pelayanan Poltekkes Kemenkes Banten |
| Resolusi | 1600 × 800 |

**Hostinger:** unggah ke `public_html/images/ppid/maklumat-pelayanan-poltekkes-banten.webp`

URL Blade:

```blade
{{ asset('images/ppid/maklumat-pelayanan-poltekkes-banten.webp') }}
```

| Item | Nilai |
|------|--------|
| Embed URL | `config('ppid.map.embed_url')` — masih `null` |
| External search | OpenStreetMap search Poltekkes Kemenkes Banten Serang |
| Fallback UI | Gradient + tombol “Buka peta eksternal” |

**TODO admin:** isi `embed_url` resmi (OSM/Google) tanpa mengarang koordinat.
