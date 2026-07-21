<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('ppid.units', []) as $unit) {
            Unit::query()->updateOrCreate(
                ['slug' => $unit['slug']],
                [
                    'name' => $unit['name'],
                    'description' => $unit['description'],
                    'url' => $unit['url'] ?? null,
                    'is_active' => $unit['is_active'] ?? true,
                    'sort_order' => $unit['sort_order'] ?? 0,
                ],
            );
        }
    }
}
