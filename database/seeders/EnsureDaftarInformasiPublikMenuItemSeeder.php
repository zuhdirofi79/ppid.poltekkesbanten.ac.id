<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\DaftarInformasiPublik;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsureDaftarInformasiPublikMenuItemSeeder extends Seeder
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

        $label = DaftarInformasiPublik::TITLE;
        $slug = DaftarInformasiPublik::SLUG;

        $existing = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', $slug)
            ->first();

        $inventory = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', EnsureInventarisBmnMenuItemSeeder::LABEL)
            ->first();

        if ($existing && (int) $existing->parent_id === (int) $parent->id) {
            $alreadyAfterInventory = $inventory === null
                || (
                    (int) $existing->sort_order > (int) $inventory->sort_order
                    && MenuItem::query()
                        ->where('menu_id', $menu->id)
                        ->where('parent_id', $parent->id)
                        ->where('sort_order', '>', $inventory->sort_order)
                        ->where('sort_order', '<', $existing->sort_order)
                        ->doesntExist()
                );

            if ($alreadyAfterInventory && $existing->route_name === 'pages.show' && ($existing->route_params['slug'] ?? null) === $slug) {
                PublicNavigation::forget();

                return;
            }
        }

        $sortOrder = $inventory
            ? $inventory->sort_order + 1
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
