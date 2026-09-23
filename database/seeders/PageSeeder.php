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
                'excerpt' => 'Struktur organisasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten periode 2025-2029.',
                'meta_title' => 'Struktur Organisasi PPID Poltekkes Kemenkes Banten',
                'meta_description' => 'Struktur organisasi PPID Poltekkes Kemenkes Banten periode 2025-2029.',
                'body' => '',
                'status' => 'published',
                'published_at' => now(),
                'sort_order' => 5,
                'created_by' => $admin?->id,
                'updated_by' => $admin?->id,
            ],
        );

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
}
