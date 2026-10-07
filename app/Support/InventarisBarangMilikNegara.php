<?php

namespace App\Support;

class InventarisBarangMilikNegara
{
    public const ANCHOR = 'inventaris-barang-milik-negara';

    public const EVIDENCE_PATH = '/informasi-publik/setiap-saat#inventaris-barang-milik-negara';

    public const PORTAL_URL = 'https://bmn.poltekkesbanten.ac.id/';

    public const PORTAL_HOST = 'bmn.poltekkesbanten.ac.id';

    public const HEADING = 'Informasi Data Perbendaharaan atau Inventaris Barang Milik Negara';

    public const DESCRIPTION = 'PPID Poltekkes Kemenkes Banten menyediakan akses terhadap informasi data perbendaharaan dan inventaris Barang Milik Negara (BMN). Informasi lebih lanjut mengenai data dan pengelolaan Barang Milik Negara dapat diakses melalui Portal BMN Poltekkes Kemenkes Banten.';

    public const KICKER = 'INVENTARIS BARANG MILIK NEGARA';

    public const SUMMARY = 'Informasi data perbendaharaan dan inventaris Barang Milik Negara Poltekkes Kemenkes Banten tersedia melalui Portal BMN.';

    public const CTA_LABEL = 'Kunjungi Portal BMN';

    public const CTA_ARIA = 'Kunjungi Portal BMN Poltekkes Kemenkes Banten (membuka tab baru)';

    /**
     * @return array{
     *     anchor: string,
     *     evidence_path: string,
     *     portal_url: string,
     *     portal_host: string,
     *     heading: string,
     *     description: string,
     *     kicker: string,
     *     summary: string,
     *     cta_label: string,
     *     cta_aria: string
     * }
     */
    public static function presentation(): array
    {
        return [
            'anchor' => self::ANCHOR,
            'evidence_path' => self::EVIDENCE_PATH,
            'portal_url' => self::PORTAL_URL,
            'portal_host' => self::PORTAL_HOST,
            'heading' => self::HEADING,
            'description' => self::DESCRIPTION,
            'kicker' => self::KICKER,
            'summary' => self::SUMMARY,
            'cta_label' => self::CTA_LABEL,
            'cta_aria' => self::CTA_ARIA,
        ];
    }
}
