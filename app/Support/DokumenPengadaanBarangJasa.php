<?php

namespace App\Support;

class DokumenPengadaanBarangJasa
{
    public const SECTION = 'Surat Perjanjian dengan Pihak Ketiga mengenai Pengadaan Barang dan Jasa';

    public const DESCRIPTION = 'Dokumen dan data dukung pengadaan barang dan jasa Poltekkes Kemenkes Banten.';

    public const IMAGE_PATH = 'docs/dok-pbaj.jpeg';

    public const IMAGE_ALT = 'Dokumen Pengadaan Barang dan Jasa Poltekkes Kemenkes Banten';

    public const DOCUMENT_LABEL = 'DOKUMEN PENGADAAN BARANG DAN JASA';

    public const FORMAT_LABEL = 'JPEG / Dokumen PBJ';

    public const OPEN_LABEL = 'Lihat Gambar Penuh';

    public const DOWNLOAD_LABEL = 'Unduh Gambar';

    public const DOWNLOAD_NAME = 'dok-pbaj.jpeg';

    public const OPEN_ARIA = 'Buka Dokumen Pengadaan Barang dan Jasa dalam ukuran penuh';

    public static function imageUrl(): string
    {
        return asset(self::IMAGE_PATH);
    }

    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     image_url: string,
     *     image_alt: string,
     *     document_label: string,
     *     format_label: string,
     *     open_label: string,
     *     download_label: string,
     *     download_name: string,
     *     open_aria: string
     * }
     */
    public static function presentation(): array
    {
        return [
            'title' => self::SECTION,
            'description' => self::DESCRIPTION,
            'image_url' => self::imageUrl(),
            'image_alt' => self::IMAGE_ALT,
            'document_label' => self::DOCUMENT_LABEL,
            'format_label' => self::FORMAT_LABEL,
            'open_label' => self::OPEN_LABEL,
            'download_label' => self::DOWNLOAD_LABEL,
            'download_name' => self::DOWNLOAD_NAME,
            'open_aria' => self::OPEN_ARIA,
        ];
    }
}
