<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\ServiceLink;
use App\Models\SiteSetting;
use App\Support\PublicNavigation;
use Database\Seeders\EnsureDaftarRegistrasiPemohonMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DaftarRegistrasiPemohonPageTest extends TestCase
{
    use RefreshDatabase;

    private const DOCUMENT_URL = 'https://ppid.poltekkesbanten.ac.id/docs/DAFTAR%20REGISTER%20PERMOHONAN.docx';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function register_page_renders_the_docx_document_card(): void
    {
        $response = $this->get('/layanan/daftar-registrasi-pemohon');

        $response->assertOk();
        $response->assertSee('PPID-DAFTAR-REGISTRASI-PEMOHON-DEPLOY-20261006-A', false);
        $response->assertSee('Daftar Registrasi Pemohon', false);
        $response->assertSee('Daftar Register Permohonan Informasi Publik merupakan dokumen pencatatan permohonan informasi yang diterima oleh PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Dokumen ini digunakan sebagai instrumen administrasi dan dokumentasi pelayanan informasi publik untuk mencatat permohonan informasi yang masuk.', false);
        $response->assertSee('Daftar Register Permohonan Informasi', false);
        $response->assertSee('Daftar Register Permohonan Informasi Publik', false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee('DOCX / Dokumen', false);
        $response->assertSee('Dokumen administrasi pencatatan permohonan informasi publik PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Buka Dokumen', false);
        $response->assertSee('Unduh DOCX', false);
        $response->assertSee(self::DOCUMENT_URL, false);
        $response->assertSee('DAFTAR%20REGISTER%20PERMOHONAN.docx', false);
        $response->assertSee('download="DAFTAR REGISTER PERMOHONAN.docx"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('aria-label="Buka Daftar Register Permohonan Informasi Publik"', false);
        $response->assertSee('aria-label="Unduh Daftar Register Permohonan Informasi Publik format DOCX"', false);
        $response->assertSee('SOP & Layanan', false);
        $response->assertSee('id="daftar-register-permohonan-informasi"', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(2, substr_count($html, self::DOCUMENT_URL));
        $this->assertStringNotContainsString('http://ppid.poltekkesbanten.ac.id/docs/', $html);
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('application/pdf', $html);
        $this->assertLessThan(
            strpos($html, 'id="daftar-register-doc-title"'),
            strpos($html, 'id="daftar-register-heading"'),
        );
    }

    #[Test]
    public function existing_sop_and_information_request_pages_stay_available(): void
    {
        $this->get('/halaman/standar-operasional-prosedur-layanan-informasi-publik')
            ->assertOk()
            ->assertSee('Standar Operasional Prosedur Layanan Informasi Publik', false);

        $this->get('/layanan/permohonan-informasi')
            ->assertOk()
            ->assertSee('Ajukan Permohonan Informasi Publik', false)
            ->assertSee('Waktu Pelayanan Informasi', false)
            ->assertDontSee('PPID-DAFTAR-REGISTRASI-PEMOHON-DEPLOY-20261006-A', false);
    }

    #[Test]
    public function external_service_link_does_not_replace_the_document_page(): void
    {
        ServiceLink::query()->create([
            'title' => 'Daftar Registrasi Pemohon',
            'slug' => 'daftar-registrasi-pemohon',
            'excerpt' => 'Tidak boleh menggantikan halaman dokumen.',
            'external_url' => 'https://example.com/should-not-redirect',
            'is_external' => true,
            'is_featured' => false,
            'is_active' => true,
            'sort_order' => 90,
        ]);

        $response = $this->get('/layanan/daftar-registrasi-pemohon');

        $response->assertOk();
        $response->assertSee(self::DOCUMENT_URL, false);
        $response->assertSee('Buka Dokumen', false);
        $response->assertDontSee('https://example.com/should-not-redirect', false);
    }

    #[Test]
    public function database_navigation_inserts_the_item_directly_after_sop(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', 'Daftar Registrasi Pemohon')->delete();

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $before = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Daftar Registrasi Pemohon', $before);

        $this->seed(EnsureDaftarRegistrasiPemohonMenuItemSeeder::class);
        $this->seed(EnsureDaftarRegistrasiPemohonMenuItemSeeder::class);
        PublicNavigation::forget();

        $this->assertSame(1, MenuItem::query()->where('label', 'Daftar Registrasi Pemohon')->count());

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString(route('services.show', ['slug' => 'daftar-registrasi-pemohon'], false), $html);
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Standar Operasional Prosedur Layanan Informasi Publik[\s\S]+?Daftar Registrasi Pemohon/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Standar Operasional Prosedur Layanan Informasi Publik[\s\S]+?Daftar Registrasi Pemohon/u',
            $html
        ));
    }
}
