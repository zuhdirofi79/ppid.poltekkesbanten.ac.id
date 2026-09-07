<?php

/**
 * Konfigurasi identitas & navigasi publik PPID Poltekkes Kemenkes Banten.
 * Data kontak yang belum resmi: placeholder TODO — lihat docs/CONTENT_REQUIREMENTS.md
 */

return [

    'name' => 'PPID Poltekkes Kemenkes Banten',

    'name_long' => 'Pejabat Pengelola Informasi dan Dokumentasi Politeknik Kesehatan Kementerian Kesehatan Banten',

    'institution' => 'Politeknik Kesehatan Kementerian Kesehatan Banten',

    'logo' => 'images/logo/logo-poltekkes-banten.png',

    'hero' => 'images/hero/gedung-direktorat-poltekkes-banten.webp',

    'hero_alt' => 'Gedung Direktorat Poltekkes Kemenkes Banten di Serang',

    /*
    |--------------------------------------------------------------------------
    | Admin PPID (credential dari .env — jangan hardcode password di source)
    |--------------------------------------------------------------------------
    */
    'admin' => [
        'email' => env('PPID_ADMIN_EMAIL'),
        'username' => env('PPID_ADMIN_USERNAME'),
        'password' => env('PPID_ADMIN_PASSWORD'),
        'name' => env('PPID_ADMIN_NAME', 'Admin PPID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Kontak (placeholder — ganti dengan data resmi)
    |--------------------------------------------------------------------------
    */
    'contact' => [
        // TODO: ganti dengan alamat resmi Direktorat Serang
        'address' => '[TODO: Alamat resmi Direktorat Poltekkes Kemenkes Banten, Serang]',
        // TODO: email resmi PPID
        'email' => '[TODO: email-ppid@poltekkesbanten.ac.id]',
        // TODO: telepon resmi
        'phone' => '[TODO: nomor telepon]',
        // TODO: WhatsApp layanan
        'whatsapp' => '[TODO: nomor WhatsApp]',
        // TODO: jam layanan resmi
        'service_hours' => 'Senin–Jumat, 08.00–15.30 WIB (CONTOH)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Peta (jangan hardcode koordinat fiktif)
    |--------------------------------------------------------------------------
    */
    'map' => [
        // TODO: isi embed OpenStreetMap/Google Maps resmi bila tersedia
        'embed_url' => null,
        // Pencarian peta publik (bukan koordinat karangan)
        'external_url' => 'https://www.openstreetmap.org/search?query=Poltekkes%20Kemenkes%20Banten%20Serang',
        'title' => 'Lokasi Direktorat Poltekkes Kemenkes Banten, Serang',
    ],

    /*
    |--------------------------------------------------------------------------
    | Kategori informasi publik (beranda)
    |--------------------------------------------------------------------------
    */
    'information_categories' => [
        [
            'slug' => 'berkala',
            'title' => 'Informasi Berkala',
            'excerpt' => 'Informasi yang wajib disediakan dan diumumkan secara berkala.',
            'icon' => 'calendar',
        ],
        [
            'slug' => 'serta-merta',
            'title' => 'Informasi Serta-Merta',
            'excerpt' => 'Informasi yang berkaitan dengan ancaman terhadap hajat hidup orang banyak dan ketertiban umum.',
            'icon' => 'alert',
        ],
        [
            'slug' => 'setiap-saat',
            'title' => 'Informasi Setiap Saat',
            'excerpt' => 'Informasi yang wajib tersedia dan dapat diakses oleh masyarakat setiap saat.',
            'icon' => 'clock',
        ],
        [
            'slug' => 'dikecualikan',
            'title' => 'Informasi Dikecualikan',
            'excerpt' => 'Informasi yang aksesnya dibatasi berdasarkan pengujian konsekuensi dan ketentuan peraturan perundang-undangan.',
            'icon' => 'lock',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Layanan / shortcut formulir (URL dari config; diganti DB di FASE 3–4)
    |--------------------------------------------------------------------------
    */
    'services' => [
        [
            'slug' => 'permohonan-informasi',
            'title' => 'Permohonan Informasi Publik',
            'excerpt' => 'Ajukan permohonan informasi publik secara resmi melalui PPID.',
            // Landing page tetap di /layanan/permohonan-informasi.
            // URL Google Form: information_request.external_url
            'external_url' => null,
            'is_featured' => true,
            'sort_order' => 1,
        ],
        [
            'slug' => 'pengajuan-keberatan',
            'title' => 'Pengajuan Keberatan',
            'excerpt' => 'Ajukan keberatan atas permohonan informasi yang ditolak atau tidak ditanggapi.',
            'external_url' => null,
            'is_featured' => true,
            'sort_order' => 2,
        ],
        [
            'slug' => 'pengaduan-penyalahgunaan-wewenang',
            'title' => 'Pengaduan Penyalahgunaan Wewenang',
            'excerpt' => 'Sampaikan pengaduan terkait dugaan penyalahgunaan wewenang.',
            'external_url' => null,
            'is_featured' => true,
            'sort_order' => 3,
        ],
        [
            'slug' => 'keluhan-pelayanan',
            'title' => 'Keluhan Pelayanan',
            'excerpt' => 'Sampaikan keluhan terkait kualitas layanan informasi publik.',
            'external_url' => null,
            'is_featured' => true,
            'sort_order' => 4,
        ],
        [
            'slug' => 'surat-keterangan',
            'title' => 'Surat Keterangan',
            'excerpt' => 'Informasi terkait penerbitan surat keterangan melalui PPID.',
            'external_url' => null,
            'is_featured' => false,
            'sort_order' => 5,
        ],
        [
            'slug' => 'alur-permohonan-informasi',
            'title' => 'Alur Permohonan Informasi',
            'excerpt' => 'Pelajari tahapan permohonan informasi publik dari pengajuan hingga jawaban.',
            'external_url' => null,
            'is_featured' => false,
            'sort_order' => 6,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Formulir permohonan informasi (Google Form eksternal)
    |--------------------------------------------------------------------------
    */
    'information_request' => [
        'external_url' => env(
            'PPID_INFORMATION_REQUEST_FORM_URL',
            'https://docs.google.com/forms/d/e/1FAIpQLSffivoxDcFT-iDrc8eb1GKvkqhx-LenWuaQkE826D5uSbOG3A/viewform'
        ),
        'email' => 'humas@poltekkesbanten.ac.id',
    ],

    /*
    |--------------------------------------------------------------------------
    | PPID Pelaksana / unit (khusus Poltekkes Kemenkes Banten)
    |--------------------------------------------------------------------------
    */
    'units' => [
        [
            'name' => 'Direktorat',
            'slug' => 'direktorat',
            'description' => 'PPID Pelaksana tingkat Direktorat Poltekkes Kemenkes Banten.',
            'url' => null,
            'is_active' => true,
            'sort_order' => 1,
        ],
        [
            'name' => 'Jurusan Keperawatan',
            'slug' => 'jurusan-keperawatan',
            'description' => 'Unit pelaksana informasi publik Jurusan Keperawatan.',
            'url' => null,
            'is_active' => true,
            'sort_order' => 2,
        ],
        [
            'name' => 'Jurusan Kebidanan',
            'slug' => 'jurusan-kebidanan',
            'description' => 'Unit pelaksana informasi publik Jurusan Kebidanan.',
            'url' => null,
            'is_active' => true,
            'sort_order' => 3,
        ],
        [
            'name' => 'Jurusan Teknologi Laboratorium Medis',
            'slug' => 'jurusan-teknologi-laboratorium-medis',
            'description' => 'Unit pelaksana informasi publik Jurusan Teknologi Laboratorium Medis.',
            'url' => null,
            'is_active' => true,
            'sort_order' => 4,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistik beranda (CONTOH — diganti data nyata / visitor_statistics di FASE 3)
    |--------------------------------------------------------------------------
    */
    'statistics' => [
        [
            'label' => 'Permohonan Informasi',
            'value' => 0,
            'suffix' => '',
            'note' => 'CONTOH',
        ],
        [
            'label' => 'Dokumen Publik',
            'value' => 0,
            'suffix' => '',
            'note' => 'CONTOH',
        ],
        [
            'label' => 'Pengunjung Tahun Ini',
            'value' => 0,
            'suffix' => '',
            'note' => 'CONTOH',
        ],
        [
            'label' => 'Unit PPID Pelaksana',
            'value' => 4,
            'suffix' => '',
            'note' => 'Direktorat + 3 jurusan',
        ],
    ],

    'campuses' => [
        [
            'name' => 'Direktorat / Kampus Serang',
            'address' => '[TODO: alamat Kampus Serang]',
        ],
        [
            'name' => 'Kampus Tangerang',
            'address' => '[TODO: alamat Kampus Tangerang]',
        ],
        [
            'name' => 'Kampus Rangkasbitung',
            'address' => '[TODO: alamat Kampus Rangkasbitung]',
        ],
    ],

    'social' => [
        // TODO: tautan media sosial resmi
        'instagram' => null,
        'facebook' => null,
        'youtube' => null,
        'x' => null,
    ],

    'important_links' => [
        [
            'label' => 'Kementerian Kesehatan RI',
            'url' => 'https://kemkes.go.id',
            'external' => true,
        ],
        [
            'label' => 'Poltekkes Kemenkes Banten',
            'url' => 'https://poltekkesbanten.ac.id',
            'external' => true,
        ],
        [
            'label' => 'PPID Kementerian Kesehatan',
            'url' => 'https://ppid.kemkes.go.id',
            'external' => true,
        ],
        [
            'label' => 'SPMB Poltekkes Kemenkes Banten',
            'url' => 'https://spmb.poltekkesbanten.ac.id',
            'external' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu navigasi utama (sementara; diganti database di FASE 3)
    |--------------------------------------------------------------------------
    */
    'navigation' => [
        [
            'label' => 'Beranda',
            'route' => 'home',
            'children' => [],
        ],
        [
            'label' => 'Profil',
            'route' => null,
            'children' => [
                ['label' => 'Profil Singkat PPID', 'route' => 'pages.show', 'params' => ['slug' => 'profil-singkat-ppid']],
                ['label' => 'Dasar Hukum dan SK PPID', 'route' => 'pages.show', 'params' => ['slug' => 'dasar-hukum-sk-ppid']],
                ['label' => 'Visi dan Misi', 'route' => 'pages.show', 'params' => ['slug' => 'visi-dan-misi']],
                ['label' => 'Tugas, Fungsi, dan Wewenang', 'route' => 'pages.show', 'params' => ['slug' => 'tugas-fungsi-wewenang']],
                ['label' => 'Struktur Organisasi', 'route' => 'pages.show', 'params' => ['slug' => 'struktur-organisasi']],
                ['label' => 'Maklumat Pelayanan', 'route' => 'profil.maklumat-pelayanan', 'params' => []],
            ],
        ],
        [
            'label' => 'Informasi Publik',
            'route' => null,
            'children' => [
                ['label' => 'Informasi Berkala', 'route' => 'information.category', 'params' => ['slug' => 'berkala']],
                ['label' => 'Informasi Serta-Merta', 'route' => 'information.category', 'params' => ['slug' => 'serta-merta']],
                ['label' => 'Informasi Setiap Saat', 'route' => 'information.category', 'params' => ['slug' => 'setiap-saat']],
                ['label' => 'Informasi Dikecualikan', 'route' => 'information.category', 'params' => ['slug' => 'dikecualikan']],
                ['label' => 'Daftar Informasi Publik', 'route' => 'information.index', 'params' => []],
                ['label' => 'Dokumen Publik', 'route' => 'documents.index', 'params' => []],
            ],
        ],
        [
            'label' => 'Layanan',
            'route' => null,
            'children' => [
                ['label' => 'Permohonan Informasi', 'route' => 'services.show', 'params' => ['slug' => 'permohonan-informasi']],
                ['label' => 'Pengajuan Keberatan', 'route' => 'services.show', 'params' => ['slug' => 'pengajuan-keberatan']],
                ['label' => 'Pengaduan Penyalahgunaan Wewenang', 'route' => 'services.show', 'params' => ['slug' => 'pengaduan-penyalahgunaan-wewenang']],
                ['label' => 'Keluhan Pelayanan', 'route' => 'services.show', 'params' => ['slug' => 'keluhan-pelayanan']],
                ['label' => 'Surat Keterangan', 'route' => 'services.show', 'params' => ['slug' => 'surat-keterangan']],
                ['label' => 'Alur Permohonan Informasi', 'route' => 'services.show', 'params' => ['slug' => 'alur-permohonan-informasi']],
            ],
        ],
        [
            'label' => 'Informasi',
            'route' => null,
            'children' => [
                ['label' => 'Akreditasi', 'route' => 'pages.show', 'params' => ['slug' => 'akreditasi']],
                ['label' => 'Kerja Sama', 'route' => 'pages.show', 'params' => ['slug' => 'kerja-sama']],
                ['label' => 'Pengadaan Barang dan Jasa', 'route' => 'pages.show', 'params' => ['slug' => 'pengadaan-barang-dan-jasa']],
                ['label' => 'Statistik Layanan Informasi', 'route' => 'pages.show', 'params' => ['slug' => 'statistik-layanan-informasi']],
            ],
        ],
        [
            'label' => 'Berita',
            'route' => 'posts.index',
            'children' => [],
        ],
        [
            'label' => 'Pengumuman',
            'route' => 'announcements.index',
            'children' => [],
        ],
        [
            'label' => 'FAQ',
            'route' => 'faqs.index',
            'children' => [],
        ],
        [
            'label' => 'Kontak',
            'route' => 'contact',
            'children' => [],
        ],
    ],
];
