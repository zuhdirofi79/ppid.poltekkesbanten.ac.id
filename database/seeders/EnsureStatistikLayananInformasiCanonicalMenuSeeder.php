<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Support\PublicNavigation;
use App\Support\ServiceSummaryReport;
use Illuminate\Database\Seeder;

class EnsureStatistikLayananInformasiCanonicalMenuSeeder extends Seeder
{
    public function run(): void
    {
        $canonical = ServiceSummaryReport::SLUG;

        MenuItem::query()
            ->where(function ($query) {
                $query->where('label', 'Statistik Layanan Informasi')
                    ->orWhere('route_params->slug', 'statistik-layanan-informasi');
            })
            ->get()
            ->each(function (MenuItem $item) use ($canonical) {
                $params = $item->route_params ?? [];
                if (($params['slug'] ?? null) === $canonical && $item->route_name === 'pages.show') {
                    return;
                }

                $params['slug'] = $canonical;
                $item->update([
                    'route_name' => 'pages.show',
                    'route_params' => $params,
                ]);
            });

        PublicNavigation::forget();
    }
}
