<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\DaftarInformasiPublik;
use Illuminate\Database\Seeder;

class DaftarInformasiPublikPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => DaftarInformasiPublik::SLUG],
            [
                'title' => DaftarInformasiPublik::TITLE,
                'excerpt' => DaftarInformasiPublik::EXCERPT,
                'meta_title' => DaftarInformasiPublik::TITLE,
                'meta_description' => DaftarInformasiPublik::META_DESCRIPTION,
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 23,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
