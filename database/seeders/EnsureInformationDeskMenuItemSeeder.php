<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\InformationDesk;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsureInformationDeskMenuItemSeeder extends Seeder
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
            ->where('label', 'Informasi')
            ->first();

        if (! $parent) {
            return;
        }

        $label = InformationDesk::TITLE;
        $slug = InformationDesk::SLUG;

        $existing = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where(function ($query) use ($label, $slug) {
                $query->where('label', $label)
                    ->orWhere('route_params->slug', $slug);
            })
            ->first();

        if ($existing && (int) $existing->parent_id === (int) $parent->id) {
            PublicNavigation::forget();

            return;
        }

        $statistik = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', 'Statistik Layanan Informasi')
            ->first();

        $sortOrder = $statistik
            ? $statistik->sort_order + 1
            : ((int) MenuItem::query()
                ->where('menu_id', $menu->id)
                ->where('parent_id', $parent->id)
                ->max('sort_order')) + 1;

        MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('sort_order', '>=', $sortOrder)
            ->when($existing, fn ($query) => $query->whereKeyNot($existing->id))
            ->increment('sort_order');

        $attributes = [
            'parent_id' => $parent->id,
            'label' => $label,
            'type' => 'route',
            'route_name' => 'pages.show',
            'route_params' => ['slug' => $slug],
            'url' => null,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ];

        if ($existing) {
            $existing->update($attributes);
        } else {
            MenuItem::query()->create([
                'menu_id' => $menu->id,
                ...$attributes,
            ]);
        }

        PublicNavigation::forget();
    }
}
