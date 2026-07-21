<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DasarHukumSkPpidPageTest extends TestCase
{
    use RefreshDatabase;

    private const PDF_RELATIVE = 'docs/2025070308315420250702075057SK-PPID-2025.pdf';

    #[Test]
    public function published_page_shows_document_viewer_shell(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/dasar-hukum-sk-ppid');

        $response->assertOk();
        $response->assertSee('ppid-document-page', false);
        $response->assertSee('Dasar Hukum dan SK PPID', false);
        $response->assertSee('Dokumen PPID', false);
        $response->assertSee('Keputusan Direktur Poltekkes Kemenkes Banten tentang Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025.', false);
        $response->assertDontSee('hstgr.io', false);
        $response->assertDontSee('public_html', false);
        $response->assertDontSee('srv1863-files', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function pdf_url_points_to_public_docs_path_when_file_exists(): void
    {
        $this->seed(PageSeeder::class);
        $this->withTemporaryPdf(function () {
            $response = $this->get('/halaman/dasar-hukum-sk-ppid');

            $response->assertOk();
            $response->assertSee('/docs/2025070308315420250702075057SK-PPID-2025.pdf', false);
            $response->assertSee('type="application/pdf"', false);
            $response->assertSee('target="_blank"', false);
            $response->assertSee('rel="noopener noreferrer"', false);
            $response->assertSee('Buka PDF di Tab Baru', false);
            $response->assertSee('Unduh PDF', false);
            $response->assertSee('download="SK-PPID-Poltekkes-Kemenkes-Banten-2025.pdf"', false);
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
            $response = $this->get('/halaman/dasar-hukum-sk-ppid');

            $response->assertOk();
            $response->assertSee('Dokumen SK PPID belum tersedia.', false);
            $response->assertDontSee('type="application/pdf"', false);
            $response->assertDontSee('Buka PDF di Tab Baru', false);
        } finally {
            if ($backup !== null && is_file($backup)) {
                File::move($backup, $pdfPath);
            }
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
        $response->assertDontSee('ppid-document-page', false);
        $response->assertDontSee('Dokumen SK PPID belum tersedia.', false);
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
