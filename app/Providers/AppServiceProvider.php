<?php

namespace App\Providers;

use App\Models\PublicDocument;
use App\Models\ServiceLink;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale', 'id'));

        Route::bind('document', fn (string $value) => PublicDocument::query()->whereKey($value)->firstOrFail());
        Route::bind('service', fn (string $value) => ServiceLink::query()->whereKey($value)->firstOrFail());

        $abilities = [
            'access-admin',
            'manage-users',
            'manage-settings',
            'manage-menus',
            'manage-pages',
            'manage-posts',
            'manage-announcements',
            'manage-documents',
            'manage-categories',
            'manage-services',
            'manage-faqs',
            'manage-units',
            'manage-contacts',
            'view-activity-logs',
        ];

        foreach ($abilities as $ability) {
            Gate::define($ability, function (User $user) use ($ability) {
                return $user->canDo($ability);
            });
        }

        Gate::define('role', function (User $user, string ...$roles) {
            return $user->hasRole(...$roles);
        });
    }
}
