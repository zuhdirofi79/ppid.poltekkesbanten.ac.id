# Security Notes — PPID Poltekkes Kemenkes Banten

## Sudah diterapkan

- CSRF pada form web
- Password hashing bcrypt Laravel
- Rate limit login admin & unduhan/pencarian publik
- Middleware otorisasi role (server-side)
- Validasi upload (MIME/ukuran, tolak executable)
- Path traversal guard pada unduhan
- Redirect eksternal hanya `http`/`https`
- Security headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, CSP realistis
- Session regenerate pada login
- Activity log login/logout & CRUD penting
- `.env` tidak di-commit; gunakan `.env.example`

## Production wajib

```env
APP_DEBUG=false
APP_ENV=production
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

- Ganti password super-admin setelah deploy bila perlu
- Jangan meletakkan `.env` di `public_html`
- Jalankan `php artisan config:cache` setelah set env production
