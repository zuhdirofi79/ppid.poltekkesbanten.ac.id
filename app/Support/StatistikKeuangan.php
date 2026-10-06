<?php

namespace App\Support;

class StatistikKeuangan
{
    public const SLUG = 'statistik-keuangan';

    public const TITLE = 'Statistik Keuangan';

    public const EXCERPT = 'Data statistik dan informasi mengenai keuangan Poltekkes Kemenkes Banten sebagai bagian dari keterbukaan informasi publik.';

    public const EXPLANATION = 'PPID Poltekkes Kemenkes Banten menyediakan data statistik mengenai keuangan satuan kerja. Informasi keuangan yang tersedia pada halaman ini bersumber dari dokumen Daftar Isian Pelaksanaan Anggaran (DIPA) Poltekkes Kemenkes Banten Tahun Anggaran 2026.';

    public const META_DESCRIPTION = 'Informasi statistik keuangan Poltekkes Kemenkes Banten berdasarkan dokumen resmi DIPA Tahun Anggaran 2026.';

    public const DOCUMENT_TITLE = 'DIPA TAHUN ANGGARAN 2026';

    public const DOCUMENT_HEADING = 'DIPA Tahun Anggaran 2026';

    public const DOCUMENT_DESCRIPTION = 'Daftar Isian Pelaksanaan Anggaran Poltekkes Kemenkes Banten Tahun Anggaran 2026.';

    public const PERIOD = 'Tahun Anggaran 2026';

    public const FILENAME = 'DIPA POLKESTEN.pdf';

    public static function documentUrl(): string
    {
        return asset('docs/'.rawurlencode(self::FILENAME));
    }
}
