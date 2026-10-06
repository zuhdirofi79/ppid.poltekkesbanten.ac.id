<?php

namespace App\Support;

use App\Models\PublicDocument;
use Illuminate\Support\Collection;

class InformasiDikecualikan
{
    public const SLUG = 'dikecualikan';

    public const TITLE = 'Informasi yang Dikecualikan';

    public const EXCERPT = 'Daftar informasi publik yang dikecualikan di lingkungan Poltekkes Kemenkes Banten sesuai ketentuan peraturan perundang-undangan dan hasil penetapan yang berlaku.';

    public const FILENAME = 'DIK PPID POLKESTEN.docx';

    public const INTERNAL_PATH = '/docs/DIK PPID POLKESTEN.docx';

    public const YEAR = 2026;

    public const DOCUMENT_TITLE = 'Daftar Informasi yang Dikecualikan Tahun 2026';

    public const DOCUMENT_DESCRIPTION = 'Dokumen Daftar Informasi yang Dikecualikan (DIK) Poltekkes Kemenkes Banten Tahun 2026.';

    public const HEADING_KICKER = 'DAFTAR INFORMASI YANG DIKECUALIKAN';

    public const MISSING_YEAR = 'Dokumen Daftar Informasi yang Dikecualikan untuk tahun tersebut belum tersedia.';

    public const MISSING_FILE = 'DOKUMEN BELUM TERSEDIA';

    /**
     * @return array{
     *     years: list<int>,
     *     selected: int|null,
     *     notice: string|null,
     *     heading_kicker: string,
     *     heading_year: string|null,
     *     cards: list<array<string, mixed>>
     * }
     */
    public static function presentation(mixed $requestedYear): array
    {
        $documents = PublicDocument::query()
            ->published()
            ->whereHas('informationCategory', fn ($query) => $query->where('slug', self::SLUG)->where('is_active', true))
            ->whereNotNull('year')
            ->orderByDesc('year')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        /** @var Collection<int, int> $years */
        $years = $documents
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->sortDesc()
            ->values();

        $selected = null;
        $notice = null;

        if ($requestedYear === null || $requestedYear === '') {
            $selected = $years->first();
        } elseif (! is_scalar($requestedYear) || ! preg_match('/^\d{4}$/', (string) $requestedYear)) {
            $notice = self::MISSING_YEAR;
        } else {
            $year = (int) $requestedYear;
            if ($year < 2000 || $year > 2100 || ! $years->contains($year)) {
                $notice = self::MISSING_YEAR;
            } else {
                $selected = $year;
            }
        }

        $cards = $selected === null
            ? []
            : self::cards($documents->filter(fn (PublicDocument $document) => (int) $document->year === $selected)->values());

        return [
            'years' => $years->all(),
            'selected' => $selected,
            'notice' => $notice,
            'heading_kicker' => self::HEADING_KICKER,
            'heading_year' => $selected === null
                ? null
                : 'POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN '.$selected,
            'cards' => $cards,
        ];
    }

    /**
     * @param  Collection<int, PublicDocument>  $documents
     * @return list<array<string, mixed>>
     */
    private static function cards(Collection $documents): array
    {
        return $documents->map(function (PublicDocument $document) {
            $href = self::href($document);
            $title = (string) $document->title;

            return [
                'title' => $title,
                'description' => filled($document->description) ? (string) $document->description : null,
                'format' => self::formatLabel($document),
                'href' => $href,
                'download_name' => self::downloadName($document),
                'available' => $href !== null,
                'open_aria' => 'Buka '.$title,
                'download_aria' => 'Unduh '.$title,
            ];
        })->all();
    }

    public static function href(PublicDocument $document): ?string
    {
        if ($document->hasInternalUrl()) {
            $relative = rawurldecode((string) (parse_url((string) $document->internal_url, PHP_URL_PATH) ?: ''));
            $relative = ltrim(str_replace('\\', '/', $relative), '/');
            if ($relative === '' || str_contains($relative, '..') || ! is_file(public_path($relative))) {
                return null;
            }

            return $document->publicHref();
        }

        return $document->publicHref();
    }

    public static function formatLabel(PublicDocument $document): string
    {
        $name = (string) ($document->file_name ?: $document->internal_url ?: $document->file_path);
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return match ($extension) {
            'pdf' => 'PDF / Dokumen',
            'doc' => 'DOC / Dokumen',
            'docx' => 'DOCX / Dokumen',
            default => 'Dokumen',
        };
    }

    private static function downloadName(PublicDocument $document): string
    {
        if (filled($document->file_name)) {
            return (string) $document->file_name;
        }

        $path = (string) (parse_url((string) $document->internal_url, PHP_URL_PATH) ?: '');
        $name = basename(rawurldecode($path));

        return $name !== '' ? $name : 'dokumen';
    }
}
