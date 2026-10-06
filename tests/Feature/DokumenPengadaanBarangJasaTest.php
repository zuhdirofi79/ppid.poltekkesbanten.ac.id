<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use App\Support\DokumenPengadaanBarangJasa;
use App\Support\StatistikKepegawaian;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DokumenPengadaanBarangJasaSeeder;
use Database\Seeders\StatistikKepegawaianSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DokumenPengadaanBarangJasaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(DokumenPengadaanBarangJasaSeeder::class);
        $this->seed(StatistikKepegawaianSeeder::class);
    }

    #[Test]
    public function setiap_saat_shows_the_local_procurement_image_before_staffing_statistics(): void
    {
        PublicDocument::query()
            ->where('section_title', DokumenPengadaanBarangJasa::SECTION)
            ->update([
                'external_url' => 'https://s.kemkes.go.id/DokumenPBJPolkesten',
                'is_external' => true,
            ]);

        $response = $this->get('/informasi-publik/setiap-saat');

        $response->assertOk();
        $response->assertSee(DokumenPengadaanBarangJasa::SECTION, false);
        $response->assertSee(DokumenPengadaanBarangJasa::DESCRIPTION, false);
        $response->assertSee(DokumenPengadaanBarangJasa::IMAGE_PATH, false);
        $response->assertSee(DokumenPengadaanBarangJasa::IMAGE_ALT, false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee(DokumenPengadaanBarangJasa::DOCUMENT_LABEL, false);
        $response->assertSee(DokumenPengadaanBarangJasa::FORMAT_LABEL, false);
        $response->assertSee(DokumenPengadaanBarangJasa::OPEN_LABEL, false);
        $response->assertSee(DokumenPengadaanBarangJasa::DOWNLOAD_LABEL, false);
        $response->assertSee(DokumenPengadaanBarangJasa::OPEN_ARIA, false);
        $response->assertSee('id="surat-perjanjian-pengadaan-barang-jasa"', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('object-fit: contain', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('download="'.DokumenPengadaanBarangJasa::DOWNLOAD_NAME.'"', false);
        $response->assertSee('Statistik Kepegawaian', false);
        $response->assertSee('Oktober 2026', false);
        $response->assertSee('id="statistik-kepegawaian"', false);
        $response->assertDontSee('s.kemkes.go.id/DokumenPBJPolkesten', false);
        $response->assertDontSee('DokumenPBJPolkesten', false);
        $response->assertDontSee('Akses Dokumen Pengadaan Barang dan Jasa', false);
        $response->assertDontSee('Pindai QR Code', false);

        $html = $response->getContent();
        $procurement = strpos($html, DokumenPengadaanBarangJasa::SECTION);
        $staffing = strpos($html, 'Statistik Kepegawaian');
        $this->assertNotFalse($procurement);
        $this->assertNotFalse($staffing);
        $this->assertLessThan($staffing, $procurement);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $response->assertDontSee('/dokumen-publik/'.PublicDocument::makeCatalogSlug(DokumenPengadaanBarangJasa::SECTION), false);
    }

    #[Test]
    public function seeder_is_idempotent_and_does_not_render_an_admin_url_edit(): void
    {
        $this->seed(DokumenPengadaanBarangJasaSeeder::class);

        $this->assertSame(1, PublicDocument::query()->where('section_title', DokumenPengadaanBarangJasa::SECTION)->count());

        $document = PublicDocument::query()
            ->where('slug', PublicDocument::makeCatalogSlug(DokumenPengadaanBarangJasa::SECTION))
            ->firstOrFail();
        $document->update([
            'external_url' => 'https://example.com/dokumen-pbj-suntingan',
        ]);

        $this->seed(DokumenPengadaanBarangJasaSeeder::class);

        $document->refresh();
        $this->assertSame('https://example.com/dokumen-pbj-suntingan', $document->external_url);
        $this->assertSame(1, PublicDocument::query()->where('section_title', DokumenPengadaanBarangJasa::SECTION)->count());

        $this->get('/informasi-publik/setiap-saat')
            ->assertOk()
            ->assertSee(DokumenPengadaanBarangJasa::IMAGE_PATH, false)
            ->assertDontSee('https://example.com/dokumen-pbj-suntingan', false)
            ->assertDontSee('s.kemkes.go.id/DokumenPBJPolkesten', false)
            ->assertSee(StatistikKepegawaian::SECTION, false);
    }
}
