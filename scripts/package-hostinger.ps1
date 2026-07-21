<#
.SYNOPSIS
  Membuat dua paket deploy Hostinger: app-root dan public-html.

.DESCRIPTION
  - Tidak menyertakan .env, node_modules, .git, tests (opsional)
  - Tidak menghapus data production
  - Menyalin index.php & .htaccess khusus Hostinger ke paket public-html
  - Output: dist/hostinger/app-root.zip dan public-html.zip

.NOTES
  Jalankan dari root project, atau lewat path relatif script ini.
  Disarankan jalankan scripts/build-production.bat terlebih dahulu.
#>

[CmdletBinding()]
param(
    [switch]$SkipBuild,
    [switch]$IncludeTests
)

$ErrorActionPreference = 'Stop'
$Root = Resolve-Path (Join-Path $PSScriptRoot '..')
Set-Location $Root

$Dist = Join-Path $Root 'dist\hostinger'
$Stage = Join-Path $Dist '_stage'
$AppStage = Join-Path $Stage 'app-root'
$PublicStage = Join-Path $Stage 'public-html'

Write-Host "==> Root: $Root"

if (-not $SkipBuild) {
    Write-Host '==> Running production build...'
    & (Join-Path $Root 'scripts\build-production.bat')
    if ($LASTEXITCODE -ne 0) { throw 'build-production.bat failed' }
}

if (-not (Test-Path (Join-Path $Root 'public\build'))) {
    throw 'public/build tidak ditemukan. Jalankan npm run build dulu.'
}

if (-not (Test-Path (Join-Path $Root 'vendor'))) {
    throw 'vendor/ tidak ditemukan. Jalankan composer install --no-dev dulu.'
}

Write-Host '==> Preparing staging folders...'
if (Test-Path $Stage) { Remove-Item $Stage -Recurse -Force }
New-Item -ItemType Directory -Force -Path $AppStage, $PublicStage | Out-Null

# --- APP ROOT ---
$appInclude = @(
    'app', 'bootstrap', 'config', 'database', 'resources', 'routes', 'storage',
    'vendor', 'artisan', 'composer.json', 'composer.lock'
)

foreach ($name in $appInclude) {
    $src = Join-Path $Root $name
    if (-not (Test-Path $src)) {
        Write-Warning "Skip missing: $name"
        continue
    }
    Write-Host "  copy app: $name"
    Copy-Item -LiteralPath $src -Destination (Join-Path $AppStage $name) -Recurse -Force
}

# Bersihkan storage runtime yang tidak perlu di package (pertahankan struktur)
$storageKeep = @(
    'storage\app\.gitignore',
    'storage\app\public\.gitignore',
    'storage\framework\.gitignore',
    'storage\framework\cache\.gitignore',
    'storage\framework\cache\data\.gitignore',
    'storage\framework\sessions\.gitignore',
    'storage\framework\testing\.gitignore',
    'storage\framework\views\.gitignore',
    'storage\logs\.gitignore'
)

# Hapus isi cache/log/session di staging (aman)
$cleanDirs = @(
    'storage\framework\cache\data',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\logs',
    'storage\app\public'
)
foreach ($rel in $cleanDirs) {
    $path = Join-Path $AppStage $rel
    if (Test-Path $path) {
        Get-ChildItem -LiteralPath $path -Force -ErrorAction SilentlyContinue |
            Where-Object { $_.Name -ne '.gitignore' } |
            Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
    }
}

# Pastikan .gitignore storage tetap ada
foreach ($rel in $storageKeep) {
    $src = Join-Path $Root $rel
    $dst = Join-Path $AppStage $rel
    if (Test-Path $src) {
        $dstDir = Split-Path $dst -Parent
        if (-not (Test-Path $dstDir)) { New-Item -ItemType Directory -Force -Path $dstDir | Out-Null }
        Copy-Item -LiteralPath $src -Destination $dst -Force
    }
}

# Jangan sertakan .env
Get-ChildItem -LiteralPath $AppStage -Filter '.env*' -Force -ErrorAction SilentlyContinue | Remove-Item -Force

# Sertakan .env.example sebagai referensi
Copy-Item (Join-Path $Root '.env.example') (Join-Path $AppStage '.env.example') -Force

# Opsional: tests
if ($IncludeTests -and (Test-Path (Join-Path $Root 'tests'))) {
    Copy-Item (Join-Path $Root 'tests') (Join-Path $AppStage 'tests') -Recurse -Force
    Copy-Item (Join-Path $Root 'phpunit.xml') (Join-Path $AppStage 'phpunit.xml') -Force -ErrorAction SilentlyContinue
}

# --- PUBLIC HTML ---
Write-Host '==> Preparing public-html package...'
Get-ChildItem -LiteralPath (Join-Path $Root 'public') -Force | ForEach-Object {
    if ($_.Name -in @('index.php', 'hot', 'storage')) {
        return
    }
    Copy-Item -LiteralPath $_.FullName -Destination (Join-Path $PublicStage $_.Name) -Recurse -Force
}

# index.php & .htaccess Hostinger
Copy-Item (Join-Path $Root 'deployment\hostinger\index.php') (Join-Path $PublicStage 'index.php') -Force
Copy-Item (Join-Path $Root 'deployment\hostinger\.htaccess') (Join-Path $PublicStage '.htaccess') -Force

# README singkat di dalam zip
@"
PPID Poltekkes Kemenkes Banten — PUBLIC-HTML package
1. Ekstrak seluruh isi ke folder public_html Hostinger.
2. Pastikan index.php adalah versi Hostinger (mengarah ke ../ppid-poltekkesbanten).
3. Lihat deployment/hostinger/DEPLOYMENT.md
"@ | Set-Content (Join-Path $PublicStage 'README-UPLOAD.txt') -Encoding UTF8

@"
PPID Poltekkes Kemenkes Banten — APP-ROOT package
1. Ekstrak ke folder ~/ppid-poltekkesbanten (di luar public_html).
2. Buat file .env di folder ini (jangan commit/upload .env dari lokal sembarangan).
3. Jalankan: php artisan key:generate && php artisan migrate --force
4. Lihat deployment/hostinger/DEPLOYMENT.md
"@ | Set-Content (Join-Path $AppStage 'README-UPLOAD.txt') -Encoding UTF8

# --- ZIP ---
Write-Host '==> Creating zip archives...'
New-Item -ItemType Directory -Force -Path $Dist | Out-Null
$appZip = Join-Path $Dist 'app-root.zip'
$publicZip = Join-Path $Dist 'public-html.zip'

if (Test-Path $appZip) { Remove-Item $appZip -Force }
if (Test-Path $publicZip) { Remove-Item $publicZip -Force }

Compress-Archive -Path (Join-Path $AppStage '*') -DestinationPath $appZip -Force
Compress-Archive -Path (Join-Path $PublicStage '*') -DestinationPath $publicZip -Force

Write-Host ''
Write-Host 'Selesai.'
Write-Host "  APP-ROOT   : $appZip"
Write-Host "  PUBLIC-HTML: $publicZip"
Write-Host ''
Write-Host 'Staging folder (boleh dihapus):' $Stage
