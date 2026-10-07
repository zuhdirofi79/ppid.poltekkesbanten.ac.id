<?php

namespace App\Support;

use App\Models\Page;

/**
 * Konten resmi Visi dan Misi Poltekkes Kemenkes Banten untuk /halaman/visi-dan-misi.
 * Dipakai sebagai default CMS dan fallback tampilan bila record masih berisi teks lama.
 */
class VisiDanMisi
{
    public const SLUG = 'visi-dan-misi';

    public const TITLE = 'Visi dan Misi';

    public const DOCUMENT_TITLE = 'Visi dan Misi - PPID Poltekkes Kemenkes Banten';

    public const META_TITLE = self::DOCUMENT_TITLE;

    public const META_DESCRIPTION = 'Visi dan misi Poltekkes Kemenkes Banten sebagai perguruan tinggi kesehatan yang unggul, inovatif, dan berdaya saing global Tahun 2044.';

    public const EXCERPT = self::META_DESCRIPTION;

    public const VISION = 'Menjadi Perguruan Tinggi Kesehatan yang Unggul, Inovatif, dan Berdaya Saing Global Tahun 2044';

    public const LEGACY_TITLE = 'Visi dan Misi PPID Poltekkes Kemenkes Banten';

    public const LEGACY_META_TITLE = self::LEGACY_TITLE;

    public const LEGACY_EXCERPT = 'Visi dan misi pelayanan informasi publik PPID Poltekkes Kemenkes Banten.';

    public const LEGACY_META_MARKER = 'transparan, akuntabel, prima';

    public const LEGACY_VISION_MARKER = 'Terwujudnya pelayanan informasi yang transparan, akuntabel, dan prima';

    /** @var list<string> */
    public const MISSIONS = [
        'Menyelenggarakan pendidikan tinggi vokasi dan profesi kesehatan yang unggul dan inovatif untuk menghasilkan tenaga kesehatan yang kompeten dan berdaya saing global.',
        'Mengembangkan penelitian terapan dan inovatif di bidang kesehatan yang berdampak dan relevan dengan kebutuhan masyarakat global.',
        'Melaksanakan Pengabdian kepada Masyarakat yang inovatif, berkelanjutan dan berorientasi pemberdayaan masyarakat untuk peningkatan derajat Kesehatan.',
        'Memperkuat tata kelola kelembagaan yang adaptif, efektif dengan memanfaatkan transformasi digital untuk membangun organisasi yang unggul.',
        'Memperluas jejaring kerja sama nasional dan internasional untuk memperkuat kapasitas kelembagaan dan daya saing global.',
    ];

    public static function documentTitle(?string $seoTitle): string
    {
        $title = trim((string) $seoTitle);

        if ($title === '' || $title === self::LEGACY_TITLE || $title === self::LEGACY_META_TITLE) {
            return self::DOCUMENT_TITLE;
        }

        if (str_contains($title, (string) config('ppid.name'))) {
            return $title;
        }

        return $title.' — '.config('ppid.name');
    }

    public static function isLegacyBody(?string $body): bool
    {
        $body = trim((string) $body);

        if ($body === '') {
            return true;
        }

        if (str_contains($body, self::VISION)) {
            return false;
        }

        return str_contains($body, self::LEGACY_VISION_MARKER);
    }

    public static function isLegacyRecord(Page $page): bool
    {
        return self::isLegacyBody($page->body);
    }

    public static function bodyHtml(): string
    {
        $vision = '“'.self::VISION.'”';
        $items = '';

        foreach (self::MISSIONS as $index => $mission) {
            $number = $index + 1;
            $items .= <<<HTML
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">{$number}</span>
                        <p class="ppid-mission-text">{$mission}</p>
                    </li>

            HTML;
        }

        return <<<HTML
<section class="ppid-vision-section" aria-labelledby="visi-misi-heading">
    <h2 id="visi-misi-heading" class="ppid-vision-heading">VISI DAN MISI POLTEKKES KEMENKES BANTEN</h2>
</section>
<section class="ppid-vision-section" aria-labelledby="visi-heading">
    <h2 id="visi-heading" class="ppid-vision-heading">Visi</h2>
    <blockquote class="ppid-vision-box">
        <p class="ppid-vision-text">{$vision}</p>
    </blockquote>
</section>
<section class="ppid-mission-section" aria-labelledby="misi-heading">
    <h2 id="misi-heading" class="ppid-mission-heading">Misi</h2>
    <ol class="ppid-mission-list">
{$items}    </ol>
</section>
HTML;
    }
}
