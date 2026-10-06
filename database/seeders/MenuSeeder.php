<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menu = Menu::query()->updateOrCreate(
            ['location' => 'primary'],
            ['name' => 'Navigasi Utama', 'is_active' => true],
        );

        $menu->items()->delete();

        $items = config('ppid.navigation', []);
        $order = 0;

        foreach ($items as $item) {
            $parent = MenuItem::query()->create([
                'menu_id' => $menu->id,
                'parent_id' => null,
                'label' => $item['label'],
                'type' => ($item['route'] ?? null) ? 'route' : 'url',
                'route_name' => $item['route'] ?? null,
                'route_params' => [],
                'url' => ($item['route'] ?? null) ? null : '#',
                'sort_order' => $order++,
                'is_active' => true,
            ]);

            foreach ($item['children'] ?? [] as $child) {
                $childUrl = $child['url'] ?? null;
                $childRoute = $child['route'] ?? null;
                $isExternal = is_string($childUrl) && $childUrl !== '' && $childRoute === null;

                MenuItem::query()->create([
                    'menu_id' => $menu->id,
                    'parent_id' => $parent->id,
                    'label' => $child['label'],
                    'type' => $isExternal ? 'url' : 'route',
                    'route_name' => $isExternal ? null : $childRoute,
                    'route_params' => $isExternal ? [] : ($child['params'] ?? []),
                    'url' => $isExternal ? $childUrl : null,
                    'sort_order' => $order++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
