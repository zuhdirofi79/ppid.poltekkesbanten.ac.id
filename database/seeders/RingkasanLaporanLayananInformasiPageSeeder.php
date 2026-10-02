<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class RingkasanLaporanLayananInformasiPageSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->value('id');

        Page::query()->firstOrCreate(
            ['slug' => 'ringkasan-laporan-layanan-informasi'],
            [
                'title' => 'Ringkasan Laporan Layanan Informasi Publik',
                'excerpt' => 'Ringkasan statistik pelayanan informasi publik PPID Poltekkes Kemenkes Banten yang memuat jumlah permohonan, waktu pemenuhan permohonan, status pemenuhan, serta alasan penolakan permohonan informasi publik.',
                'meta_title' => 'Ringkasan Laporan Layanan Informasi Publik | PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Ringkasan statistik pelayanan informasi publik PPID Poltekkes Kemenkes Banten yang memuat jumlah permohonan, waktu pemenuhan permohonan, status pemenuhan, serta alasan penolakan permohonan informasi publik.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 40,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ],
        );
    }
}
