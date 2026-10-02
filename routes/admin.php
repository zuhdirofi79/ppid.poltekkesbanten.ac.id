<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\InformasiBerkalaController;
use App\Http\Controllers\Admin\InformationServiceSummaryController;
use App\Http\Controllers\Admin\MediaAssetController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SaqController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PpidAdminController;
use App\Http\Controllers\Admin\PpidDashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PublicDocumentController;
use App\Http\Controllers\Admin\ServiceLinkController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureUserCanAccessAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('login.store');
    });

    Route::middleware(['auth', EnsureUserCanAccessAdmin::class])->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-documents')->group(function () {
            Route::get('ppid', PpidDashboardController::class)->name('ppid.dashboard');
            Route::get('informasi-publik/berkala', [InformasiBerkalaController::class, 'index'])
                ->name('informasi-publik.berkala');

            Route::middleware('can:manage-ppid-admins')->prefix('ppid/admins')->name('ppid.admins.')->group(function () {
                Route::get('/', [PpidAdminController::class, 'index'])->name('index');
                Route::get('create', [PpidAdminController::class, 'create'])->name('create');
                Route::post('/', [PpidAdminController::class, 'store'])->name('store');
                Route::post('bulk-destroy', [PpidAdminController::class, 'bulkDestroy'])->name('bulk-destroy');
                Route::get('{user}/edit', [PpidAdminController::class, 'edit'])->name('edit');
                Route::put('{user}', [PpidAdminController::class, 'update'])->name('update');
                Route::patch('{user}/disable', [PpidAdminController::class, 'disable'])->name('disable');
                Route::patch('{user}/enable', [PpidAdminController::class, 'enable'])->name('enable');
                Route::delete('{user}', [PpidAdminController::class, 'destroy'])->name('destroy');
            });

            Route::resource('documents', PublicDocumentController::class)
                ->except(['show'])
                ->parameters(['documents' => 'document']);

            Route::delete('documents/{document}/file', [PublicDocumentController::class, 'removeFile'])
                ->name('documents.remove-file');
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-pages')->group(function () {
            Route::resource('pages', PageController::class)->except(['show']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-posts')->group(function () {
            Route::resource('posts', PostController::class)->except(['show']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-announcements')->group(function () {
            Route::resource('announcements', AnnouncementController::class)->except(['show']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-faqs')->group(function () {
            Route::resource('faqs', FaqController::class)->except(['show']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-units')->group(function () {
            Route::resource('units', UnitController::class)->except(['show']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-services')->group(function () {
            Route::resource('services', ServiceLinkController::class)
                ->except(['show'])
                ->parameters(['services' => 'service']);
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-pages')->group(function () {
            Route::get('saq', [SaqController::class, 'index'])->name('saq.index');
            Route::get('saq/{saq}', [SaqController::class, 'show'])->name('saq.show');
            Route::put('saq/{saq}', [SaqController::class, 'update'])->name('saq.update');
            Route::post('saq/{saq}/evidences', [SaqController::class, 'storeEvidence'])->name('saq.evidences.store');
            Route::delete('saq/{saq}/evidences/{evidence}', [SaqController::class, 'destroyEvidence'])->name('saq.evidences.destroy');

            Route::get('media', [MediaAssetController::class, 'index'])->name('media.index');
            Route::get('media/picker', [MediaAssetController::class, 'picker'])->name('media.picker');
            Route::post('media', [MediaAssetController::class, 'store'])->name('media.store');
            Route::put('media/{mediaAsset}', [MediaAssetController::class, 'update'])->name('media.update');
            Route::post('media/{mediaAsset}/replace', [MediaAssetController::class, 'replace'])->name('media.replace');
            Route::delete('media/{mediaAsset}', [MediaAssetController::class, 'destroy'])->name('media.destroy');
            Route::get('media/{mediaAsset}/file', [MediaAssetController::class, 'download'])->name('media.download');

            Route::get('layanan-informasi/ringkasan', [InformationServiceSummaryController::class, 'index'])->name('service-summaries.index');
            Route::post('layanan-informasi/ringkasan', [InformationServiceSummaryController::class, 'store'])->name('service-summaries.store');
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-menus')->group(function () {
            Route::get('menus', [MenuController::class, 'edit'])->name('menus.edit');
            Route::put('menus/source', [MenuController::class, 'updateSource'])->name('menus.source');
            Route::post('menus/items', [MenuController::class, 'storeItem'])->name('menus.items.store');
            Route::put('menus/items/{menuItem}', [MenuController::class, 'updateItem'])->name('menus.items.update');
            Route::post('menus/items/{menuItem}/move', [MenuController::class, 'moveItem'])->name('menus.items.move');
            Route::delete('menus/items/{menuItem}', [MenuController::class, 'destroyItem'])->name('menus.items.destroy');
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-settings')->group(function () {
            Route::get('settings', [SiteSettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings', [SiteSettingController::class, 'update'])->name('settings.update');
        });

        Route::middleware(EnsureUserCanAccessAdmin::class.':manage-users')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
        });
    });
});
