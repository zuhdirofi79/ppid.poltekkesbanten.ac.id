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

    public const OPEN_LABEL = 'Buka Dokumen';

    public const DOWNLOAD_LABEL = 'Unduh Dokumen';

    public const OPEN_ARIA = 'Buka Daftar Informasi Publik Tahun 2026';

    public const DOWNLOAD_ARIA = 'Unduh Daftar Informasi Publik Tahun 2026';

    public static function documentUrl(): string
    {
        return asset('docs/'.rawurlencode(self::FILENAME));
    }
}
