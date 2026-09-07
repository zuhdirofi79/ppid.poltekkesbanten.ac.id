<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PublicDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function search_page_is_ok_without_query(): void
    {
        $this->get('/cari')->assertOk();
        $this->get('/pencarian')
            ->assertOk()
            ->assertSee('Cari Informasi PPID', false)
            ->assertSee('PPID-SEARCH-PAGE-DEPLOY-20260907-A', false)
            ->assertDontSee('0 hasil', false);
    }

    #[Test]
    public function homepage_nav_includes_global_search_marker(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('PPID-GLOBAL-SEARCH-DEPLOY-20260907-A', false)
            ->assertSee('Cari informasi PPID', false);
    }

    #[Test]
    public function lakin_query_finds_available_and_missing_documents(): void
    {
        $response = $this->get('/pencarian?q=lakin');

        $response->assertOk();
        $response->assertSee('Hasil pencarian untuk', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2022', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2023', false);
        $response->assertSee('Dokumen belum tersedia', false);
        $response->assertDontSee('href="#"', false);
        $response->assertDontSee('javascript:void', false);
    }

    #[Test]
    public function rsb_and_service_and_page_queries_work(): void
    {
        $this->get('/pencarian?q=rsb')
            ->assertOk()
            ->assertSee('RSB Poltekkes Banten 2025–2029', false);

        $this->get('/pencarian?q=keberatan')
            ->assertOk()
            ->assertSee('Pengajuan Keberatan', false)
            ->assertSee(route('services.show', ['slug' => 'pengajuan-keberatan'], false), false);

        $this->get('/pencarian?q=akreditasi')
            ->assertOk()
            ->assertSee('Akreditasi', false);
    }

    #[Test]
    public function unpublished_page_is_not_searchable(): void
    {
        Page::query()->create([
            'title' => 'Draft Rahasia Internal',
            'slug' => 'draft-rahasia-internal',
            'excerpt' => 'Tidak untuk publik',
            'body' => 'konten draft',
            'status' => 'draft',
        ]);

        $this->get('/pencarian?q=rahasia')
            ->assertOk()
            ->assertDontSee('Draft Rahasia Internal', false);
    }

    #[Test]
    public function xss_query_is_escaped(): void
    {
        $response = $this->get('/pencarian?q=<script>alert(1)</script>');

        $response->assertOk();
        $response->assertDontSee('<script>alert(1)</script>', false);
        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    #[Test]
    public function long_query_is_truncated_without_error(): void
    {
        $this->get('/pencarian?q='.str_repeat('ab', 80))->assertOk();
    }

    #[Test]
    public function suggestions_return_normalized_json(): void
    {
        $response = $this->getJson('/pencarian/saran?q=lakin');

        $response->assertOk();
        $response->assertJsonPath('query', 'lakin');
        $response->assertJsonStructure(['query', 'results']);
        $this->assertNotEmpty($response->json('results'));
        $this->assertArrayNotHasKey('file_path', $response->json('results.0'));
        $this->assertArrayNotHasKey('created_by', $response->json('results.0'));
    }

    #[Test]
    public function suggestions_ignore_short_queries(): void
    {
        $this->getJson('/pencarian/saran?q=a')
            ->assertOk()
            ->assertExactJson(['query' => '', 'results' => []]);
    }

    #[Test]
    public function missing_document_has_no_public_url(): void
    {
        $document = PublicDocument::query()->where('title', 'LAKIN Poltekkes Kemenkes Banten 2023')->first();
        $this->assertNotNull($document);
        $this->assertFalse($document->isAvailable());
        $this->assertNull($document->publicHref());
    }

    #[Test]
    public function verify_search_command_passes(): void
    {
        $this->artisan('ppid:verify-search')
            ->expectsOutputToContain('PASS')
            ->assertSuccessful();
    }
}
