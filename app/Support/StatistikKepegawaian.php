<?php

namespace App\Support;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\Support\Collection;

class StatistikKepegawaian
{
    public const SECTION = 'Statistik Kepegawaian';

    public const INTRO = 'Data statistik kepegawaian yang dibuat dan dikelola oleh Poltekkes Kemenkes Banten.';

    public const NOTE = 'Informasi statistik ini menyajikan gambaran data sumber daya manusia Poltekkes Kemenkes Banten sebagai bagian dari keterbukaan informasi publik.';

    /**
     * Definisi awal. Periode berikutnya cukup menambah entri di sini lalu menjalankan seeder.
     *
     * @return list<array{period: string, period_key: string, title: string, caption: string, path: string, alt: string, sort_order: int}>
     */
    public static function definitions(): array
    {
        return [
            [
                'period' => 'Oktober 2026',
                'period_key' => 'oktober-2026',
                'title' => 'Statistik Kepegawaian Oktober 2026 - Grafik 1',
                'caption' => 'Statistik Kepegawaian Poltekkes Kemenkes Banten — Oktober 2026',
                'path' => 'docs/statistik-kepegawaian/oktober-2026/grafik-1.jpeg',
                'alt' => 'Statistik kepegawaian Poltekkes Kemenkes Banten periode Oktober 2026 - Grafik 1',
                'sort_order' => 10,
            ],
            [
                'period' => 'Oktober 2026',
                'period_key' => 'oktober-2026',
                'title' => 'Statistik Kepegawaian Oktober 2026 - Grafik 2',
                'caption' => 'Statistik Kepegawaian Poltekkes Kemenkes Banten — Oktober 2026',
                'path' => 'docs/statistik-kepegawaian/oktober-2026/grafik-2.jpeg',
                'alt' => 'Statistik kepegawaian Poltekkes Kemenkes Banten periode Oktober 2026 - Grafik 2',
                'sort_order' => 20,
            ],
        ];
    }

    /**
     * @return Collection<int, array{key: string, label: string, charts: Collection<int, array<string, mixed>>}>
     */
    public static function groupsFor(PublicInformationCategory $category): Collection
    {
        $charts = PublicDocument::query()
            ->published()
            ->where('public_information_category_id', $category->id)
            ->where('section_title', self::SECTION)
            ->catalogOrdered()
            ->get()
            ->map(fn (PublicDocument $document) => self::present($document))
            ->filter()
            ->values();

        return $charts
            ->groupBy('period_key')
            ->map(function (Collection $items, string $key) {
                /** @var array<string, mixed> $first */
                $first = $items->first();

                return [
                    'key' => $key,
                    'label' => (string) ($first['period'] ?? $key),
                    'charts' => $items->values(),
                ];
            })
            ->values();
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function present(PublicDocument $document): ?array
    {
        $path = self::relativePublicPath($document->internal_url);
        if ($path === null) {
            return null;
        }

        $periodKey = self::periodKeyFromPath($path) ?? 'lainnya';
        $period = self::periodLabel($periodKey, $document->year);
        $definition = self::definitionForPath($path);
        $caption = trim((string) $document->description);

        return [
            'title' => $document->title,
            'caption' => $caption !== '' ? $caption : self::captionFor($period),
            'alt' => $definition['alt'] ?? $document->title,
            'path' => $path,
            'period' => $period,
            'period_key' => $periodKey,
            'width' => 1448,
            'height' => 1086,
        ];
    }

    public static function captionFor(string $period): string
    {
        return 'Statistik Kepegawaian Poltekkes Kemenkes Banten — '.$period;
    }

    public static function relativePublicPath(?string $internalUrl): ?string
    {
        $path = trim((string) $internalUrl);
        if ($path === '' || str_starts_with($path, '//') || preg_match('#^[a-z][a-z0-9+.-]*:#i', $path)) {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    public static function periodKeyFromPath(string $path): ?string
    {
        if (preg_match('#(?:^|/)statistik-kepegawaian/([^/]+)/#', $path, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    public static function periodLabel(string $periodKey, ?int $year = null): string
    {
        $months = [
            'januari' => 'Januari',
            'februari' => 'Februari',
            'maret' => 'Maret',
            'april' => 'April',
            'mei' => 'Mei',
            'juni' => 'Juni',
            'juli' => 'Juli',
            'agustus' => 'Agustus',
            'september' => 'September',
            'oktober' => 'Oktober',
            'november' => 'November',
            'desember' => 'Desember',
        ];

        if (preg_match('/^([a-z]+)-(\d{4})$/', $periodKey, $matches) === 1 && isset($months[$matches[1]])) {
            return $months[$matches[1]].' '.$matches[2];
        }

        if (preg_match('/^\d{4}$/', $periodKey) === 1) {
            return $periodKey;
        }

        if ($year) {
            return (string) $year;
        }

        return ucfirst(str_replace('-', ' ', $periodKey));
    }

    /**
     * @return array{period: string, period_key: string, title: string, caption: string, path: string, alt: string, sort_order: int}|null
     */
    private static function definitionForPath(string $path): ?array
    {
        foreach (self::definitions() as $definition) {
            if ($definition['path'] === $path) {
                return $definition;
            }
        }

        return null;
    }
}
