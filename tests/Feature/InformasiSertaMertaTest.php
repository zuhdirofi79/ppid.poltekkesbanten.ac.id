<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiBerkalaSeeder;
use Database\Seeders\InformasiSertaMertaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InformasiSertaMertaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(InformasiSertaMertaSeeder::class);
    }

    #[Test]
    public function serta_merta_lists_the_ppid_decree_without_the_empty_state(): void
    {
        $response = $this->get('/informasi-publik/serta-merta');

        $response->assertOk();
        $response->assertSee('Informasi Serta-Merta', false);
        $response->assertSee('SK Penetapan PPID', false);
        $response->assertSee('SK Penetapan PPID Poltekkes Kemenkes Banten Tahun 2025', false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee('PDF / Dokumen', false);
        $response->assertSee('Lihat Dokumen', false);
        $response->assertSee('SK%20PPID%202025.docx.pdf', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('Belum ada dokumen pada kategori ini.', false);
        $response->assertDontSee('%2520', false);

        $document = PublicDocument::query()->where('slug', PublicDocument::makeCatalogSlug(InformasiSertaMertaSeeder::TITLE))->firstOrFail();
        $this->assertTrue($document->isAvailable());
        $this->assertSame(PublicDocument::SOURCE_INTERNAL, $document->source_type);
        $this->assertSame('/docs/SK PPID 2025.docx.pdf', $document->internal_url);
    }

    #[Test]
    public function seeder_is_idempotent_and_keeps_an_admin_replacement(): void
    {
        $this->seed(InformasiSertaMertaSeeder::class);

        $this->assertSame(1, PublicDocument::query()->where('section_title', InformasiSertaMertaSeeder::SECTION)->count());

        $document = PublicDocument::query()->where('slug', PublicDocument::makeCatalogSlug(InformasiSertaMertaSeeder::TITLE))->firstOrFail();
        $document->update([
            'title' => 'SK Penetapan PPID — suntingan admin',
            'internal_url' => '/docs/pengganti-admin.pdf',
        ]);

        $this->seed(InformasiSertaMertaSeeder::class);

        $document->refresh();
        $this->assertSame('SK Penetapan PPID — suntingan admin', $document->title);
        $this->assertSame('/docs/pengganti-admin.pdf', $document->internal_url);
        $this->assertSame(1, PublicDocument::withTrashed()->where('slug', $document->slug)->count());
    }

    #[Test]
    public function berkala_catalog_still_responds(): void
    {
        $this->seed(InformasiBerkalaSeeder::class);

        $this->get('/informasi-publik/berkala')
            ->assertOk()
            ->assertSee('Informasi Berkala', false)
            ->assertSee('PPID-BERKALA-PUBLIC-SYNC-FIX-20260916-A', false);
    }
}
