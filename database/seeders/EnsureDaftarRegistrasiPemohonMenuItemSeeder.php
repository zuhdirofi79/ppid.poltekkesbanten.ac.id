<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsureDaftarRegistrasiPemohonMenuItemSeeder extends Seeder
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
            ->where('label', 'SOP & Layanan')
            ->first();

        if (! $parent) {
            return;
        }

        $label = 'Daftar Registrasi Pemohon';
        $already = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', $label)
            ->exists();

        if ($already) {
            PublicNavigation::forget();

            return;
        }

        $sop = MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('parent_id', $parent->id)
            ->where('label', 'Standar Operasional Prosedur Layanan Informasi Publik')
            ->first();

        $sortOrder = $sop
            ? $sop->sort_order + 1
            : ((int) MenuItem::query()->where('menu_id', $menu->id)->max('sort_order')) + 1;

        MenuItem::query()
            ->where('menu_id', $menu->id)
            ->where('sort_order', '>=', $sortOrder)
            ->increment('sort_order');

        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'parent_id' => $parent->id,
            'label' => $label,
            'type' => 'route',
            'route_name' => 'services.show',
            'route_params' => ['slug' => 'daftar-registrasi-pemohon'],
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        PublicNavigation::forget();
    }
}
