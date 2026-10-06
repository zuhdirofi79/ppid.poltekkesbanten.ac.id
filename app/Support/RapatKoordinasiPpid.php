<?php

namespace App\Support;

class RapatKoordinasiPpid
{
    public const SLUG = 'rapat-koordinasi-ppid';

    public const TITLE = 'Rapat Koordinasi PPID';

    public const EYEBROW = 'Informasi Publik';

    public const EXCERPT = 'Dokumentasi dan bukti pelaksanaan koordinasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten.';

    public const META_DESCRIPTION = 'Dokumentasi dan bukti pelaksanaan koordinasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten.';

    public const SECTION_HEADING = 'Dokumen Rapat Koordinasi PPID';

    public const DOCUMENT_TITLE = 'Rapat Koordinasi PPID';

    public const DOCUMENT_DESCRIPTION = 'Dokumen pendukung pelaksanaan rapat koordinasi PPID Poltekkes Kemenkes Banten.';

    public const FORMAT_LABEL = 'PDF / Dokumen';

    public const FILENAME = 'RAPAT KOORDINASI PPID.pdf';

    public const OPEN_LABEL = 'Buka PDF';

    public const DOWNLOAD_LABEL = 'Unduh PDF';

    public const OPEN_ARIA = 'Buka dokumen Rapat Koordinasi PPID';

    public const DOWNLOAD_ARIA = 'Unduh dokumen Rapat Koordinasi PPID';

    public const UNAVAILABLE = 'Dokumen Rapat Koordinasi PPID belum tersedia.';

    public const BROWSER_FALLBACK = 'Tampilan PDF tidak tersedia pada browser Anda. Silakan buka atau unduh dokumen.';

    public static function documentUrl(): string
    {
        return asset('docs/'.rawurlencode(self::FILENAME));
    }

    public static function isAvailable(): bool
    {
        return is_file(public_path('docs/'.self::FILENAME));
    }
}
