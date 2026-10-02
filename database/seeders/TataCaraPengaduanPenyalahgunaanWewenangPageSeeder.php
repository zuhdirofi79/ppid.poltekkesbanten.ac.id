<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class TataCaraPengaduanPenyalahgunaanWewenangPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => 'tata-cara-pengaduan-penyalahgunaan-wewenang'],
            [
                'title' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang',
                'excerpt' => 'Informasi tata cara pengaduan atas dugaan penyalahgunaan wewenang atau pelanggaran yang dilakukan oleh pejabat di lingkungan Poltekkes Kemenkes Banten.',
                'meta_title' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang | PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Informasi tata cara pengaduan atas dugaan penyalahgunaan wewenang atau pelanggaran yang dilakukan oleh pejabat di lingkungan Poltekkes Kemenkes Banten.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 39,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
