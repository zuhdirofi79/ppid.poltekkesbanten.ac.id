<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsureTataCaraPengaduanMenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::query()->where('location', 'primary')->where('is_active', true)->first();
        if (! $menu) {
            return;
        }

        $parent = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->where('label', 'Diumumkan Berkala')
            ->first();

        if (! $parent) {
            return;
        }

        $label = 'Tata Cara Pengaduan Penyalahgunaan Wewenang';
        $slug = 'tata-cara-pengaduan-penyalahgunaan-wewenang';

        $already = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where(function ($query) use ($label, $slug) {
                $query->where('label', $label)
                    ->orWhere('route_params->slug', $slug);
            })
            ->exists();

        if ($already) {
            PublicNavigation::forget();

            return;
        }

        $kalender = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', 'Kalender Akademik')
            ->first();

        $sortOrder = $kalender
            ? $kalender->sort_order + 1
            : ((int) MenuItem::query()
                ->where('menu_id', $menu->id)
                ->where('parent_id', $parent->id)
                ->max('sort_order')) + 1;

        MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('sort_order', '>=', $sortOrder)
            ->increment('sort_order');

        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => $label,
            'type' => 'route',
            'route_name' => 'pages.show',
            'route_params' => ['slug' => $slug],
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        PublicNavigation::forget();
    }
}
