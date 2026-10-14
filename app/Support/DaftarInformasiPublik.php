<?php

namespace App\Support;

class DaftarInformasiPublik
{
    public const SLUG = 'daftar-informasi-publik';

    public const TITLE = 'Daftar Informasi Publik';

    public const EYEBROW = 'Informasi Publik';

    public const EXCERPT = 'Daftar Informasi Publik (DIP) Poltekkes Kemenkes Banten yang telah dimuktahirkan untuk Tahun 2026.';

    public const META_DESCRIPTION = 'Daftar Informasi Publik (DIP) Poltekkes Kemenkes Banten yang telah dimuktahirkan untuk Tahun 2026.';

    public const SECTION_HEADING = 'Daftar Informasi Publik yang telah dimuktahirkan 2026';

    public const DOCUMENT_TITLE = 'Daftar Informasi Publik Tahun 2026';

    public const DOCUMENT_DESCRIPTION = 'Daftar Informasi Publik Poltekkes Kemenkes Banten yang telah dimuktahirkan untuk Tahun 2026.';

    public const FORMAT_LABEL = 'PDF / Dokumen';

    public const FILENAME = 'DAFTAR INFORMASI PUBLIK 2026.pdf';

    public const OPEN_LABEL = 'Preview PDF';

    public const DOWNLOAD_LABEL = 'Unduh Dokumen';

    public const PREVIEW_HEADING = 'Preview Daftar Informasi Publik Tahun 2026';

    public const CLOSE_LABEL = 'Tutup Preview';

    public const OPEN_ARIA = 'Preview Daftar Informasi Publik Tahun 2026';

    public const CLOSE_ARIA = 'Tutup preview Daftar Informasi Publik Tahun 2026';

    public const DOWNLOAD_ARIA = 'Unduh Daftar Informasi Publik Tahun 2026';

    public const IFRAME_TITLE = 'Preview Daftar Informasi Publik Poltekkes Kemenkes Banten Tahun 2026';

    public static function documentUrl(): string
    {
        return asset('docs/'.rawurlencode(self::FILENAME));
    }
}
