<?php

namespace Database\Seeders;

use App\Models\AcademicCalendar;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        // Seed 2026 calendar - IDEMPOTENT: don't overwrite if already exists
        // Note: 2026 uses PNG image, NOT PDF
        AcademicCalendar::query()->firstOrCreate(
            ['year' => 2026],
            [
                'title' => 'Kalender Akademik Tahun Akademik 2026/2027',
                'description' => 'Kalender Akademik Poltekkes Kemenkes Banten Tahun Akademik 2026/2027.',
                'source_type' => AcademicCalendar::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => 'docs/Poltekkes Banten Academic Calendar 2026_2027.png',
                'mime_type' => 'image/png',
                'extension' => 'png',
                'is_published' => true,
                'sort_order' => 2026,
                'published_at' => now(),
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ]
        );
    }
}
