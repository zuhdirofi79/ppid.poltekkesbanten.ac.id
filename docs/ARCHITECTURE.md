# Architecture — PPID Poltekkes Kemenkes Banten

## Stack

- Laravel 13 (PHP 8.3+)
- Blade + Vite 8 + Tailwind CSS 4 + Alpine.js
- MySQL/MariaDB (production) / SQLite (local optional)
- Auth session native + role enum (`super-admin`, `admin-ppid`, `editor`, `operator`)

## Lapisan

```text
HTTP
 ├─ routes/web.php          Publik
 ├─ routes/admin.php        Panel /admin
 ├─ Middleware               SecurityHeaders, EnsureUserCanAccessAdmin
 ├─ Controllers             Publik + Admin\*
 ├─ Services                HomeContentService, UniqueSlug
 ├─ Models / Enums          Eloquent + UserRole
 └─ Blade views             layouts, components, admin, pages
```

## Modul data utama

pages, posts, announcements, public_documents, faqs, units, service_links, menus, site_settings, activity_logs, download_logs, visitor_statistics

## Deployment split

Local `public/` ↔ Hostinger `public_html/`  
Local root ↔ Hostinger `ppid-poltekkesbanten/`

Lihat `docs/DEPLOYMENT_HOSTINGER.md`.
