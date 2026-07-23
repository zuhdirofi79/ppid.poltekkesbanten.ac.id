<?php

namespace Tests\Feature;

use App\Models\Page;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TugasFungsiWewenangPageTest extends TestCase
{
    use RefreshDatabase;

    private const FUNGSI_TEXT = 'Undang-Undang Nomor 14 Tahun 2008';

    private const TUGAS_ITEMS = [
        'menerima, memeriksa, dan menindaklanjuti pengaduan masyarakat',
        'produsen dan pengendali pembuatan konten atau berita tentang penyelenggaraan dan pengembangan pendidikan',
        'pengembangan Tri Dharma Perguruan Tinggi melalui media sosial',
        'melaksanakan pembinaan, pengawasan, dan evaluasi terhadap pelaksanaan pengelolaan informasi publik',
        'menyusun laporan semester dan tahunan pengelolaan informasi',
        'penyusunan dan pemutakhiran Daftar Informasi Publik',
    ];

    #[Test]
    public function published_page_shows_tugas_and_fungsi(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/tugas-fungsi-wewenang');

        $response->assertOk();
        $response->assertSee('PPID-TUGAS-FUNGSI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-duty-page', false);
        $response->assertSee('Tugas, Fungsi, dan Wewenang PPID', false);
        $response->assertSee('Profil PPID', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Profil', false);
        $response->assertSee('Tugas dan fungsi PPID Poltekkes Kemenkes Banten dalam pengelolaan, pelayanan, serta keterbukaan informasi publik.', false);
        $response->assertSee('Tugas PPID', false);
        $response->assertSee('Fungsi PPID', false);
        $response->assertSee(self::FUNGSI_TEXT, false);
        $response->assertDontSee('id="wewenang-ppid"', false);
        $response->assertDontSee('ppid-authority-section', false);
        $response->assertDontSee('>Wewenang PPID</h2>', false);

        foreach (self::TUGAS_ITEMS as $item) {
            $response->assertSee($item, false);
        }

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(1, preg_match_all('/<ol\b[^>]*class="ppid-duty-list"/', $html));
        $this->assertSame(6, preg_match_all('/<li\b[^>]*class="ppid-duty-item"/', $html));
        $this->assertSame(6, preg_match_all('/class="ppid-duty-item"/', $html));
    }

    #[Test]
    public function page_renders_even_when_body_is_empty(): void
    {
        Page::query()->create([
            'title' => 'Tugas, Fungsi, dan Wewenang PPID',
            'slug' => 'tugas-fungsi-wewenang',
            'excerpt' => 'Tugas dan fungsi PPID Poltekkes Kemenkes Banten dalam pengelolaan, pelayanan, serta keterbukaan informasi publik.',
            'body' => '',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/tugas-fungsi-wewenang');

        $response->assertOk();
        $response->assertSee('PPID-TUGAS-FUNGSI-DEPLOY-20260723-A', false);
        $response->assertSee('ppid-duty-page', false);
        $response->assertSee('Tugas PPID', false);
        $response->assertSee(self::TUGAS_ITEMS[0], false);
        $response->assertSee(self::FUNGSI_TEXT, false);
        $response->assertDontSee('id="wewenang-ppid"', false);
        $response->assertDontSee('ppid-authority-section', false);
        $this->assertSame(6, preg_match_all('/class="ppid-duty-item"/', $response->getContent()));
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
            'title' => 'Tugas, Fungsi, dan Wewenang PPID',
            'slug' => 'tugas-fungsi-wewenang',
            'excerpt' => 'Draft',
            'body' => '',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->get('/halaman/tugas-fungsi-wewenang')->assertNotFound();
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
        $response->assertDontSee('ppid-duty-page', false);
        $response->assertDontSee('PPID-TUGAS-FUNGSI-DEPLOY-20260723-A', false);
    }

    #[Test]
    public function menu_profil_points_to_correct_url(): void
    {
        $this->seed(MenuSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('pages.show', ['slug' => 'tugas-fungsi-wewenang'], false), false);
        $response->assertSee('Tugas, Fungsi, dan Wewenang', false);
    }

    #[Test]
    public function page_seeder_is_idempotent(): void
    {
        $this->seed(PageSeeder::class);
        $this->seed(PageSeeder::class);

        $page = Page::query()->where('slug', 'tugas-fungsi-wewenang')->first();

        $this->assertNotNull($page);
        $this->assertSame(1, Page::query()->where('slug', 'tugas-fungsi-wewenang')->count());
        $this->assertSame('published', $page->status);
        $this->assertSame('Tugas, Fungsi, dan Wewenang PPID', $page->title);
        $this->assertStringContainsString('<h2>Tugas PPID</h2>', (string) $page->body);
        $this->assertStringContainsString('<h2>Fungsi PPID</h2>', (string) $page->body);
        $this->assertStringContainsString('<ol>', (string) $page->body);
        $this->assertStringNotContainsString('<h2>Wewenang', (string) $page->body);
        $this->assertStringNotContainsString('<script', (string) $page->body);
    }

    #[Test]
    public function dangerous_html_in_body_is_not_rendered_as_script(): void
    {
        Page::query()->create([
            'title' => 'Tugas, Fungsi, dan Wewenang PPID',
            'slug' => 'tugas-fungsi-wewenang',
            'excerpt' => 'Ringkasan',
            'body' => '<h2>Tugas</h2><p>Aman</p><script>window.__xss=1</script><p onclick="alert(1)">Teks</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = $this->get('/halaman/tugas-fungsi-wewenang');

        $response->assertOk();
        $response->assertDontSee('<script>', false);
        $response->assertDontSee('onclick=', false);
        $response->assertDontSee('window.__xss', false);
        $response->assertSee('PPID-TUGAS-FUNGSI-DEPLOY-20260723-A', false);
        $response->assertSee('Tugas PPID', false);
        $response->assertSee(self::FUNGSI_TEXT, false);
    }

    #[Test]
    public function seo_meta_tags_are_present(): void
    {
        $this->seed(PageSeeder::class);

        $response = $this->get('/halaman/tugas-fungsi-wewenang');

        $response->assertOk();
        $response->assertSee('Informasi mengenai tugas dan fungsi PPID Poltekkes Kemenkes Banten dalam pelayanan serta pengelolaan keterbukaan informasi publik.', false);
        $response->assertSee('<title>', false);
        $response->assertSee('Tugas, Fungsi, dan Wewenang PPID', false);
    }
}
