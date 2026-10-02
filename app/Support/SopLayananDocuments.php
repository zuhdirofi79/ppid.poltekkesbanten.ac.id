<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class SopLayananDocuments
{
    public const SLUG = 'standar-operasional-prosedur-layanan-informasi-publik';

    /**
     * @return list<array{title: string, filename: string, description: string, available: bool, url: ?string}>
     */
    public static function all(): array
    {
        return self::resolve(public_path());
    }

    /**
     * @return list<array{title: string, filename: string, description: string, available: bool, url: ?string}>
     */
    public static function resolve(string $publicRoot): array
    {
        return array_map(function (array $document) use ($publicRoot): array {
            $relative = 'docs/'.$document['filename'];
            $available = is_file(rtrim($publicRoot, '\\/').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative));

            if (! $available) {
                Log::warning('Dokumen SOP layanan informasi publik tidak ditemukan.', [
                    'halaman' => self::SLUG,
                    'judul' => $document['title'],
                    'filename' => $document['filename'],
                ]);
            }

            return [
                'title' => $document['title'],
                'filename' => $document['filename'],
                'description' => $document['description'],
                'available' => $available,
                'url' => $available ? self::publicUrl($relative) : null,
            ];
        }, self::definitions());
    }

    public static function publicUrl(string $relative): string
    {
        $encoded = implode('/', array_map(
            rawurlencode(...),
            explode('/', str_replace('\\', '/', $relative)),
        ));

        return asset($encoded);
    }

    /**
     * @return list<array{title: string, filename: string, description: string}>
     */
    public static function definitions(): array
    {
        return [
            [
                'title' => 'SOP Pengelolaan Permohonan Informasi',
                'filename' => 'SOP Pengelolaan Permohonan Informasi.pdf',
                'description' => 'Pedoman pengelolaan permohonan informasi publik.',
            ],
            [
                'title' => 'SOP Pengelolaan Keberatan atas Informasi',
                'filename' => 'SOP Pengelolaan Keberatan atas Informasi.pdf',
                'description' => 'Pedoman pengelolaan keberatan atas informasi publik.',
            ],
            [
                'title' => 'SOP Penanganan Sengketa Informasi Publik',
                'filename' => 'SOP Penanganan Sengketa Informasi Publik.pdf',
                'description' => 'Pedoman penanganan sengketa informasi publik.',
            ],
            [
                'title' => 'SOP Penetapan dan Pemuktahiran DIP',
                'filename' => 'SOP Penetapan dan Pemuktahiran DIP.pdf',
                'description' => 'Pedoman penetapan dan pemutakhiran Daftar Informasi Publik.',
            ],
            [
                'title' => 'SOP Pengujian tentang Konsekuensi',
                'filename' => 'SOP Pengujian tentang Konsekuensi.pdf',
                'description' => 'Pedoman pengujian konsekuensi informasi publik.',
            ],
            [
                'title' => 'SOP Pendokumentasian Informasi Publik',
                'filename' => 'SOP   pendokumentasian informasi publik.pdf',
                'description' => 'Pedoman pendokumentasian informasi publik.',
            ],
            [
                'title' => 'SOP Pendokumentasian Informasi yang Dikecualikan',
                'filename' => 'SOP  pendokumentasian informasi yang dikecualikan.pdf',
                'description' => 'Pedoman pendokumentasian informasi yang dikecualikan.',
            ],
        ];
    }
}
