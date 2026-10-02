<?php

namespace App\Support;

class InformationDesk
{
    public const SLUG = 'information-desk';

    public const TITLE = 'Information Desk';

    public const EXCERPT = 'Fasilitas dan kelengkapan layanan informasi publik PPID Poltekkes Kemenkes Banten.';

    public const META_DESCRIPTION = 'Fasilitas Information Desk dan sarana pelayanan informasi publik PPID Poltekkes Kemenkes Banten.';

    public const HIGHLIGHT = 'Meja, kursi, kursi tunggu dan sarana penerimaan (desk information).';

    /**
     * @return list<string>
     */
    public static function introParagraphs(): array
    {
        return [
            'PPID Poltekkes Kemenkes Banten menyediakan fasilitas pelayanan informasi publik untuk mendukung pelayanan yang nyaman, tertib, dan mudah diakses oleh masyarakat.',
            'Fasilitas yang tersedia meliputi meja pelayanan, kursi petugas, kursi tunggu, ruang penerimaan tamu, serta sarana penerimaan informasi (desk information).',
        ];
    }

    public static function introHtml(): string
    {
        return implode('', array_map(
            fn (string $paragraph) => '<p>'.e($paragraph).'</p>',
            self::introParagraphs(),
        ));
    }

    /**
     * Foto fasilitas. Rasio sumber 1280×720 (16:9), sehingga kartu 16:9 menampilkan seluruh ruangan.
     *
     * @return list<array{path: string, caption: string, alt: string, width: int, height: int}>
     */
    public static function photos(): array
    {
        return [
            [
                'path' => 'docs/desk-information/receptionist-room.jpeg',
                'caption' => 'Area Receptionist / Information Desk',
                'alt' => 'Area receptionist dan information desk PPID Poltekkes Kemenkes Banten',
                'width' => 1280,
                'height' => 720,
            ],
            [
                'path' => 'docs/desk-information/receptionist-room2.jpeg',
                'caption' => 'Area Pelayanan Informasi',
                'alt' => 'Area pelayanan informasi PPID Poltekkes Kemenkes Banten',
                'width' => 1280,
                'height' => 720,
            ],
            [
                'path' => 'docs/desk-information/ruang-tamu1.jpeg',
                'caption' => 'Ruang Tunggu dan Penerimaan Tamu',
                'alt' => 'Ruang tunggu pelayanan informasi PPID Poltekkes Kemenkes Banten',
                'width' => 1280,
                'height' => 720,
            ],
            [
                'path' => 'docs/desk-information/ruang-tamu2.jpeg',
                'caption' => 'Fasilitas Ruang Tamu',
                'alt' => 'Fasilitas ruang tamu PPID Poltekkes Kemenkes Banten',
                'width' => 1280,
                'height' => 720,
            ],
            [
                'path' => 'docs/desk-information/ruang-tamu3.jpeg',
                'caption' => 'Area Penerimaan dan Kursi Tunggu',
                'alt' => 'Area penerimaan dan kursi tunggu PPID Poltekkes Kemenkes Banten',
                'width' => 1280,
                'height' => 720,
            ],
        ];
    }
}
