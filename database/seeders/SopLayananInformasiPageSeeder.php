<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\SopLayananDocuments;
use Illuminate\Database\Seeder;

class SopLayananInformasiPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => SopLayananDocuments::SLUG],
            [
                'title' => 'Standar Operasional Prosedur Layanan Informasi Publik',
                'excerpt' => 'Kumpulan Standar Operasional Prosedur (SOP) yang menjadi pedoman penyelenggaraan dan pengelolaan layanan informasi publik PPID Poltekkes Kemenkes Banten.',
                'meta_title' => 'Standar Operasional Prosedur Layanan Informasi Publik',
                'meta_description' => 'Kumpulan SOP pengelolaan permohonan informasi, keberatan, sengketa informasi, pemutakhiran DIP, uji konsekuensi, dan pendokumentasian informasi publik PPID Poltekkes Kemenkes Banten.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 45,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
