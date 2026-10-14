<?php

namespace App\Support;

class AkreditasiSertifikat
{
    public const SECTION = 'Akreditasi Poltekkes Kemenkes Banten';

    /**
     * @return list<array{title: string, documents: list<array{key: string, label: string, filename: string, url: string}>}>
     */
    public static function groups(): array
    {
        return [
            [
                'title' => 'Akreditasi Institusi',
                'documents' => [
                    self::document(
                        'institusi',
                        'Akreditasi Institusi Poltekkes Kemenkes Banten',
                        'Sertifikat Akreditasi Poltekkes Kemenkes Banten.pdf',
                    ),
                ],
            ],
            [
                'title' => 'Akreditasi Program Studi',
                'documents' => [
                    self::document(
                        'd3-keperawatan',
                        'Akreditasi Prodi D3 Keperawatan',
                        '0730-162-Sertifikat_DIPLOMA_TIGA_-_KEPERAWATAN_-_POLITEKNIK_KESEHATAN_KEMENTERIAN_KESEHATAN_BANTEN__BANTEN.pdf',
                    ),
                    self::document(
                        'sarjana-keperawatan',
                        'Akreditasi Prodi Sarjana Terapan Keperawatan',
                        '0516-161-Sertifikat_SARJANA_TERAPAN_-_KEPERAWATAN_-_POLITEKNIK_KESEHATAN_KEMENTERIAN_KESEHATAN_BANTEN__BANTEN.pdf',
                    ),
                    self::document(
                        'd3-kebidanan',
                        'Akreditasi Prodi D3 Kebidanan',
                        '0568-161-Sertifikat_DIPLOMA_TIGA_-_KEBIDANAN_(KAMPUS_RANGKASBITUNG)_-_POLITEKNIK_KESEHATAN_KEMENTERIAN_KESEHATAN_.pdf',
                    ),
                    self::document(
                        'd3-tlm',
                        'Akreditasi Prodi D3 Teknologi Laboratorium Medis (TLM)',
                        '0635-161-Sertifikat_DIPLOMA_TIGA_-_TEKNOLOGI_LABORATORIUM_MEDIS_-_POLITEKNIK_KESEHATAN_KEMENTERIAN_KESEHATAN_BANTEN.pdf',
                    ),
                    self::document(
                        'profesi-ners',
                        'Akreditasi Prodi Profesi Ners',
                        '0517-161-Sertifikat_Profesi_-_NERS_-_POLITEKNIK_KESEHATAN_KEMENTERIAN_KESEHATAN_BANTEN__BANTEN.pdf',
                    ),
                ],
            ],
        ];
    }

    /**
     * @return list<array{key: string, label: string, filename: string, url: string}>
     */
    public static function documents(): array
    {
        return array_merge(...array_column(self::groups(), 'documents'));
    }

    /**
     * @return array{key: string, label: string, filename: string, url: string}
     */
    private static function document(string $key, string $label, string $filename): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'filename' => $filename,
            'url' => asset('docs/'.rawurlencode($filename)),
        ];
    }
}
