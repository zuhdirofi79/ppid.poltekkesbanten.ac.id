<?php

namespace Tests\Feature;

use App\Models\AcademicCalendar;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\PublicNavigation;
use Database\Seeders\AcademicCalendarSeeder;
use Database\Seeders\EnsureKalenderAkademikMenuItemSeeder;
use Database\Seeders\KalenderAkademikPageSeeder;
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
        $this->assertStringContainsString(route('pages.show', ['slug' => 'kalender-akademik'], false), $html);
        $this->assertStringContainsString('Kalender Akademik', $html);
        $this->assertSame(1, preg_match(
            '/Kebijakan Mengikat bagi Publik[\s\S]+?Kalender Akademik/u',
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
            ->assertSee('PPID-LHKPN-PIMPINAN-V1', false)
            ->assertDontSee('Konten halaman ini sedang disiapkan', false);
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
    public function kalender_akademik_page_renders_with_year_selector(): void
    {
        $this->seed(\Database\Seeders\AcademicCalendarSeeder::class);

        $response = $this->get('/halaman/kalender-akademik');

        $response->assertOk();
        $response->assertSee('PPID-KALENDER-AKADEMIK-V1', false);
        $response->assertSee('PPID-KALENDER-AKADEMIK-DEPLOY-20261002-B', false);
        $response->assertSee('Kalender Akademik', false);
        $response->assertSee('berdasarkan tahun akademik', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee('2026', false);
        // 2026 uses PNG, NOT PDF
        $response->assertSee('Poltekkes Banten Academic Calendar 2026_2027.png', false);
        $response->assertSee('Lihat Dokumen', false);
        $response->assertSee('Unduh Dokumen', false);
        // Should render image viewer, not PDF viewer
        $response->assertSee('ppid-kalender-image-viewer', false);
        $response->assertSee('<img', false);
        // Should NOT reference old PDF
        $response->assertDontSee('draft-kalender-akademik-2026-occasionally.pdf', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function kalender_akademik_page_shows_specific_year(): void
    {
        $this->seed(\Database\Seeders\AcademicCalendarSeeder::class);

        $response = $this->get('/halaman/kalender-akademik?tahun=2026');

        $response->assertOk();
        $response->assertSee('Kalender Akademik Tahun Akademik 2026/2027', false);
        $response->assertSee('Poltekkes Banten Academic Calendar 2026_2027.png', false);
        // Should NOT reference old PDF
        $response->assertDontSee('draft-kalender-akademik-2026-occasionally.pdf', false);
    }

    #[Test]
    public function kalender_akademik_2026_renders_as_image_not_pdf(): void
    {
        $this->seed(\Database\Seeders\AcademicCalendarSeeder::class);

        $response = $this->get('/halaman/kalender-akademik?tahun=2026');
        $html = $response->getContent();

        $response->assertOk();
        // Must have <img> tag for PNG
        $this->assertStringContainsString('<img', $html);
        // Must have the image viewer section element
        $this->assertMatchesRegularExpression('/<section[^>]*class="ppid-kalender-image-viewer"/', $html);
        // Must NOT have PDF iframe section element (note: CSS definitions may still exist)
        $this->assertDoesNotMatchRegularExpression('/<section[^>]*class="ppid-kalender-pdf-viewer"/', $html);
        // Must NOT have iframe element for PDF
        $this->assertDoesNotMatchRegularExpression('/<iframe[^>]*ppid-kalender-pdf-frame/', $html);
        // Must have alt text
        $this->assertStringContainsString('alt="Kalender Akademik Poltekkes Kemenkes Banten Tahun Akademik 2026/2027"', $html);
    }

    #[Test]
    public function kalender_akademik_model_correctly_detects_image_format(): void
    {
        $this->seed(\Database\Seeders\AcademicCalendarSeeder::class);

        $calendar = \App\Models\AcademicCalendar::query()->where('year', 2026)->first();

        $this->assertTrue($calendar->isImage());
        $this->assertFalse($calendar->isPdf());
        $this->assertSame('png', $calendar->getExtension());
        $this->assertSame('image/png', $calendar->getMimeType());
        $this->assertSame('PNG', $calendar->getFormatLabel());
        $this->assertSame('2026/2027', $calendar->getYearLabel());
    }

    #[Test]
    public function kalender_akademik_page_handles_unavailable_year_gracefully(): void
    {
        $this->seed(\Database\Seeders\AcademicCalendarSeeder::class);

        $response = $this->get('/halaman/kalender-akademik?tahun=2099');

        $response->assertOk();
        $response->assertSee('Kalender Akademik Tahun 2099 Belum Tersedia', false);
        $response->assertDontSee('Poltekkes Banten Academic Calendar 2026_2027.png', false);
        $response->assertDontSee('draft-kalender-akademik-2026-occasionally.pdf', false);
    }

    #[Test]
    public function kalender_akademik_returns_not_found_without_page_record(): void
    {
        Page::query()->where('slug', 'kalender-akademik')->delete();

        $this->get('/halaman/kalender-akademik')->assertNotFound();
        $this->get('/halaman/kalender-akademik?tahun=2026')->assertNotFound();
    }

    #[Test]
    public function kalender_page_seeder_does_not_overwrite_admin_excerpt(): void
    {
        $page = Page::query()->where('slug', 'kalender-akademik')->firstOrFail();
        $page->update(['excerpt' => 'Diubah oleh admin PPID.']);

        $this->seed(KalenderAkademikPageSeeder::class);

        $this->assertSame('Diubah oleh admin PPID.', $page->fresh()->excerpt);
        $this->assertSame(1, Page::query()->where('slug', 'kalender-akademik')->count());
    }

    #[Test]
    public function academic_calendar_seeder_replaces_legacy_pdf_once(): void
    {
        $legacy = AcademicCalendar::query()->create([
            'year' => 2026,
            'title' => 'Judul yang diedit admin',
            'description' => 'Deskripsi admin.',
            'source_type' => AcademicCalendar::SOURCE_PUBLIC_ASSET,
            'public_asset_path' => 'docs/draft-kalender-akademik-2026-occasionally.pdf',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->seed(AcademicCalendarSeeder::class);

        $legacy->refresh();
        $this->assertSame('docs/Poltekkes Banten Academic Calendar 2026_2027.png', $legacy->public_asset_path);
        $this->assertSame('image/png', $legacy->mime_type);
        $this->assertSame('png', $legacy->extension);
        $this->assertSame('Judul yang diedit admin', $legacy->title);

        $legacy->update([
            'public_asset_path' => 'docs/custom-admin.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
        ]);

        $this->seed(AcademicCalendarSeeder::class);

        $this->assertSame('docs/custom-admin.png', $legacy->fresh()->public_asset_path);
        $this->assertSame(1, AcademicCalendar::query()->where('year', 2026)->count());
    }

    #[Test]
    public function database_navigation_gains_kalender_item_without_rebuilding_the_menu(): void
    {
        $this->seed(MenuSeeder::class);

        \App\Models\MenuItem::query()->where('label', 'Kalender Akademik')->delete();

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();
        $this->assertSame('database', SiteSetting::getValue('navigation.public_source'));

        $before = $this->get('/')->assertOk()->getContent();
        $this->assertDoesNotMatchRegularExpression(
            '/href="[^"]*\/halaman\/kalender-akademik"/',
            $before
        );

        $this->seed(EnsureKalenderAkademikMenuItemSeeder::class);
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString(route('pages.show', ['slug' => 'kalender-akademik'], false), $html);
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Kebijakan Mengikat bagi Publik[\s\S]+?Kalender Akademik/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Kebijakan Mengikat bagi Publik[\s\S]+?Kalender Akademik/u',
            $html
        ));
    }

    #[Test]
    public function kalender_akademik_alias_redirects_to_main_page(): void
    {
        $response = $this->get('/halaman/kalender-akademik-2026');

        $response->assertRedirect('/halaman/kalender-akademik?tahun=2026');
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
