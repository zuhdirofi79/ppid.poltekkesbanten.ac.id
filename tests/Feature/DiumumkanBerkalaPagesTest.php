<?php

namespace Tests\Feature;

use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DiumumkanBerkalaPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function navbar_shows_diumumkan_berkala_between_informasi_publik_and_layanan(): void
    {
        $this->seed(MenuSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Informasi Publik[\s\S]+?Diumumkan Berkala[\s\S]+?Layanan/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Informasi Publik[\s\S]+?Diumumkan Berkala[\s\S]+?Layanan/u',
            $html
        ));

        $this->assertStringContainsString(route('pages.show', ['slug' => 'alamat-kantor-satuan-kerja'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'visi-misi-satuan-kerja'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'tugas-fungsi-satuan-kerja'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'struktur-organisasi-satuan-kerja'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'profil-pimpinan-satuan-kerja'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'lhkpn-pimpinan-tertinggi'], false), $html);
        $this->assertStringContainsString(route('information.category', ['slug' => 'berkala'], false), $html);
        $this->assertStringContainsString(route('pages.show', ['slug' => 'program-kegiatan-prioritas-2026'], false), $html);
        $this->assertStringContainsString('Alamat Kantor Lengkap Satuan Kerja', $html);
        $this->assertStringContainsString('LHKPN Pimpinan Tertinggi', $html);
        $this->assertStringContainsString('Program atau Kegiatan Prioritas', $html);

        $this->assertSame(1, preg_match(
            '/Informasi Berkala[\s\S]+?Program atau Kegiatan Prioritas/u',
            $html
        ));
        $this->assertStringContainsString(route('pages.show', ['slug' => 'kebijakan-mengikat-bagi-publik'], false), $html);
        $this->assertStringContainsString('Kebijakan Mengikat bagi Publik', $html);
        $this->assertSame(1, preg_match(
            '/Program atau Kegiatan Prioritas[\s\S]+?Kebijakan Mengikat bagi Publik/u',
            $html
        ));
    }

    #[Test]
    public function alamat_kantor_page_renders_campus_cards(): void
    {
        $response = $this->get('/halaman/alamat-kantor-satuan-kerja');

        $response->assertOk();
        $response->assertSee('PPID-ALAMAT-KANTOR-SATUAN-KERJA-DEPLOY-20260923-A', false);
        $response->assertSee('Alamat Kantor Lengkap Satuan Kerja', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee('Direktorat / Kampus Serang', false);
        $response->assertSee('Jl. Syekh Moh. Nawawi Albantani No.12,', false);
        $response->assertSee('Kampus Tangerang', false);
        $response->assertSee('Persis samping RSUP Dr. Sitanala', false);
        $response->assertSee('Kampus Rangkasbitung', false);
        $response->assertSee('Jl. Raya Rangkasbitung Pandeglang No.421,', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function visi_misi_satuan_kerja_page_renders_all_units(): void
    {
        $response = $this->get('/halaman/visi-misi-satuan-kerja');

        $response->assertOk();
        $response->assertSee('PPID-VISI-MISI-SATUAN-KERJA-DEPLOY-20260923-A', false);
        $response->assertSee('Visi dan Misi Satuan Kerja', false);
        $response->assertSee('Direktorat Poltekkes Kemenkes Banten', false);
        $response->assertSee('Menjadi Perguruan Tinggi Kesehatan yang Unggul, Profesional dan Berkarakter pada Tahun 2026.', false);
        $response->assertSee('Jurusan Keperawatan', false);
        $response->assertSee('Menghasilkan tenaga keperawatan profesional yang berkarakter', false);
        $response->assertSee('Jurusan Kebidanan', false);
        $response->assertSee('Berdaya Saing Global Tahun 2044', false);
        $response->assertSee('Jurusan Teknologi Laboratorium Medis', false);
        $response->assertSee('pemeriksaan penyakit infeksi pada tahun 2026', false);
        $response->assertDontSee('ppid-vision-mission-page', false);
    }

    #[Test]
    public function tugas_fungsi_satuan_kerja_page_uses_accordion_cards(): void
    {
        $response = $this->get('/halaman/tugas-fungsi-satuan-kerja');

        $response->assertOk();
        $response->assertSee('PPID-TUGAS-FUNGSI-SATUAN-KERJA-DEPLOY-20260923-A', false);
        $response->assertSee('Tugas Fungsi Satuan Kerja', false);
        $response->assertSee('Direktorat Poltekkes Kemenkes Banten', false);
        $response->assertSee('Menyelenggarakan pendidikan tinggi vokasi dan profesi bidang kesehatan.', false);
        $response->assertSee('Penjaminan mutu.', false);
        $response->assertSee('Jurusan Keperawatan', false);
        $response->assertSee('Jurusan Kebidanan', false);
        $response->assertSee('Jurusan Teknologi Laboratorium Medis', false);
        $response->assertSee('<details', false);
        $response->assertDontSee('ppid-duty-page', false);
    }

    #[Test]
    public function placeholder_pages_are_published(): void
    {
        $this->get('/halaman/struktur-organisasi-satuan-kerja')
            ->assertOk()
            ->assertSee('Struktur Organisasi Satuan Kerja', false)
            ->assertSee('Konten halaman ini sedang disiapkan', false);

        $this->get('/halaman/profil-pimpinan-satuan-kerja')
            ->assertOk()
            ->assertSee('Profil Pimpinan Satuan Kerja', false)
            ->assertDontSee('Konten halaman ini sedang disiapkan', false);

        $this->get('/halaman/lhkpn-pimpinan-tertinggi')
            ->assertOk()
            ->assertSee('LHKPN Pimpinan Tertinggi', false)
            ->assertSee('Konten halaman ini sedang disiapkan', false);
    }

    #[Test]
    public function program_kegiatan_prioritas_page_renders_cms_content(): void
    {
        $response = $this->get('/halaman/program-kegiatan-prioritas-2026');

        $response->assertOk();
        $response->assertSee('PPID-PROGRAM-KEGIATAN-PRIORITAS-2026-DEPLOY-20260923-A', false);
        $response->assertSee('ppid-priority-page', false);
        $response->assertSee('Program atau Kegiatan Prioritas Tahun 2026', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee('Rencana Bisnis dan Anggaran (RBA) Poltekkes Banten Tahun 2026', false);
        $response->assertSee('Daftar Isian Pelaksanaan Anggaran (DIPA) Tahun 2026', false);
        $response->assertSee('Seleksi Kantor Akuntan Publik (KAP)', false);
        $response->assertSee('Dr. Dahrizal, S.Kp., M.P.H.', false);
        $response->assertSee('SMK Go Global', false);
        $response->assertSee('BINAR 2026', false);
        $response->assertSee('25-27 Agustus 2026', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function program_kegiatan_prioritas_body_is_admin_editable_and_preserved_on_reseed(): void
    {
        $page = \App\Models\Page::query()->where('slug', 'program-kegiatan-prioritas-2026')->firstOrFail();
        $page->update([
            'body' => '<section><h2>Program Uji Admin</h2><p>Konten diedit dari admin.</p></section>',
        ]);

        $this->seed(PageSeeder::class);

        $fresh = $page->fresh();
        $this->assertStringContainsString('Program Uji Admin', (string) $fresh->body);
        $this->assertStringContainsString('Konten diedit dari admin.', (string) $fresh->body);

        $admin = \App\Models\User::factory()->adminPpid()->create();
        $this->actingAs($admin)
            ->put(route('admin.pages.update', $fresh), [
                'title' => $fresh->title,
                'slug' => $fresh->slug,
                'excerpt' => $fresh->excerpt,
                'meta_title' => $fresh->meta_title,
                'meta_description' => $fresh->meta_description,
                'body' => '<section><h2>Program Baru Admin</h2><p>Ditambahkan via form CMS.</p></section>',
                'status' => 'published',
                'sort_order' => $fresh->sort_order,
            ])
            ->assertRedirect(route('admin.pages.index'));

        $this->get('/halaman/program-kegiatan-prioritas-2026')
            ->assertOk()
            ->assertSee('Program Baru Admin', false)
            ->assertSee('Ditambahkan via form CMS.', false)
            ->assertDontSee('Seleksi Kantor Akuntan Publik (KAP)', false);
    }

    #[Test]
    public function kebijakan_mengikat_bagi_publik_page_renders_pdf_viewer(): void
    {
        $response = $this->get('/halaman/kebijakan-mengikat-bagi-publik');

        $response->assertOk();
        $response->assertSee('PPID-KEBIJAKAN-MENGIKAT-BAGI-PUBLIK-DEPLOY-20261002-A', false);
        $response->assertSee('Kebijakan Mengikat bagi Publik', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee('Peraturan Menteri Kesehatan Republik Indonesia Nomor 13 Tahun 2026', false);
        $response->assertSee('Perubahan Kedua atas Peraturan Menteri Kesehatan Nomor 71 Tahun 2020', false);
        $response->assertSee('2026permenkes013.pdf', false);
        $response->assertSee('Buka PDF', false);
        $response->assertSee('Download PDF', false);
        $response->assertSee('ppid-kebijakan-viewer', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function informasi_berkala_halaman_redirects_to_existing_catalog(): void
    {
        $this->get('/halaman/informasi-berkala')
            ->assertRedirect('/informasi-publik/berkala');
    }

    #[Test]
    public function existing_profil_pages_remain_intact(): void
    {
        $this->get('/halaman/visi-dan-misi')->assertOk()->assertSee('ppid-vision-mission-page', false);
        $this->get('/halaman/tugas-fungsi-wewenang')->assertOk()->assertSee('ppid-duty-page', false);
        $this->get('/halaman/struktur-organisasi')->assertOk()->assertSee('ppid-org-structure-page', false);
    }
}
