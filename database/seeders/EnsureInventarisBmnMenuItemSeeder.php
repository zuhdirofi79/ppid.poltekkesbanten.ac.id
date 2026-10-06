<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsureInventarisBmnMenuItemSeeder extends Seeder
{
    public const LABEL = 'Inventaris Barang Milik Negara';

    public const URL = 'https://bmn.poltekkesbanten.ac.id/';

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

        $existing = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where(function ($query) {
                $query->where('label', self::LABEL)
                    ->orWhere('url', self::URL);
            })
            ->first();

        if ($existing && (int) $existing->parent_id === (int) $parent->id && $existing->url === self::URL) {
            PublicNavigation::forget();

            return;
        }

        $finance = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', 'Statistik Keuangan')
            ->first();

        $sortOrder = $finance
            ? $finance->sort_order + 1
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
            'label' => self::LABEL,
            'type' => 'url',
            'route_name' => null,
            'route_params' => [],
            'url' => self::URL,
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
