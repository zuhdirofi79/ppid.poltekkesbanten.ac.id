<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\DaftarInformasiPublik;
use App\Support\PublicNavigation;
use App\Support\RapatKoordinasiPpid;
use Illuminate\Database\Seeder;

class EnsureRapatKoordinasiPpidMenuItemSeeder extends Seeder
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

        $label = RapatKoordinasiPpid::TITLE;
        $slug = RapatKoordinasiPpid::SLUG;

        $existing = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', $slug)
            ->first();

        $dip = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', DaftarInformasiPublik::SLUG)
            ->first();

        if ($existing && (int) $existing->parent_id === (int) $parent->id) {
            $alreadyAfterDip = $dip === null
                || (
                    (int) $existing->sort_order > (int) $dip->sort_order
                    && MenuItem::query()
                        ->where('menu_id', $menu->id)
                        ->where('parent_id', $parent->id)
                        ->where('sort_order', '>', $dip->sort_order)
                        ->where('sort_order', '<', $existing->sort_order)
                        ->doesntExist()
                );

            if ($alreadyAfterDip && ($existing->route_params['slug'] ?? null) === $slug) {
                PublicNavigation::forget();

                return;
            }
        }

        $sortOrder = $dip
            ? $dip->sort_order + 1
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
