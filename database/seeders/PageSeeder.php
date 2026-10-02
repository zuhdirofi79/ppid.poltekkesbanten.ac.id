<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->where('email', env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id'))
            ->orWhere('email', 'admin@ppid.local')
            ->first();

        Page::query()->updateOrCreate(
            ['slug' => 'profil-singkat-ppid'],
            [
                'title' => 'Profil Singkat PPID Poltekkes Kemenkes Banten',
                'excerpt' => 'Profil singkat Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten beserta dokumen lengkap dalam format PDF.',
                'meta_title' => 'Profil Singkat PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Mengenal profil singkat PPID Poltekkes Kemenkes Banten dan unduh dokumen profil lengkap.',
                'body' => HtmlSanitizer::clean($this->profilSingkatBody()),
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 1,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

        Page::query()->updateOrCreate(
            ['slug' => 'dasar-hukum-sk-ppid'],
            [
                'title' => 'Dasar Hukum dan SK PPID',
                'excerpt' => 'Keputusan Direktur Poltekkes Kemenkes Banten tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025.',
                'meta_title' => 'Dasar Hukum dan SK PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Dokumen Keputusan Direktur Poltekkes Kemenkes Banten tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 2,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

        Page::query()->updateOrCreate(
            ['slug' => 'visi-dan-misi'],
            [
                'title' => 'Visi dan Misi PPID Poltekkes Kemenkes Banten',
                'excerpt' => 'Visi dan misi pelayanan informasi publik PPID Poltekkes Kemenkes Banten.',
                'meta_title' => 'Visi dan Misi PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Visi dan misi PPID Poltekkes Kemenkes Banten dalam mewujudkan layanan informasi publik yang transparan, akuntabel, prima, dan berbasis digital.',
                'body' => HtmlSanitizer::clean($this->visiDanMisiBody()),
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 3,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

        Page::query()->updateOrCreate(
            ['slug' => 'tugas-fungsi-wewenang'],
            [
                'title' => 'Tugas, Fungsi, dan Wewenang PPID',
                'excerpt' => 'Tugas dan fungsi PPID Poltekkes Kemenkes Banten dalam pengelolaan, pelayanan, serta keterbukaan informasi publik.',
                'meta_title' => 'Tugas, Fungsi, dan Wewenang PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Informasi mengenai tugas dan fungsi PPID Poltekkes Kemenkes Banten dalam pelayanan serta pengelolaan keterbukaan informasi publik.',
                'body' => HtmlSanitizer::clean($this->tugasFungsiWewenangBody()),
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 4,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

        Page::query()->updateOrCreate(
            ['slug' => 'struktur-organisasi'],
            [
                'title' => 'Struktur Organisasi PPID Poltekkes Kemenkes Banten',
                'excerpt' => 'Struktur organisasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten.',
                'meta_title' => 'Struktur Organisasi PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Struktur organisasi Pejabat Pengelola Informasi dan Dokumentasi PPID Poltekkes Kemenkes Banten.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 5,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

        $this->seedDiumumkanBerkalaPages($admin?->id);

        Page::query()->updateOrCreate(
            ['slug' => 'akreditasi'],
            [
                'title' => 'Akreditasi Poltekkes Kemenkes Banten',
                'excerpt' => 'Informasi akreditasi institusi dan program studi Poltekkes Kemenkes Banten.',
                'meta_title' => 'Akreditasi Poltekkes Kemenkes Banten',
                'meta_description' => 'Informasi akreditasi institusi dan program studi Poltekkes Kemenkes Banten.',
                'body' => HtmlSanitizer::clean($this->akreditasiBody()),
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 20,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );
    }

    private function seedDiumumkanBerkalaPages(?int $adminId): void
    {
        $pages = [
            [
                'slug' => 'alamat-kantor-satuan-kerja',
                'title' => 'Alamat Kantor Lengkap Satuan Kerja',
                'excerpt' => 'Alamat lengkap Direktorat dan kampus Poltekkes Kemenkes Banten.',
                'meta_description' => 'Alamat lengkap Direktorat / Kampus Serang, Kampus Tangerang, dan Kampus Rangkasbitung Poltekkes Kemenkes Banten.',
                'body' => '',
                'sort_order' => 30,
            ],
            [
                'slug' => 'visi-misi-satuan-kerja',
                'title' => 'Visi dan Misi Satuan Kerja',
                'excerpt' => 'Visi dan misi Direktorat serta jurusan di Poltekkes Kemenkes Banten.',
                'meta_description' => 'Visi dan misi satuan kerja Poltekkes Kemenkes Banten meliputi Direktorat, Keperawatan, Kebidanan, dan Teknologi Laboratorium Medis.',
                'body' => '',
                'sort_order' => 31,
            ],
            [
                'slug' => 'tugas-fungsi-satuan-kerja',
                'title' => 'Tugas Fungsi Satuan Kerja',
                'excerpt' => 'Tugas pokok dan fungsi Direktorat serta jurusan Poltekkes Kemenkes Banten.',
                'meta_description' => 'Tugas pokok dan fungsi satuan kerja Poltekkes Kemenkes Banten dalam penyelenggaraan pendidikan tinggi kesehatan.',
                'body' => '',
                'sort_order' => 32,
            ],
            [
                'slug' => 'struktur-organisasi-satuan-kerja',
                'title' => 'Struktur Organisasi Satuan Kerja',
                'excerpt' => 'Struktur organisasi satuan kerja Poltekkes Kemenkes Banten.',
                'meta_description' => 'Struktur organisasi satuan kerja Poltekkes Kemenkes Banten. Konten lengkap sedang disiapkan.',
                'body' => HtmlSanitizer::clean($this->placeholderBody('Struktur Organisasi Satuan Kerja')),
                'sort_order' => 33,
            ],
            [
                'slug' => 'lhkpn-pimpinan-tertinggi',
                'title' => 'LHKPN Pimpinan Tertinggi',
                'excerpt' => 'Laporan Harta Kekayaan Penyelenggara Negara (LHKPN) pimpinan tertinggi.',
                'meta_description' => 'Informasi LHKPN pimpinan tertinggi Poltekkes Kemenkes Banten. Konten lengkap sedang disiapkan.',
                'body' => HtmlSanitizer::clean($this->placeholderBody('LHKPN Pimpinan Tertinggi')),
                'sort_order' => 35,
            ],
        ];

        foreach ($pages as $page) {
            Page::query()->updateOrCreate(
                ['slug' => $page['slug']],
                [
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'meta_title' => $page['title'].' — PPID Poltekkes Kemenkes Banten',
                    'meta_description' => $page['meta_description'],
                    'body' => $page['body'],
                    'status' => 'published',
                    'published_at' => now(),
                    'sort_order' => $page['sort_order'],
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ],
            );
        }

        $this->seedProfilPimpinanPage($adminId);
        $this->seedProgramKegiatanPrioritasPage($adminId);
        $this->seedKebijakanMengikatBagiPublikPage($adminId);
        $this->call(KalenderAkademikPageSeeder::class);
        $this->call(SopLayananInformasiPageSeeder::class);
    }

    private function seedProfilPimpinanPage(?int $adminId): void
    {
        $excerpt = 'Profil pimpinan Poltekkes Kemenkes Banten beserta riwayat pendidikan dan riwayat pekerjaan.';
        $meta = 'Profil pimpinan Poltekkes Kemenkes Banten yang memuat nama, jabatan, riwayat pendidikan, dan riwayat pekerjaan.';
        $existing = Page::withTrashed()->where('slug', 'profil-pimpinan-satuan-kerja')->first();

        if (! $existing) {
            Page::query()->create([
                'title' => 'Profil Pimpinan Satuan Kerja',
                'slug' => 'profil-pimpinan-satuan-kerja',
                'excerpt' => $excerpt,
                'meta_title' => 'Profil Pimpinan Satuan Kerja',
                'meta_description' => $meta,
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 34,
                'created_by' => $adminId,
                'updated_by' => $adminId,
            ]);

            return;
        }

        $updates = [];
        if (str_contains((string) $existing->body, 'sedang disiapkan')) {
            $updates['body'] = '';
        }
        if (blank($existing->excerpt) || str_contains((string) $existing->excerpt, 'sedang disiapkan')) {
            $updates['excerpt'] = $excerpt;
        }
        if (blank($existing->meta_description) || str_contains((string) $existing->meta_description, 'sedang disiapkan')) {
            $updates['meta_description'] = $meta;
            $updates['meta_title'] = 'Profil Pimpinan Satuan Kerja';
        }
        if ($existing->trashed()) {
            $existing->restore();
        }
        if ($updates !== []) {
            $existing->update($updates);
        }
    }

    private function seedProgramKegiatanPrioritasPage(?int $adminId): void
    {
        $slug = 'program-kegiatan-prioritas-2026';
        $defaults = [
            'title' => 'Program atau Kegiatan Prioritas Tahun 2026',
            'excerpt' => 'Informasi program dan kegiatan prioritas Poltekkes Kemenkes Banten tahun anggaran 2026.',
            'meta_title' => 'Program atau Kegiatan Prioritas Tahun 2026 — PPID Poltekkes Kemenkes Banten',
            'meta_description' => 'Program atau kegiatan prioritas Poltekkes Kemenkes Banten tahun 2026, termasuk penanggung jawab, target, jadwal, dan sumber anggaran.',
            'body' => HtmlSanitizer::clean($this->programKegiatanPrioritas2026Body()),
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 36,
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ];

        $page = Page::query()->firstOrCreate(['slug' => $slug], $defaults);

        if ($page->wasRecentlyCreated) {
            return;
        }

        // Jangan timpa body yang sudah diedit admin; perbarui metadata ringan saja.
        $page->fill([
            'title' => $defaults['title'],
            'excerpt' => $defaults['excerpt'],
            'meta_title' => $defaults['meta_title'],
            'meta_description' => $defaults['meta_description'],
            'status' => 'published',
            'published_at' => $page->published_at ?? now(),
            'sort_order' => 36,
            'updated_by' => $adminId,
        ]);

        if (! filled(trim((string) $page->body))) {
            $page->body = $defaults['body'];
        }

        $page->save();
    }

    private function programKegiatanPrioritas2026Body(): string
    {
        return <<<'HTML'
<section>
    <p>Informasi detail mengenai nama program/kegiatan prioritas, penanggung jawab, target, jadwal pelaksanaan, serta sumber dan besaran anggaran spesifik untuk Poltekkes Kemenkes Banten tahun anggaran 2026 secara menyeluruh belum dipublikasikan secara terbuka dalam satu dokumen publik terintegrasi.</p>
    <p>Rincian komponen anggaran makro tersebut umumnya tercantum dalam dokumen internal:</p>
    <ul>
        <li>Rencana Bisnis dan Anggaran (RBA) Poltekkes Banten Tahun 2026</li>
        <li>Daftar Isian Pelaksanaan Anggaran (DIPA) Tahun 2026</li>
    </ul>
</section>

<section>
    <h2>1. Seleksi Kantor Akuntan Publik (KAP) untuk Audit Laporan Keuangan BLU 2026</h2>
    <p><strong>Nama Kegiatan:</strong> Beauty Contest / Seleksi Kantor Akuntan Publik (KAP) Audit Eksternal Laporan Keuangan Badan Layanan Umum (BLU) TA 2026</p>
    <p><strong>Penanggung Jawab:</strong> Direktur Poltekkes Kemenkes Banten Dr. Dahrizal, S.Kp., M.P.H. dan Tim Seleksi Beauty Contest</p>
    <p><strong>Target:</strong> Terpilihnya KAP yang memenuhi kualifikasi untuk mengaudit laporan keuangan BLU.</p>
    <p><strong>Jadwal:</strong> September 2026</p>
    <p><strong>Sumber Anggaran:</strong> Anggaran BLU Poltekkes Banten TA 2026</p>
</section>

<section>
    <h2>2. Pelaksanaan Kerja Sama Program Internasional SMK Go Global</h2>
    <p><strong>Nama Kegiatan:</strong> Pengelolaan program kesiapan kerja internasional dan pelatihan bahasa melalui LPK Poltekkes Banten.</p>
    <p><strong>Pelaksana:</strong> LPK Poltekkes Kemenkes Banten bekerja sama dengan BP3MI Banten.</p>
    <p><strong>Target:</strong> Penyerapan alumni ke pasar kerja internasional.</p>
    <p><strong>Jadwal:</strong> Semester berjalan tahun 2026.</p>
    <p><strong>Sumber Anggaran:</strong> Anggaran Kerja Sama Operasional / Dana Masyarakat / BLU.</p>
</section>

<section>
    <h2>3. Penyelenggaraan Konferensi Internasional BINAR 2026</h2>
    <p><strong>Nama:</strong> 2nd Banten International Conference on Health Advancement and Research (BINAR) 2026</p>
    <p><strong>Pelaksana:</strong> Unit Penelitian dan Pengabdian Masyarakat (PPM)</p>
    <p><strong>Target:</strong> Diseminasi inovasi pelayanan kesehatan publik global.</p>
    <p><strong>Jadwal:</strong> 25-27 Agustus 2026</p>
    <p><strong>Sumber:</strong> DIPA Poltekkes Kemenkes Banten 2026</p>
</section>
HTML;
    }

    private function placeholderBody(string $topic): string
    {
        return <<<HTML
<section>
    <h2>{$topic}</h2>
    <p>Konten halaman ini sedang disiapkan dan akan ditampilkan setelah data resmi diverifikasi oleh Admin PPID.</p>
</section>
HTML;
    }

    private function visiDanMisiBody(): string
    {
        return <<<'HTML'
<section>
    <h2>Visi</h2>
    <blockquote>
        <p>Terwujudnya pelayanan informasi yang transparan, akuntabel, dan prima demi terselenggaranya pendidikan tinggi yang menghasilkan tenaga kesehatan berbasis kearifan lokal dan diakui internasional sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.</p>
    </blockquote>
</section>
<section>
    <h2>Misi</h2>
    <ol>
        <li>Menyediakan informasi publik yang akurat dan dapat dipertanggungjawabkan;</li>
        <li>Membangun dan mengembangkan sistem penyediaan dan layanan informasi berbasis digital;</li>
        <li>Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi;</li>
        <li>Memanfaatkan teknologi informasi dalam memberikan layanan informasi publik kepada masyarakat dan mendukung pengelolaan keterbukaan informasi publik;</li>
        <li>Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi.</li>
    </ol>
</section>
HTML;
    }

    private function profilSingkatBody(): string
    {
        return <<<'HTML'
<section>
    <h2>Profil Singkat</h2>
    <p>Mengacu pada Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik, Poltekkes Kemenkes Banten berkomitmen mewujudkan layanan informasi publik yang transparan, akuntabel, cepat, tepat, dan mudah diakses.</p>
    <p>Untuk mendukung pelaksanaan layanan tersebut, Direktur Poltekkes Kemenkes Banten telah menunjuk pegawai di lingkungan institusi sebagai pengelola Pejabat Pengelola Informasi dan Dokumentasi (PPID).</p>
    <p>PPID Poltekkes Kemenkes Banten berfungsi mengelola, mendokumentasikan, menyediakan, serta menyampaikan informasi publik yang berada dalam penguasaan Poltekkes Kemenkes Banten.</p>
    <p>Keberadaan PPID diharapkan memberikan kemudahan kepada masyarakat dalam memperoleh informasi secara cepat, tepat, efektif, dan sesuai dengan ketentuan peraturan perundang-undangan.</p>
</section>
HTML;
    }

    /**
     * Cadangan konten CMS (Tugas + Fungsi). Partial Blade merender tampilan khusus;
     * bagian Wewenang sengaja tidak disertakan sampai materi resmi tersedia.
     */
    private function tugasFungsiWewenangBody(): string
    {
        return <<<'HTML'
<section>
    <h2>Tugas PPID</h2>
    <ol>
        <li>PPID Poltekkes Kemenkes Banten bertugas menerima, memeriksa, dan menindaklanjuti pengaduan masyarakat, permintaan layanan informasi, serta sengketa informasi publik.</li>
        <li>PPID Poltekkes Kemenkes Banten bertugas sebagai produsen dan pengendali pembuatan konten atau berita tentang penyelenggaraan dan pengembangan pendidikan di Poltekkes Kemenkes Banten, yang meliputi pendidikan Diploma Tiga, Sarjana Terapan, dan Pendidikan Profesi Ners.</li>
        <li>PPID Poltekkes Kemenkes Banten bertugas sebagai produsen dan pengendali pembuatan konten atau berita tentang pengembangan Tri Dharma Perguruan Tinggi melalui media sosial Poltekkes Kemenkes Banten.</li>
        <li>PPID Poltekkes Kemenkes Banten bertugas melaksanakan pembinaan, pengawasan, dan evaluasi terhadap pelaksanaan pengelolaan informasi publik.</li>
        <li>PPID Poltekkes Kemenkes Banten bertugas menyusun laporan semester dan tahunan pengelolaan informasi.</li>
        <li>PPID Poltekkes Kemenkes Banten bertugas melakukan penyusunan dan pemutakhiran Daftar Informasi Publik.</li>
    </ol>
</section>
<section>
    <h2>Fungsi PPID</h2>
    <p>PPID Poltekkes Kemenkes Banten berfungsi menjalankan Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik serta menetapkan standar operasional untuk mengelola layanan informasi publik.</p>
</section>
HTML;
    }

    private function akreditasiBody(): string
    {
        return <<<'HTML'
<section>
    <h2>Informasi Akreditasi</h2>
    <p>Informasi akreditasi institusi dan program studi Poltekkes Kemenkes Banten sedang dalam proses pemutakhiran.</p>
    <p>Dokumen dan status akreditasi resmi akan ditampilkan pada halaman ini setelah data diverifikasi oleh Admin PPID.</p>
</section>
HTML;
    }

    private function seedKebijakanMengikatBagiPublikPage(?int $adminId): void
    {
        $slug = 'kebijakan-mengikat-bagi-publik';
        $defaults = [
            'title' => 'Kebijakan Mengikat bagi Publik',
            'excerpt' => 'Peraturan, keputusan, dan/atau kebijakan yang mengikat bagi publik serta berkaitan dengan penyelenggaraan Politeknik Kesehatan Kementerian Kesehatan.',
            'meta_title' => 'Kebijakan Mengikat bagi Publik | PPID Poltekkes Kemenkes Banten',
            'meta_description' => 'Peraturan, keputusan, dan kebijakan yang mengikat bagi publik pada Poltekkes Kemenkes Banten.',
            'body' => '',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 37,
            'created_by' => $adminId,
            'updated_by' => $adminId,
        ];

        Page::query()->firstOrCreate(['slug' => $slug], $defaults);
    }
}
