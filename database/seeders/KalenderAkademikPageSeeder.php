<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class KalenderAkademikPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => 'kalender-akademik'],
            [
                'title' => 'Kalender Akademik',
                'excerpt' => 'Kalender kegiatan akademik Poltekkes Kemenkes Banten berdasarkan tahun akademik.',
                'meta_title' => 'Kalender Akademik | PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Kalender kegiatan akademik Poltekkes Kemenkes Banten berdasarkan tahun akademik.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 38,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
