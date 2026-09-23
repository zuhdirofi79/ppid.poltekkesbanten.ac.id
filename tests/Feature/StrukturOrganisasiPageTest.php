<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StrukturOrganisasiPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function published_page_shows_organization_chart_image(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/struktur-organisasi');

        $response->assertOk();
        $response->assertSee('PPID-STRUKTUR-ORGANISASI-DEPLOY-20260923-A', false);
        $response->assertSee('ppid-org-structure-page', false);
        $response->assertSee('Struktur Organisasi PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee('struktur-organisasi-ppid.jpeg', false);
        $response->assertSee('Profil PPID', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Struktur Organisasi', false);
        $response->assertSee('Struktur organisasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten periode 2025-2029.', false);
        $response->assertSee('alt="Struktur Organisasi PPID Poltekkes Kemenkes Banten Periode 2025-2029"', false);
        $response->assertSee('object-contain', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('Lihat Ukuran Penuh', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Download Struktur Organisasi', false);
        $response->assertSee('download="struktur-organisasi-ppid-poltekkes-kemenkes-banten.jpeg"', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function unpublished_page_is_not_publicly_accessible(): void
    {
        Page::query()->create([
            'title' => 'Struktur Organisasi PPID Poltekkes Kemenkes Banten',
            'slug' => 'struktur-organisasi',
            'excerpt' => 'Draft',
            'body' => '',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/halaman/struktur-organisasi')->assertNotFound();
    }

    #[Test]
    public function other_page_slug_is_unaffected(): void
    {
        Page::query()->create([
            'title' => 'Halaman Uji Generik',
            'slug' => 'halaman-uji-generik',
            'excerpt' => 'Ringkasan uji',
            'body' => '<p>Konten generik aman.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/halaman-uji-generik');

        $response->assertOk();
        $response->assertSee('Halaman Uji Generik', false);
        $response->assertSee('cms-page', false);
        $response->assertDontSee('ppid-org-structure-page', false);
        $response->assertDontSee('struktur-organisasi-ppid.jpeg', false);
    }

    #[Test]
    public function menu_profil_points_to_organization_chart(): void
    {
        $this->seed(MenuSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('pages.show', ['slug' => 'struktur-organisasi'], false), false);
        $response->assertSee('Struktur Organisasi PPID', false);
    }

    #[Test]
    public function page_seeder_is_idempotent(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'struktur-organisasi')->first();

        $this->assertNotNull($page);
        $this->assertSame(1, Page::query()->where('slug', 'struktur-organisasi')->count());
        $this->assertSame('published', $page->status);
        $this->assertSame('Struktur Organisasi PPID Poltekkes Kemenkes Banten', $page->title);
        $this->assertSame('Struktur Organisasi PPID Poltekkes Kemenkes Banten', $page->meta_title);
        $this->assertSame('Struktur organisasi PPID Poltekkes Kemenkes Banten periode 2025-2029.', $page->meta_description);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/struktur-organisasi');

        $response->assertOk();
        $response->assertSee('<title>', false);
        $response->assertSee('Struktur Organisasi PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee('Struktur organisasi PPID Poltekkes Kemenkes Banten periode 2025-2029.', false);
    }
}
