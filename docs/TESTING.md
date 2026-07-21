# Testing

```bash
php artisan test
```

## Cakupan utama

| Suite | Isi |
|-------|-----|
| HomePageTest | Beranda & route publik dasar |
| AdminAuthTest | Login, role, middleware |
| AdminCrudTest | CRUD berita/dokumen, otorisasi, upload reject |
| Phase5PublicTest | Search, filter dokumen, sitemap, headers, super-admin login |
| SiteSettingTest | Cache settings |

## CI lokal cepat

```bash
php artisan config:clear
php artisan test
npm run build
```

PHPUnit memakai SQLite in-memory (`phpunit.xml`).
