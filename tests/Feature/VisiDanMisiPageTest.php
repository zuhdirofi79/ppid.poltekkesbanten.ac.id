<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Support\VisiDanMisi;
use Database\Seeders\CategorySeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisiDanMisiPageTest extends TestCase
{
    use RefreshDatabase;

    private const LEGACY_VISION = 'Menjadi Perguruan Tinggi Kesehatan yang Unggul, Profesional dan Berkarakter pada Tahun 2026';

    private const LEGACY_PPID_VISION = 'Terwujudnya pelayanan informasi yang transparan, akuntabel, dan prima';

    #[Test]
    public function published_page_shows_vision_and_mission(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-vision-mission-page', false);
        $response->assertSee('VISI DAN MISI POLTEKKES KEMENKES BANTEN', false);
        $response->assertSee('Profil PPID', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Profil', false);
        $response->assertSee('Visi dan Misi', false);
        $response->assertSee(VisiDanMisi::EXCERPT, false);
        $response->assertSee('>Visi</', false);
        $response->assertSee('“'.VisiDanMisi::VISION.'”', false);
        $response->assertSee('>Misi</', false);

        foreach (VisiDanMisi::MISSIONS as $item) {
            $response->assertSee($item, false);
        }

        $response->assertDontSee(self::LEGACY_VISION, false);
        $response->assertDontSee(self::LEGACY_PPID_VISION, false);
        $response->assertDontSee('Tahun 2026', false);

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
            'title' => VisiDanMisi::LEGACY_TITLE,
            'slug' => 'visi-dan-misi',
            'excerpt' => VisiDanMisi::LEGACY_EXCERPT,
            'body' => '',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-vision-mission-page', false);
        $response->assertSee(VisiDanMisi::VISION, false);
        $response->assertSee(VisiDanMisi::MISSIONS[0], false);
        $response->assertDontSee(self::LEGACY_PPID_VISION, false);
        $this->assertSame(5, preg_match_all('/class="ppid-mission-item"/', $response->getContent()));
    }

    #[Test]
    public function legacy_database_body_is_replaced_on_display(): void
    {
        Page::query()->create([
            'title' => VisiDanMisi::LEGACY_TITLE,
            'slug' => 'visi-dan-misi',
            'excerpt' => VisiDanMisi::LEGACY_EXCERPT,
            'meta_title' => VisiDanMisi::LEGACY_META_TITLE,
            'meta_description' => 'Visi dan misi PPID Poltekkes Kemenkes Banten dalam mewujudkan layanan informasi publik yang transparan, akuntabel, prima, dan berbasis digital.',
            'body' => '<section><h2>Visi</h2><p>'.self::LEGACY_PPID_VISION.' demi terselenggaranya pendidikan tinggi.</p></section>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee(VisiDanMisi::VISION, false);
        $response->assertSee(VisiDanMisi::MISSIONS[4], false);
        $response->assertDontSee(self::LEGACY_PPID_VISION, false);
        $response->assertDontSee(self::LEGACY_VISION, false);
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
            'title' => 'Visi dan Misi',
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
        $this->assertSame('Visi dan Misi', $page->title);
        $this->assertSame('visi-dan-misi', $page->slug);
        $this->assertStringContainsString(VisiDanMisi::VISION, (string) $page->body);
        $this->assertStringContainsString('<h2', (string) $page->body);
        $this->assertStringContainsString('<ol', (string) $page->body);
        $this->assertSame(5, preg_match_all('/<li\b/', (string) $page->body));
        $this->assertStringNotContainsString('<script', (string) $page->body);
        $this->assertStringNotContainsString(self::LEGACY_PPID_VISION, (string) $page->body);
        $this->assertStringNotContainsString(self::LEGACY_VISION, (string) $page->body);
    }

    #[Test]
    public function seeder_replaces_legacy_body_without_recreating_the_row(): void
    {
        $page = Page::query()->create([
            'title' => VisiDanMisi::LEGACY_TITLE,
            'slug' => 'visi-dan-misi',
            'excerpt' => VisiDanMisi::LEGACY_EXCERPT,
            'meta_title' => VisiDanMisi::LEGACY_META_TITLE,
            'meta_description' => 'Visi lama transparan, akuntabel, prima.',
            'body' => '<p>'.self::LEGACY_PPID_VISION.'</p>',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 3,
        ]);

        $this->seed(PageSeeder::class);

        $page->refresh();
        $this->assertSame(1, Page::query()->where('slug', 'visi-dan-misi')->count());
        $this->assertSame($page->id, Page::query()->where('slug', 'visi-dan-misi')->value('id'));
        $this->assertSame(VisiDanMisi::TITLE, $page->title);
        $this->assertStringContainsString(VisiDanMisi::VISION, (string) $page->body);
        $this->assertStringNotContainsString(self::LEGACY_PPID_VISION, (string) $page->body);
    }

    #[Test]
    public function seeder_does_not_overwrite_admin_edits(): void
    {
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'visi-dan-misi')->firstOrFail();
        $page->update([
            'title' => 'Visi dan Misi Disunting Admin',
            'body' => '<section><h2>Visi</h2><blockquote><p>Teks visi hasil suntingan admin yang tetap tersimpan.</p></blockquote></section>',
        ]);

        $this->seed(PageSeeder::class);

        $page->refresh();
        $this->assertSame('Visi dan Misi Disunting Admin', $page->title);
        $this->assertStringContainsString('hasil suntingan admin', (string) $page->body);

        $response = $this->get('/halaman/visi-dan-misi');
        $response->assertOk();
        $response->assertSee('Teks visi hasil suntingan admin yang tetap tersimpan.', false);
        $response->assertSee('Visi dan Misi Disunting Admin', false);
    }

    #[Test]
    public function dangerous_html_in_body_is_not_rendered_as_script(): void
    {
        Page::query()->create([
            'title' => 'Visi dan Misi',
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
        $response->assertSee('Aman', false);
        $response->assertSee('Teks', false);
        $response->assertSee('PPID-VISI-MISI-DEPLOY-20260723-A', false);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/visi-dan-misi');

        $response->assertOk();
        $response->assertSee(VisiDanMisi::META_DESCRIPTION, false);
        $response->assertSee('<title>', false);
        $response->assertSee(VisiDanMisi::DOCUMENT_TITLE, false);
        $response->assertDontSee('transparan, akuntabel, prima', false);
    }

    #[Test]
    public function related_public_pages_remain_available(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/halaman/profil-pimpinan-satuan-kerja')->assertOk();
        $this->get('/halaman/struktur-organisasi')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();
        $this->get('/informasi-publik/setiap-saat')->assertOk();
        $this->get('/informasi-publik/dikecualikan')->assertOk();
        $this->get('/halaman/visi-misi-satuan-kerja')
            ->assertOk()
            ->assertSee('Menjadi Perguruan Tinggi Kesehatan yang Unggul, Profesional dan Berkarakter pada Tahun 2026.', false);
    }
}
