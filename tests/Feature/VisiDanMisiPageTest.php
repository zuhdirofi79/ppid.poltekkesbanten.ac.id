<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisiDanMisiPageTest extends TestCase
{
    use RefreshDatabase;

    private const VISI_TEXT = 'Terwujudnya pelayanan informasi yang transparan, akuntabel, dan prima';

    private const MISI_ITEMS = [
        'Menyediakan informasi publik yang akurat dan dapat dipertanggungjawabkan;',
        'Membangun dan mengembangkan sistem penyediaan dan layanan informasi berbasis digital;',
        'Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi;',
        'Memanfaatkan teknologi informasi dalam memberikan layanan informasi publik kepada masyarakat dan mendukung pengelolaan keterbukaan informasi publik;',
        'Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi.',
    ];

    #[Test]
    public function published_page_shows_vision_and_mission(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-vision-mission-page', false);
        $response->assertSee('Visi dan Misi PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee('Profil PPID', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Profil', false);
        $response->assertSee('Visi dan Misi', false);
        $response->assertSee('Visi dan misi pelayanan informasi publik PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('>Visi</', false);
        $response->assertSee(self::VISI_TEXT, false);
        $response->assertSee('>Misi</', false);

        foreach (self::MISI_ITEMS as $item) {
            $response->assertSee($item, false);
        }

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(5, preg_match_all('/class="ppid-mission-item"/', $html));
        $this->assertSame(1, preg_match_all('/<ol\b[^>]*class="ppid-mission-list"/', $html));
        $this->assertSame(5, preg_match_all('/<li\b[^>]*class="ppid-mission-item"/', $html));
    }

    #[Test]
    public function page_renders_even_when_body_is_empty(): void
    {
        Page::query()->create([
            'title' => 'Visi dan Misi PPID Poltekkes Kemenkes Banten',
            'slug' => 'visi-dan-misi',
            'excerpt' => 'Visi dan misi pelayanan informasi publik PPID Poltekkes Kemenkes Banten.',
            'body' => '',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-vision-mission-page', false);
        $response->assertSee(self::VISI_TEXT, false);
        $response->assertSee(self::MISI_ITEMS[0], false);
        $this->assertSame(5, preg_match_all('/class="ppid-mission-item"/', $response->getContent()));
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
            'title' => 'Visi dan Misi PPID Poltekkes Kemenkes Banten',
            'slug' => 'visi-dan-misi',
            'excerpt' => 'Draft',
            'body' => '',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/halaman/visi-dan-misi')->assertNotFound();
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
        $response->assertDontSee('ppid-vision-mission-page', false);
        $response->assertDontSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
    }

    #[Test]
    public function menu_profil_points_to_correct_url(): void
    {
        $this->seed(MenuSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('pages.show', ['slug' => 'visi-dan-misi'], false), false);
        $response->assertSee('Visi dan Misi', false);
    }

    #[Test]
    public function page_seeder_is_idempotent(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'visi-dan-misi')->first();

        $this->assertNotNull($page);
        $this->assertSame(1, Page::query()->where('slug', 'visi-dan-misi')->count());
        $this->assertSame('published', $page->status);
        $this->assertSame('Visi dan Misi PPID Poltekkes Kemenkes Banten', $page->title);
        $this->assertStringContainsString('<h2>Visi</h2>', (string) $page->body);
        $this->assertStringContainsString('<h2>Misi</h2>', (string) $page->body);
        $this->assertStringContainsString('<ol>', (string) $page->body);
        $this->assertStringNotContainsString('<script', (string) $page->body);
    }

    #[Test]
    public function dangerous_html_in_body_is_not_rendered_as_script(): void
    {
        Page::query()->create([
            'title' => 'Visi dan Misi PPID Poltekkes Kemenkes Banten',
            'slug' => 'visi-dan-misi',
            'excerpt' => 'Ringkasan',
            'body' => '<h2>Visi</h2><p>Aman</p><script>window.__xss=1</script><p onclick="alert(1)">Teks</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertDontSee('<script>', false);
        $response->assertDontSee('onclick=', false);
        $response->assertDontSee('window.__xss', false);
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
        $response->assertSee(self::VISI_TEXT, false);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('Visi dan misi PPID Poltekkes Kemenkes Banten dalam mewujudkan layanan informasi publik yang transparan, akuntabel, prima, dan berbasis digital.', false);
        $response->assertSee('<title>', false);
        $response->assertSee('Visi dan Misi PPID Poltekkes Kemenkes Banten', false);
    }
}
