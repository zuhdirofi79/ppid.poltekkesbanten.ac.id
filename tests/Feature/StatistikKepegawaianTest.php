<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use App\Support\StatistikKepegawaian;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiBerkalaSeeder;
use Database\Seeders\StatistikKepegawaianSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatistikKepegawaianTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(StatistikKepegawaianSeeder::class);
    }

    #[Test]
    public function setiap_saat_publishes_the_staffing_charts_without_replacing_other_documents(): void
    {
        PublicDocument::query()->create([
            'title' => 'Dokumen Setiap Saat yang Sudah Ada',
            'slug' => 'dokumen-setiap-saat-yang-sudah-ada',
            'public_information_category_id' => PublicDocument::query()->where('section_title', StatistikKepegawaian::SECTION)->value('public_information_category_id'),
            'section_title' => null,
            'status' => 'published',
            'published_at' => now(),
            'source_type' => PublicDocument::SOURCE_INTERNAL,
            'internal_url' => '/halaman/akreditasi',
            'sort_order' => 1,
        ]);

        $response = $this->get('/informasi-publik/setiap-saat');

        $response->assertOk();
        $response->assertSee('Informasi Setiap Saat', false);
        $response->assertSee('Statistik Kepegawaian', false);
        $response->assertSee('Data statistik kepegawaian yang dibuat dan dikelola oleh Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Oktober 2026', false);
        $response->assertSee('id="statistik-kepegawaian"', false);
        $response->assertSee('docs/statistik-kepegawaian/oktober-2026/grafik-1.jpeg', false);
        $response->assertSee('docs/statistik-kepegawaian/oktober-2026/grafik-2.jpeg', false);
        $response->assertSee('object-fit: contain', false);
        $response->assertSee('Dokumen Setiap Saat yang Sudah Ada', false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee('JPEG / Statistik', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(1, preg_match(
            '/grafik-1\.jpeg[\s\S]+grafik-2\.jpeg/u',
            $html
        ));
        $response->assertDontSee('/dokumen-publik/statistik-kepegawaian-oktober-2026-grafik-1', false);
        $response->assertDontSee('/dokumen-publik/statistik-kepegawaian-oktober-2026-grafik-2', false);
    }

    #[Test]
    public function seeder_is_idempotent_and_keeps_an_admin_edit(): void
    {
        $this->seed(StatistikKepegawaianSeeder::class);

        $this->assertSame(2, PublicDocument::query()->where('section_title', StatistikKepegawaian::SECTION)->count());

        $document = PublicDocument::query()
            ->where('slug', PublicDocument::makeCatalogSlug('Statistik Kepegawaian Oktober 2026 - Grafik 1'))
            ->firstOrFail();
        $document->update([
            'description' => 'Keterangan suntingan admin',
            'internal_url' => '/docs/statistik-kepegawaian/oktober-2026/grafik-1.jpeg',
        ]);

        $this->seed(StatistikKepegawaianSeeder::class);

        $document->refresh();
        $this->assertSame('Keterangan suntingan admin', $document->description);
        $this->assertSame(2, PublicDocument::query()->where('section_title', StatistikKepegawaian::SECTION)->count());
    }

    #[Test]
    public function unpublished_chart_is_hidden_and_other_information_routes_stay_intact(): void
    {
        PublicDocument::query()
            ->where('section_title', StatistikKepegawaian::SECTION)
            ->update(['status' => 'draft']);

        $this->get('/informasi-publik/setiap-saat')
            ->assertOk()
            ->assertDontSee('id="statistik-kepegawaian"', false)
            ->assertDontSee('grafik-1.jpeg', false)
            ->assertDontSee('Belum ada dokumen pada kategori ini.', false);

        $this->assertTrue(Route::has('information.category'));

        $this->seed(InformasiBerkalaSeeder::class);
        $this->get('/informasi-publik/berkala')
            ->assertOk()
            ->assertDontSee('docs/statistik-kepegawaian/oktober-2026/grafik-1.jpeg', false);

        $this->get('/informasi-publik/serta-merta')->assertOk();
    }
}
