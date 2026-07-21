@echo off
REM Build production assets for PPID Poltekkes Kemenkes Banten
REM Safe commands only — does not delete production data.

setlocal
cd /d "%~dp0.."

echo === Composer (no-dev, optimized) ===
call composer install --no-dev --optimize-autoloader --no-interaction
if errorlevel 1 goto :error

echo === NPM install ===
call npm ci
if errorlevel 1 (
  echo npm ci failed, falling back to npm install...
  call npm install
  if errorlevel 1 goto :error
)

echo === Vite production build ===
call npm run build
if errorlevel 1 goto :error

echo === Clear local caches (safe) ===
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Build selesai. Lanjut package dengan:
echo   powershell -ExecutionPolicy Bypass -File scripts\package-hostinger.ps1
exit /b 0

:error
echo.
echo Build GAGAL.
exit /b 1
