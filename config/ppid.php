<?php

/**
 * Konfigurasi identitas & navigasi publik PPID Poltekkes Kemenkes Banten.
 */

return [

    'max_upload_mb' => (int) env('PPID_MAX_UPLOAD_MB', 20),

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
    |
    | Jika password mengandung karakter #, tulis dengan tanda kutip di .env:
    | PPID_ADMIN_PASSWORD="nilai-password-dengan-#"
    |
    */
    'admin' => [
        'email' => env('PPID_ADMIN_EMAIL'),
        'username' => env('PPID_ADMIN_USERNAME'),
        'password' => env('PPID_ADMIN_PASSWORD'),
        'name' => env('PPID_ADMIN_NAME', 'Admin PPID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiga Admin PPID inti (protected) — identitas dari env, bukan ID database
    |--------------------------------------------------------------------------
    */
    'protected_admins' => [
        [
            'email' => env('PPID_ADMIN_EMAIL'),
            'username' => env('PPID_ADMIN_USERNAME'),
            'name' => env('PPID_ADMIN_NAME', 'Admin PPID'),
            'password' => env('PPID_ADMIN_PASSWORD'),
        ],
        [
            'email' => env('PPID_ADMIN2_EMAIL'),
            'username' => env('PPID_ADMIN2_USERNAME'),
            'name' => env('PPID_ADMIN2_NAME'),
            'password' => env('PPID_ADMIN2_PASSWORD'),
        ],
        [
            'email' => env('PPID_ADMIN3_EMAIL'),
            'username' => env('PPID_ADMIN3_USERNAME'),
            'name' => env('PPID_ADMIN3_NAME'),
            'password' => env('PPID_ADMIN3_PASSWORD'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kontak resmi PPID
    |--------------------------------------------------------------------------
    */
    'contact' => [
        'address' => 'Jl. Syekh Moh. Nawawi Albantani No.12, Banjaragung, Kec. Cipocok Jaya, Kota Serang, Banten 42122',
        'website' => 'https://poltekkesbanten.ac.id/',
        'website_label' => 'poltekkesbanten.ac.id',
        'email' => 'humas@poltekkesbanten.ac.id',
        'phone' => null,
        'whatsapp' => '+62 895-0839-4483 (Dias Arliadini)',
        'whatsapp_url' => 'https://wa.me/6289508394483',
        'service_hours' => 'Senin–Jumat, 08.00–15.30 WIB',
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
            // Landing page tetap di /layanan/pengajuan-keberatan.
            // URL Google Form: objection_request.external_url
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
        [
            'slug' => 'alur-pengajuan-keberatan',
            'title' => 'Alur Pengajuan Keberatan',
            'excerpt' => 'Pelajari alur dan tata cara pengajuan keberatan atas layanan informasi publik secara jelas, ringkas, dan mudah dipahami.',
            'external_url' => null,
            'is_featured' => false,
            'sort_order' => 7,
        ],
        [
            'slug' => 'alur-pengajuan-sengketa',
            'title' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran',
            'excerpt' => 'Informasi tata cara pengaduan dugaan penyalahgunaan wewenang, pelanggaran, pungutan liar, konflik kepentingan, gratifikasi, dan pelanggaran pelayanan publik di lingkungan Poltekkes Kemenkes Banten.',
            'external_url' => null,
            'is_featured' => false,
            'sort_order' => 8,
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
    | Formulir pengajuan keberatan (Google Form eksternal)
    |--------------------------------------------------------------------------
    */
    'objection_request' => [
        'external_url' => env(
            'PPID_OBJECTION_FORM_URL',
            'https://docs.google.com/forms/d/e/1FAIpQLScE8lH-ihPiy_FbzWJ6zQIk8eiFgd9bUeckJerQPsNHNIMbew/viewform'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Formulir pengaduan penyalahgunaan wewenang
    | Kosongkan sampai URL resmi tersedia. Jangan memakai form permohonan
    | informasi atau form pengajuan keberatan.
    |--------------------------------------------------------------------------
    */
    'complaint_request' => [
        'external_url' => env('PPID_COMPLAINT_FORM_URL'),
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
            'address' => 'Jl. Syekh Moh. Nawawi Albantani No.12, Banjaragung, Kec. Cipocok Jaya, Kota Serang, Banten 42122',
        ],
        [
            'name' => 'Kampus Tangerang',
            'address' => 'Jl. Dr. Sitanala (Komplek SPK), RT.002/RW.003, Karang Sari, Kec. Neglasari, Kota Tangerang, Banten 15121',
        ],
        [
            'name' => 'Kampus Rangkasbitung',
            'address' => 'Jl. Raya Rangkasbitung Pandeglang No.421, Kaduagung Tengah, Kec. Cibadak, Kabupaten Lebak, Banten 42317',
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
                ['label' => 'Maklumat Pelayanan', 'route' => 'profil.maklumat-pelayanan', 'params' => []],
                ['label' => 'Struktur Organisasi PPID', 'route' => 'pages.show', 'params' => ['slug' => 'struktur-organisasi']],
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
                ['label' => 'Daftar Informasi Publik', 'route' => 'pages.show', 'params' => ['slug' => 'daftar-informasi-publik']],
                ['label' => 'Dokumen Publik', 'route' => 'documents.index', 'params' => []],
            ],
        ],
        [
            'label' => 'Diumumkan Berkala',
            'route' => null,
            'children' => [
                ['label' => 'Alamat Kantor Lengkap Satuan Kerja', 'route' => 'pages.show', 'params' => ['slug' => 'alamat-kantor-satuan-kerja']],
                ['label' => 'Visi dan Misi Satuan Kerja', 'route' => 'pages.show', 'params' => ['slug' => 'visi-misi-satuan-kerja']],
                ['label' => 'Tugas Fungsi Satuan Kerja', 'route' => 'pages.show', 'params' => ['slug' => 'tugas-fungsi-satuan-kerja']],
                ['label' => 'Struktur Organisasi Satuan Kerja', 'route' => 'pages.show', 'params' => ['slug' => 'struktur-organisasi-satuan-kerja']],
                ['label' => 'Profil Pimpinan Satuan Kerja', 'route' => 'pages.show', 'params' => ['slug' => 'profil-pimpinan-satuan-kerja']],
                ['label' => 'LHKPN Pimpinan Tertinggi', 'route' => 'pages.show', 'params' => ['slug' => 'lhkpn-pimpinan-tertinggi']],
                ['label' => 'Informasi Berkala', 'route' => 'information.category', 'params' => ['slug' => 'berkala']],
                ['label' => 'Program atau Kegiatan Prioritas', 'route' => 'pages.show', 'params' => ['slug' => 'program-kegiatan-prioritas-2026']],
                ['label' => 'Kebijakan Mengikat bagi Publik', 'route' => 'pages.show', 'params' => ['slug' => 'kebijakan-mengikat-bagi-publik']],
                ['label' => 'Kalender Akademik', 'route' => 'pages.show', 'params' => ['slug' => 'kalender-akademik']],
                ['label' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang', 'route' => 'pages.show', 'params' => ['slug' => 'tata-cara-pengaduan-penyalahgunaan-wewenang']],
                ['label' => 'Ringkasan Laporan Layanan Informasi', 'route' => 'pages.show', 'params' => ['slug' => 'ringkasan-laporan-layanan-informasi']],
            ],
        ],
        [
            'label' => 'Layanan',
            'route' => null,
            'children' => [
                ['label' => 'Permohonan Informasi', 'route' => 'services.show', 'params' => ['slug' => 'permohonan-informasi']],
                ['label' => 'Pengajuan Keberatan', 'route' => 'services.show', 'params' => ['slug' => 'pengajuan-keberatan']],
                ['label' => 'Pengaduan Penyalahgunaan Wewenang', 'route' => 'services.show', 'params' => ['slug' => 'alur-pengajuan-sengketa']],
                ['label' => 'Keluhan Pelayanan', 'route' => 'services.show', 'params' => ['slug' => 'keluhan-pelayanan']],
                ['label' => 'Surat Keterangan', 'route' => 'services.show', 'params' => ['slug' => 'surat-keterangan']],
                ['label' => 'Alur Permohonan Informasi', 'route' => 'services.show', 'params' => ['slug' => 'alur-permohonan-informasi']],
                ['label' => 'Alur Pengajuan Keberatan', 'route' => 'services.show', 'params' => ['slug' => 'alur-pengajuan-keberatan']],
            ],
        ],
        [
            'label' => 'Informasi',
            'route' => null,
            'children' => [
                ['label' => 'Akreditasi', 'route' => 'pages.show', 'params' => ['slug' => 'akreditasi']],
                ['label' => 'Kerja Sama', 'route' => 'pages.show', 'params' => ['slug' => 'kerja-sama']],
                ['label' => 'Pengadaan Barang dan Jasa', 'route' => 'pages.show', 'params' => ['slug' => 'pengadaan-barang-dan-jasa']],
                ['label' => 'Statistik Layanan Informasi', 'route' => 'pages.show', 'params' => ['slug' => 'ringkasan-laporan-layanan-informasi']],
                ['label' => 'Information Desk', 'route' => 'pages.show', 'params' => ['slug' => 'information-desk']],
                ['label' => 'Statistik Keuangan', 'route' => 'pages.show', 'params' => ['slug' => 'statistik-keuangan']],
                [
                    'label' => 'Inventaris Barang Milik Negara',
                    'url' => 'https://bmn.poltekkesbanten.ac.id/',
                    'external' => true,
                    'title' => 'Sistem Informasi Barang Milik Negara Poltekkes Kemenkes Banten',
                    'aria_label' => 'Inventaris Barang Milik Negara Poltekkes Kemenkes Banten — buka Sistem BMN',
                ],
                ['label' => 'Daftar Informasi Publik', 'route' => 'pages.show', 'params' => ['slug' => 'daftar-informasi-publik']],
                ['label' => 'Rapat Koordinasi PPID', 'route' => 'pages.show', 'params' => ['slug' => 'rapat-koordinasi-ppid']],
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
            'label' => 'SOP & Layanan',
            'route' => null,
            'children' => [
                // Hanya resource publik yang sudah ada, terbit, dan dapat diakses.
                ['label' => 'Permohonan Informasi Publik', 'route' => 'services.show', 'params' => ['slug' => 'permohonan-informasi']],
                ['label' => 'Pengajuan Keberatan', 'route' => 'services.show', 'params' => ['slug' => 'pengajuan-keberatan']],
                ['label' => 'Maklumat Pelayanan', 'route' => 'profil.maklumat-pelayanan', 'params' => []],
                ['label' => 'Standar Operasional Prosedur Layanan Informasi Publik', 'route' => 'pages.show', 'params' => ['slug' => 'standar-operasional-prosedur-layanan-informasi-publik']],
                ['label' => 'Daftar Registrasi Pemohon', 'route' => 'services.show', 'params' => ['slug' => 'daftar-registrasi-pemohon']],
            ],
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
