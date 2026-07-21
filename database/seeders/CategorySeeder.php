<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use App\Models\PublicInformationCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('ppid.information_categories', []) as $index => $category) {
            PublicInformationCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['title'],
                    'excerpt' => $category['excerpt'],
                    'icon' => $category['icon'] ?? null,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        $docCategories = [
            ['name' => 'Regulasi', 'slug' => 'regulasi'],
            ['name' => 'Laporan', 'slug' => 'laporan'],
            ['name' => 'Kelembagaan', 'slug' => 'kelembagaan'],
            ['name' => 'Lainnya', 'slug' => 'lainnya'],
        ];

        foreach ($docCategories as $index => $category) {
            DocumentCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => 'Kategori dokumen CONTOH.',
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
