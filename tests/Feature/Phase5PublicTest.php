<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5PublicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_search_page_works(): void
    {
        $this->get('/cari?q=CONTOH')->assertOk()->assertSee('Hasil', false);
    }

    public function test_documents_index_and_filter(): void
    {
        PublicDocument::query()->create([
            'title' => 'CONTOH Laporan Tahunan',
            'slug' => 'contoh-laporan-tahunan',
            'year' => 2026,
            'status' => 'published',
            'published_at' => now(),
            'external_url' => 'https://example.com/laporan.pdf',
            'is_external' => true,
        ]);

        $this->get('/dokumen-publik')->assertOk()->assertSee('CONTOH Laporan Tahunan', false);
        $this->get('/dokumen-publik?year=2026')->assertOk()->assertSee('CONTOH Laporan Tahunan', false);
        $this->get('/dokumen-publik?year=1999')->assertOk()->assertDontSee('CONTOH Laporan Tahunan', false);
    }

    public function test_document_download_external_increments_counter(): void
    {
        $document = PublicDocument::query()->create([
            'title' => 'CONTOH Unduh',
            'slug' => 'contoh-unduh',
            'status' => 'published',
            'published_at' => now(),
            'external_url' => 'https://example.com/file.pdf',
            'is_external' => true,
            'download_count' => 0,
        ]);

        $this->get('/dokumen-publik/contoh-unduh/unduh')
            ->assertRedirect('https://example.com/file.pdf');

        $this->assertSame(1, $document->fresh()->download_count);
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $this->get('/robots.txt')->assertOk();
    }

    public function test_security_headers_present(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }

    public function test_404_page(): void
    {
        $this->get('/halaman/tidak-ada-sama-sekali-xyz')->assertNotFound();
    }

    public function test_posts_listing_and_detail(): void
    {
        $this->get('/berita')->assertOk();
        $this->get('/berita/contoh-pembukaan-layanan-informasi-publik-ppid')->assertOk();
    }

    public function test_super_admin_seeded_credentials_can_login(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id');

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role' => 'super-admin',
            'is_active' => 1,
        ]);

        $this->post('/admin/login', [
            'login' => $email,
            'password' => env('SUPER_ADMIN_PASSWORD', 'qtester1234567890'),
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->canDo('manage-users'));
    }
}
