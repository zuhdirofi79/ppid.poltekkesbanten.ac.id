<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfilSingkatPageTest extends TestCase
{
    use RefreshDatabase;

    private const PDF_RELATIVE = 'docs/2.-PROFIL-PPID-POLTEKKES-BANTEN.pdf';

    #[Test]
    public function published_page_shows_short_profile_and_hides_long_sections(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/profil-singkat-ppid');

        $response->assertOk();
        $response->assertSee('ppid-profile-page', false);
        $response->assertSee('Profil Singkat', false);
        $response->assertSee('Undang-Undang Nomor 14 Tahun 2008', false);
        $response->assertSee('Direktur Poltekkes Kemenkes Banten telah menunjuk', false);
        $response->assertSee('berfungsi mengelola, mendokumentasikan, menyediakan', false);
        $response->assertSee('Keberadaan PPID diharapkan memberikan kemudahan', false);
        $response->assertSee('Dokumen Profil PPID Lengkap', false);

        $response->assertDontSee('Tugas PPID', false);
        $response->assertDontSee('Jenis Informasi Publik', false);
        $response->assertDontSee('Standar Layanan Informasi Publik', false);
        $response->assertDontSee('Membutuhkan Informasi Publik?', false);
        $response->assertDontSee('hstgr.io', false);
        $response->assertDontSee('/home/', false);
        $response->assertDontSee('public_html', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function pdf_url_points_to_public_docs_path_when_file_exists(): void
    {
        $this->seed(PageSeeder::class);
        $this->withTemporaryPdf(function () {
            $response = $this->get('/halaman/profil-singkat-ppid');

            $response->assertOk();
            $response->assertSee('/docs/2.-PROFIL-PPID-POLTEKKES-BANTEN.pdf', false);
            $response->assertSee('type="application/pdf"', false);
            $response->assertSee('rel="noopener noreferrer"', false);
            $response->assertSee('Buka PDF di Tab Baru', false);
            $response->assertSee('Unduh PDF', false);
            $response->assertSee('download="Profil-PPID-Poltekkes-Kemenkes-Banten.pdf"', false);
            $response->assertDontSee('hstgr.io', false);
            $response->assertDontSee(public_path(self::PDF_RELATIVE), false);
        });
    }

    #[Test]
    public function page_remains_200_when_pdf_is_missing(): void
    {
        $this->seed(PageSeeder::class);

        $pdfPath = public_path(self::PDF_RELATIVE);
        $existed = is_file($pdfPath);
        $backup = null;

        if ($existed) {
            $backup = $pdfPath.'.bak-test';
            File::move($pdfPath, $backup);
        }

        try {
            $response = $this->get('/halaman/profil-singkat-ppid');

            $response->assertOk();
            $response->assertSee('Dokumen Profil PPID belum tersedia.', false);
            $response->assertDontSee('type="application/pdf"', false);
        } finally {
            if ($backup !== null && is_file($backup)) {
                File::move($backup, $pdfPath);
            }
        }
    }

    #[Test]
    public function building_image_has_accessible_alt_when_file_exists(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/profil-singkat-ppid');
        $response->assertOk();

        $hero = config('ppid.hero');
        if (is_string($hero) && is_file(public_path($hero))) {
            $response->assertSee('alt="Gedung Poltekkes Kemenkes Banten"', false);
            $response->assertSee(asset($hero), false);
        }
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
            'title' => 'Draft Rahasia',
            'slug' => 'profil-singkat-ppid',
            'excerpt' => 'Draft',
            'body' => '<p>Draft</p>',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/halaman/profil-singkat-ppid')->assertNotFound();
    }

    #[Test]
    public function other_page_slug_uses_generic_template(): void
    {
        Page::query()->create([
            'title' => 'Visi dan Misi',
            'slug' => 'visi-dan-misi',
            'excerpt' => 'Visi misi',
            'body' => '<p>Konten visi misi.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('Visi dan Misi', false);
        $response->assertDontSee('ppid-profile-page', false);
        $response->assertDontSee('Dokumen Profil PPID Lengkap', false);
        $response->assertSee('cms-page', false);
    }

    #[Test]
    public function menu_profil_points_to_correct_url(): void
    {
        $this->seed(MenuSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('pages.show', ['slug' => 'profil-singkat-ppid'], false), false);
        $response->assertSee('Profil Singkat PPID', false);
    }

    #[Test]
    public function dangerous_html_is_not_rendered(): void
    {
        Page::query()->create([
            'title' => 'Profil Singkat PPID Poltekkes Kemenkes Banten',
            'slug' => 'profil-singkat-ppid',
            'excerpt' => 'Ringkasan',
            'body' => '<h2>Profil Singkat</h2><p>Aman</p><script>window.__xss=1</script><p onclick="alert(1)">Teks</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/profil-singkat-ppid');

        $response->assertOk();
        $response->assertDontSee('<script>', false);
        $response->assertDontSee('onclick=', false);
        $response->assertSee('Profil Singkat', false);
    }

    #[Test]
    public function page_seeder_is_idempotent_and_only_keeps_short_profile(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'profil-singkat-ppid')->first();

        $this->assertNotNull($page);
        $this->assertSame(1, Page::query()->where('slug', 'profil-singkat-ppid')->count());
        $this->assertSame('published', $page->status);
        $this->assertStringContainsString('<h2>Profil Singkat</h2>', $page->body);
        $this->assertStringNotContainsString('Tugas PPID', $page->body);
        $this->assertStringNotContainsString('Jenis Informasi Publik', $page->body);
        $this->assertStringNotContainsString('<iframe', $page->body);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/profil-singkat-ppid');

        $response->assertOk();
        $response->assertSee('Mengenal profil singkat PPID Poltekkes Kemenkes Banten dan unduh dokumen profil lengkap.', false);
        $response->assertSee('<title>', false);
        $response->assertSee('Profil Singkat PPID Poltekkes Kemenkes Banten', false);
    }

    /**
     * @param  callable(): void  $callback
     */
    private function withTemporaryPdf(callable $callback): void
    {
        $path = public_path(self::PDF_RELATIVE);
        $dir = dirname($path);
        $createdDir = false;
        $createdFile = false;

        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
            $createdDir = true;
        }

        if (! is_file($path)) {
            File::put($path, "%PDF-1.4\n% temporary test fixture\n");
            $createdFile = true;
        }

        try {
            $callback();
        } finally {
            if ($createdFile && is_file($path)) {
                File::delete($path);
            }
            if ($createdDir && is_dir($dir) && count(File::files($dir)) === 0) {
                File::deleteDirectory($dir);
            }
        }
    }
}
