<?php

namespace App\Support;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;

class DokumenPengadaanBarangJasa
{
    public const SECTION = 'Surat Perjanjian dengan Pihak Ketiga mengenai Pengadaan Barang dan Jasa';

    public const URL = 'https://s.kemkes.go.id/DokumenPBJPolkesten';

    public const DISPLAY_URL = 's.kemkes.go.id/DokumenPBJPolkesten';

    public const DESCRIPTION = 'Dokumen perjanjian dan data dukung pengadaan barang dan jasa Poltekkes Kemenkes Banten dapat diakses melalui tautan resmi berikut.';

    public const HINT = 'Pindai QR Code atau klik tombol di bawah untuk mengakses dokumen pengadaan barang dan jasa Poltekkes Kemenkes Banten.';

    public const CTA = 'Akses Dokumen Pengadaan Barang dan Jasa';

    public const QR_PATH = 'docs/pengadaan-barang-jasa/qr-dokumen-pbj-poltekkes-banten.png';

    public const QR_ALT = 'QR Code akses Dokumen Surat Pengadaan Barang dan Jasa Poltekkes Kemenkes Banten';

    public const LINK_LABEL = 'Buka Dokumen Pengadaan Barang dan Jasa Poltekkes Kemenkes Banten';

    /**
     * Tautan resmi tetap dipakai bila record CMS belum ada atau URL-nya tidak aman.
     *
     * @return array{title: string, description: string, hint: string, cta: string, url: string, display_url: string, qr_path: string, qr_available: bool, qr_alt: string, link_label: string}
     */
    public static function presentation(PublicInformationCategory $category): array
    {
        $document = PublicDocument::query()
            ->published()
            ->where('public_information_category_id', $category->id)
            ->where('section_title', self::SECTION)
            ->catalogOrdered()
            ->first();

        $url = self::URL;
        if ($document && PublicDocument::isSafeHttpUrl($document->external_url)) {
            $url = (string) $document->external_url;
        }

        return [
            'title' => self::SECTION,
            'description' => self::DESCRIPTION,
            'hint' => self::HINT,
            'cta' => self::CTA,
            'url' => $url,
            'display_url' => self::displayUrl($url),
            'qr_path' => self::QR_PATH,
            'qr_available' => is_file(public_path(self::QR_PATH)),
            'qr_alt' => self::QR_ALT,
            'link_label' => self::LINK_LABEL,
        ];
    }

    public static function displayUrl(string $url): string
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $path = (string) parse_url($url, PHP_URL_PATH);

        $display = $host.$path;

        return $display !== '' ? $display : self::DISPLAY_URL;
    }
}
