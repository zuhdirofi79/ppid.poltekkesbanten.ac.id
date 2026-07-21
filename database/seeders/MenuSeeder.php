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
                MenuItem::query()->create([
                    'menu_id' => $menu->id,
                    'parent_id' => $parent->id,
                    'label' => $child['label'],
                    'type' => 'route',
                    'route_name' => $child['route'] ?? null,
                    'route_params' => $child['params'] ?? [],
                    'sort_order' => $order++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
