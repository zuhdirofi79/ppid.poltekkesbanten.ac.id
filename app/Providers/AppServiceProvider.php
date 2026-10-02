<?php

namespace App\Providers;

use App\Models\LhkpnDocument;
use App\Models\PublicDocument;
use App\Models\ServiceLink;
use App\Models\User;
use App\Observers\LhkpnDocumentObserver;
use App\Observers\PublicDocumentObserver;
use App\Policies\UserPolicy;
use App\Services\PpidAdminService;
use App\Services\PpidDocumentNotificationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PpidAdminService::class);
        $this->app->singleton(PpidDocumentNotificationService::class);
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale', 'id'));

        Route::bind('document', fn (string $value) => PublicDocument::query()->whereKey($value)->firstOrFail());
        Route::bind('service', fn (string $value) => ServiceLink::query()->whereKey($value)->firstOrFail());

        PublicDocument::observe(PublicDocumentObserver::class);
        LhkpnDocument::observe(LhkpnDocumentObserver::class);

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

        Gate::policy(User::class, UserPolicy::class);

        Gate::define('manage-ppid-admins', function (User $user) {
            return app(PpidAdminService::class)->canManagePpidAdmins($user);
        });

        Gate::define('viewAdminManagement', function (User $user) {
            return app(PpidAdminService::class)->canManagePpidAdmins($user);
        });

        Gate::define('createAdmin', function (User $user) {
            return app(PpidAdminService::class)->canManagePpidAdmins($user);
        });

        View::composer(['components.layout.header', 'admin.layouts.app', 'admin.partials.nav'], function ($view) {
            $view->with('ppidAdminChrome', $this->adminChromePayload());
        });
    }

    /**
     * @return array{
     *     showLogin: bool,
     *     showChrome: bool,
     *     showBell: bool,
     *     isProtected: bool,
     *     missingCount: int,
     *     badgeLabel: string,
     *     missingDocuments: \Illuminate\Support\Collection,
     *     missingLhkpn: \Illuminate\Support\Collection,
     *     lhkpnMissingCount: int,
     *     notificationTotal: int
     * }
     */
    private function adminChromePayload(): array
    {
        $user = auth()->user();
        $admins = app(PpidAdminService::class);
        $notifications = app(PpidDocumentNotificationService::class);

        $canAdmin = $user instanceof User && $user->canAccessAdmin();
        $showBell = $canAdmin && $user->canDo('manage-documents');
        $missingCount = $showBell ? $notifications->missingCount() : 0;
        $missingLhkpn = $showBell ? $notifications->missingLhkpnDocuments() : collect();
        $lhkpnMissingCount = $missingLhkpn->count();
        $notificationTotal = $missingCount + $lhkpnMissingCount;

        return [
            'showLogin' => ! $canAdmin,
            'showChrome' => $canAdmin,
            'showBell' => $showBell,
            'isProtected' => $canAdmin && $admins->canManagePpidAdmins($user),
            'missingCount' => $missingCount,
            'badgeLabel' => $showBell ? $notifications->badgeLabel($missingCount) : '',
            'missingDocuments' => $showBell ? $notifications->missingDocuments(8) : collect(),
            'missingLhkpn' => $missingLhkpn,
            'lhkpnMissingCount' => $lhkpnMissingCount,
            'notificationTotal' => $notificationTotal,
        ];
    }
}
