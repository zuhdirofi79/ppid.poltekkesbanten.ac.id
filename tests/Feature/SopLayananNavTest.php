<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PublicDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SopLayananNavTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function homepage_shows_sop_layanan_between_pengumuman_and_faq(): void
    {
        $response = $this->get('/');
        $response->assertOk();
        $response->assertSee('PPID-SOP-LAYANAN-NAV-DEPLOY-20260916-A', false);
        $response->assertSee('SOP & Layanan');
        $response->assertSee('Pengumuman');
        $response->assertSee('FAQ');
        $response->assertSee('Beranda');
        $response->assertSee('Profil');
        $response->assertSee('Informasi Publik');
        $response->assertSee('Layanan');
        $response->assertSee('Berita');
        $response->assertSee('Kontak');

        $html = $response->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Pengumuman[\s\S]+?SOP &amp; Layanan[\s\S]+?FAQ[\s\S]+?Kontak/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Pengumuman[\s\S]+?SOP &amp; Layanan[\s\S]+?FAQ[\s\S]+?Kontak/u',
            $html
        ));

        $this->assertSame(2, substr_count($html, 'SOP &amp; Layanan'));
    }

    #[Test]
    public function sop_dropdown_contains_only_existing_public_resources(): void
    {
        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee(route('services.show', ['slug' => 'permohonan-informasi'], false), false);
        $home->assertSee(route('services.show', ['slug' => 'pengajuan-keberatan'], false), false);
        $home->assertSee(route('profil.maklumat-pelayanan', [], false), false);
        $home->assertSee('Permohonan Informasi Publik');
        $home->assertSee('Pengajuan Keberatan');
        $home->assertSee('Maklumat Pelayanan');
        $home->assertSee('Standar Operasional Prosedur Layanan Informasi Publik');
        $home->assertSee(route('pages.show', ['slug' => 'standar-operasional-prosedur-layanan-informasi-publik'], false), false);
        $html = $home->getContent();
        $this->assertSame(1, preg_match(
            '/SOP &amp; Layanan[\s\S]{0,4000}?Permohonan Informasi Publik[\s\S]+?Pengajuan Keberatan[\s\S]+?Maklumat Pelayanan[\s\S]+?Standar Operasional Prosedur Layanan Informasi Publik/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?SOP &amp; Layanan[\s\S]+?Maklumat Pelayanan[\s\S]+?Standar Operasional Prosedur Layanan Informasi Publik/u',
            $html
        ));
        $home->assertDontSee('SOP Pelayanan Informasi', false);
        $home->assertDontSee('Tata Cara Permohonan Informasi', false);
        $home->assertDontSee('Tata Cara Pengajuan Keberatan', false);
        $home->assertDontSee('Jadwal Pelayanan Informasi', false);
        $home->assertDontSee('Uji Akses', false);

        $this->get('/layanan/permohonan-informasi')->assertOk();
        $this->get('/layanan/pengajuan-keberatan')->assertOk();
        $this->get('/profil/maklumat-pelayanan')->assertOk();
    }

    #[Test]
    public function sop_dropdown_markup_is_accessible_and_has_unique_ids(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('aria-haspopup="true"', $html);
        $this->assertStringContainsString('aria-controls="nav-dropdown-', $html);
        $this->assertStringContainsString('id="nav-dropdown-', $html);
        $this->assertStringContainsString('aria-controls="nav-mobile-', $html);
        $this->assertStringContainsString('id="nav-mobile-', $html);

        preg_match_all('/id="(nav-(?:dropdown|mobile)-[^"]+)"/', $html, $matches);
        $ids = $matches[1] ?? [];
        $this->assertNotEmpty($ids);
        $this->assertSame($ids, array_values(array_unique($ids)));
    }

    #[Test]
    public function sop_layanan_is_active_only_on_its_child_routes(): void
    {
        $permohonan = $this->get('/layanan/permohonan-informasi')->assertOk()->getContent();
        $this->assertSopMenuCurrent($permohonan, true);

        $keberatan = $this->get('/layanan/pengajuan-keberatan')->assertOk()->getContent();
        $this->assertSopMenuCurrent($keberatan, true);

        $maklumat = $this->get('/profil/maklumat-pelayanan')->assertOk()->getContent();
        $this->assertSopMenuCurrent($maklumat, true);

        $sop = $this->get('/halaman/standar-operasional-prosedur-layanan-informasi-publik')->assertOk()->getContent();
        $this->assertSopMenuCurrent($sop, true);

        $otherService = $this->get('/layanan/surat-keterangan')->assertOk()->getContent();
        $this->assertSopMenuCurrent($otherService, false);

        $faq = $this->get('/faq')->assertOk()->getContent();
        $this->assertSopMenuCurrent($faq, false);
    }

    #[Test]
    public function draft_pages_and_missing_documents_are_not_added_to_dropdown(): void
    {
        Page::query()->create([
            'title' => 'SOP Pelayanan Informasi Publik Draft',
            'slug' => 'sop-pelayanan-informasi',
            'status' => 'draft',
            'body' => '<p>Draft</p>',
        ]);

        PublicDocument::query()->create([
            'title' => 'SOP PPID Belum Tersedia',
            'slug' => 'sop-ppid-belum-tersedia',
            'status' => 'draft',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => null,
        ]);

        $home = $this->get('/')->assertOk();
        $home->assertDontSee('/halaman/sop-pelayanan-informasi', false);
        $home->assertDontSee('SOP Pelayanan Informasi Publik Draft', false);
        $home->assertDontSee('SOP PPID Belum Tersedia', false);
        $home->assertDontSee(route('documents.download', 'sop-ppid-belum-tersedia', false), false);
    }

    #[Test]
    public function global_search_still_works_with_new_nav(): void
    {
        $this->get('/cari?q=keberatan')
            ->assertOk()
            ->assertSee('SOP & Layanan')
            ->assertSee('Pengajuan Keberatan', false);
    }

    private function assertSopMenuCurrent(string $html, bool $expected): void
    {
        preg_match_all('/<button([^>]*)>\s*SOP &amp; Layanan/u', $html, $buttons);
        $this->assertNotEmpty($buttons[1], 'SOP & Layanan dropdown trigger not found');

        $hasCurrent = false;
        foreach ($buttons[1] as $attrs) {
            if (str_contains($attrs, 'aria-current="page"')) {
                $hasCurrent = true;
                break;
            }
        }

        $this->assertSame($expected, $hasCurrent);
    }
}
