<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaFileController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\ServiceSummaryPdfController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/media/{uuid}', [MediaFileController::class, 'show'])
    ->whereUuid('uuid')
    ->name('media.show');

Route::get('/cari', [SearchController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('search');
Route::get('/pencarian', [SearchController::class, 'index'])
    ->middleware('throttle:60,1')
    ->name('search.index');
Route::get('/pencarian/saran', [SearchController::class, 'suggestions'])
    ->middleware('throttle:60,1')
    ->name('search.suggestions');

Route::get('/profil/maklumat-pelayanan', [ProfileController::class, 'maklumatPelayanan'])
    ->name('profil.maklumat-pelayanan');
Route::get('/profil/maklumat-pelayanan/unduh', [ProfileController::class, 'downloadMaklumatPelayanan'])
    ->middleware('throttle:30,1')
    ->name('profil.maklumat-pelayanan.download');

Route::get('/kontak', function () {
    return view('pages.contact', [
        'title' => 'Kontak',
        'description' => 'Hubungi PPID Poltekkes Kemenkes Banten.',
    ]);
})->name('contact');

Route::redirect('/halaman/informasi-berkala', '/informasi-publik/berkala', 301);

Route::get('/halaman/ringkasan-laporan-layanan-informasi/pdf', ServiceSummaryPdfController::class)
    ->name('pages.service-summary.pdf');

Route::get('/halaman/{slug}', [PublicPageController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('pages.show');

Route::get('/informasi-publik', [InformationController::class, 'index'])->name('information.index');
Route::get('/informasi-publik/{slug}', [InformationController::class, 'category'])->name('information.category');

Route::get('/dokumen-publik', [DocumentController::class, 'index'])->name('documents.index');
Route::get('/dokumen-publik/{slug}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/dokumen-publik/{slug}/unduh', [DocumentController::class, 'download'])
    ->middleware('throttle:30,1')
    ->name('documents.download');

Route::get('/layanan/{slug}', [ServiceController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('services.show');

Route::get('/berita', [PostController::class, 'index'])->name('posts.index');
Route::get('/berita/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

Route::get('/faq', [FaqController::class, 'index'])->name('faqs.index');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function () {
    $sitemap = url('/sitemap.xml');
    $body = <<<TXT
User-agent: *
Allow: /
Disallow: /admin
Disallow: /admin/

Sitemap: {$sitemap}
TXT;

    return response($body, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
})->name('robots');
