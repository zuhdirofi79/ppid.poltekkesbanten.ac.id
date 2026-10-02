<?php

namespace Database\Seeders;

use App\Models\AcademicCalendar;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicCalendarSeeder extends Seeder
{
    private const LEGACY_PDF_PATH = 'docs/draft-kalender-akademik-2026-occasionally.pdf';

    private const PNG_PATH = 'docs/Poltekkes Banten Academic Calendar 2026_2027.png';

    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        $existing = AcademicCalendar::query()->where('year', 2026)->first();

        if (! $existing) {
            AcademicCalendar::query()->create([
                'year' => 2026,
                'title' => 'Kalender Akademik Tahun Akademik 2026/2027',
                'description' => 'Kalender Akademik Poltekkes Kemenkes Banten Tahun Akademik 2026/2027.',
                'source_type' => AcademicCalendar::SOURCE_PUBLIC_ASSET,
                'public_asset_path' => self::PNG_PATH,
                'mime_type' => 'image/png',
                'extension' => 'png',
                'is_published' => true,
                'sort_order' => 2026,
                'published_at' => now(),
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ]);

            return;
        }

        if ($existing->public_asset_path !== self::LEGACY_PDF_PATH) {
            return;
        }

        $existing->forceFill([
            'source_type' => AcademicCalendar::SOURCE_PUBLIC_ASSET,
            'public_asset_path' => self::PNG_PATH,
            'mime_type' => 'image/png',
            'extension' => 'png',
            'updated_by' => $admin?->id ?? $existing->updated_by,
        ])->save();
    }
}
