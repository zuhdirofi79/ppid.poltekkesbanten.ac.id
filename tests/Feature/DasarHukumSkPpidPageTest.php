<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DasarHukumSkPpidPageTest extends TestCase
{
    use RefreshDatabase;

    private const PDF_PATH = '/docs/SK_PPID_2025.pdf';

    #[Test]
    public function published_page_shows_document_card(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/dasar-hukum-sk-ppid');

        $response->assertOk();
        $response->assertSee('ppid-sk-card', false);
        $response->assertSee('ppid-sk-document', false);
        $response->assertSee('Dasar Hukum dan SK PPID', false);
        $response->assertSee('SK Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025', false);
        $response->assertSee('Lihat SK PPID Poltekkes Banten', false);
        $response->assertSee('Download PDF', false);
        $response->assertSee('Preview SK PPID', false);
        $response->assertSee('Dokumen Resmi PPID', false);
        $response->assertSee('8 halaman', false);
        $response->assertSee(self::PDF_PATH, false);
        $response->assertSee('aria-controls="sk-ppid-preview"', false);
        $response->assertSee('title="Preview SK PPID Poltekkes Kemenkes Banten Tahun 2025"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('download="SK-PPID-Poltekkes-Kemenkes-Banten-2025.pdf"', false);
        $response->assertDontSee('hstgr.io', false);
        $response->assertDontSee('srv1863-files', false);
        $response->assertDontSee('bmn.poltekkesbanten.ac.id', false);
        $response->assertDontSee('public_html', false);
        $response->assertDontSee('2025070308315420250702075057SK-PPID-2025.pdf', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function card_renders_even_when_page_body_is_empty(): void
    {
        Page::query()->create([
            'title' => 'Dasar Hukum dan SK PPID',
            'slug' => 'dasar-hukum-sk-ppid',
            'excerpt' => 'Keputusan Direktur Poltekkes Kemenkes Banten tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025.',
            'body' => '',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/dasar-hukum-sk-ppid');

        $response->assertOk();
        $response->assertSee('ppid-sk-card', false);
        $response->assertSee('Lihat SK PPID Poltekkes Banten', false);
        $response->assertSee(self::PDF_PATH, false);
    }

    #[Test]
    public function missing_slug_returns_404(): void
    {
        $this->get('/halaman/tidak-ada-sama-sekali-xyz')->assertNotFound();
    }

    #[Test]
    public function unpublished_page_is_not_publicly_accessible(): void
    {
        Page::query()->create([
            'title' => 'Dasar Hukum dan SK PPID',
            'slug' => 'dasar-hukum-sk-ppid',
            'excerpt' => 'Draft',
            'body' => '',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/halaman/dasar-hukum-sk-ppid')->assertNotFound();
    }

    #[Test]
    public function other_page_slug_is_unaffected(): void
    {
        Page::query()->create([
            'title' => 'Tugas Fungsi Wewenang',
            'slug' => 'tugas-fungsi-wewenang',
            'excerpt' => 'Ringkasan tugas',
            'body' => '<p>Konten tugas fungsi.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/tugas-fungsi-wewenang');

        $response->assertOk();
        $response->assertSee('Tugas Fungsi Wewenang', false);
        $response->assertDontSee('ppid-sk-card', false);
        $response->assertDontSee('Preview SK PPID', false);
        $response->assertSee('cms-page', false);
    }

    #[Test]
    public function menu_profil_points_to_correct_url(): void
    {
        $this->seed(MenuSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('pages.show', ['slug' => 'dasar-hukum-sk-ppid'], false), false);
        $response->assertSee('Dasar Hukum dan SK PPID', false);
    }

    #[Test]
    public function page_seeder_is_idempotent(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'dasar-hukum-sk-ppid')->first();

        $this->assertNotNull($page);
        $this->assertSame(1, Page::query()->where('slug', 'dasar-hukum-sk-ppid')->count());
        $this->assertSame('published', $page->status);
        $this->assertSame('Dasar Hukum dan SK PPID', $page->title);
        $this->assertSame('', $page->body ?? '');
        $this->assertStringNotContainsString('<iframe', (string) $page->body);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/dasar-hukum-sk-ppid');

        $response->assertOk();
        $response->assertSee('Dokumen Keputusan Direktur Poltekkes Kemenkes Banten tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025.', false);
        $response->assertSee('<title>', false);
        $response->assertSee('Dasar Hukum dan SK PPID Poltekkes Kemenkes Banten', false);
    }
}
