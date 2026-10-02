<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\PublicNavigation;
use Database\Seeders\CategorySeeder;
use Database\Seeders\EnsureTataCaraPengaduanMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\TataCaraPengaduanPenyalahgunaanWewenangPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TataCaraPengaduanPenyalahgunaanWewenangPageTest extends TestCase
{
    use RefreshDatabase;

    private const SLUG = 'tata-cara-pengaduan-penyalahgunaan-wewenang';

    private const ALT = 'Tata Cara Pengaduan Penyalahgunaan Wewenang atau Pelanggaran oleh Pejabat Satuan Kerja Poltekkes Kemenkes Banten';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function public_page_renders_infographic_when_the_asset_exists(): void
    {
        $this->withInfographic(function () {
            $response = $this->get('/halaman/'.self::SLUG);
            $html = $response->getContent();

            $response->assertOk();
            $response->assertSee('PPID-TATA-CARA-PENGADUAN-WEWENANG-DEPLOY-20261002-A', false);
            $response->assertSee('Tata Cara Pengaduan Penyalahgunaan Wewenang', false);
            $response->assertSee('Diumumkan Berkala', false);
            $response->assertSee('Informasi tata cara pengaduan atas dugaan penyalahgunaan wewenang atau pelanggaran yang dilakukan oleh pejabat di lingkungan Poltekkes Kemenkes Banten.', false);
            $response->assertSee('tata-cara-pengaduan-penyalahgunaan-wewenang.png', false);
            $response->assertSee(self::ALT, false);
            $response->assertSee('Buka Gambar', false);
            $response->assertSee('Download Infografis', false);
            $response->assertSee('object-fit: contain', false);
            $response->assertSee('loading="lazy"', false);
            $response->assertDontSee('Infografis tata cara pengaduan belum tersedia.', false);
            $response->assertDontSee('<iframe', false);
            $response->assertDontSee('srv1863-files.hstgr.io', false);
            $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
            $this->assertSame(1, preg_match(
                '/<a[^>]*href="[^"]*tata-cara-pengaduan-penyalahgunaan-wewenang\.png"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
                $html
            ));
            $this->assertSame(1, preg_match(
                '/<a[^>]*href="[^"]*tata-cara-pengaduan-penyalahgunaan-wewenang\.png"[^>]*download="tata-cara-pengaduan-penyalahgunaan-wewenang\.png"/',
                $html
            ));
            $this->assertSame(1, preg_match(
                '/<img[^>]*alt="'.preg_quote(self::ALT, '/').'"[^>]*class="ppid-tata-cara-image"/',
                $html
            ));
        });
    }

    #[Test]
    public function public_page_shows_fallback_when_the_asset_is_missing(): void
    {
        $this->withoutInfographic(function () {
            $response = $this->get('/halaman/'.self::SLUG);

            $response->assertOk();
            $response->assertSee('Infografis tata cara pengaduan belum tersedia.', false);
            $response->assertSee('Tata Cara Pengaduan Penyalahgunaan Wewenang', false);
            $response->assertDontSee('tata-cara-pengaduan-penyalahgunaan-wewenang.png', false);
            $response->assertDontSee(self::ALT, false);
            $response->assertDontSee('<iframe', false);
        });
    }

    #[Test]
    public function navbar_places_the_item_directly_after_kalender_akademik_and_marks_it_active(): void
    {
        $this->seed(MenuSeeder::class);

        $html = $this->get('/halaman/'.self::SLUG)->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Kalender Akademik[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Kalender Akademik[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/href="[^"]*\/halaman\/'.self::SLUG.'"[^>]*aria-current="page"/',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?aria-current="page"[\s\S]{0,250}Diumumkan Berkala/u',
            $html
        ));
    }

    #[Test]
    public function database_navigation_gains_the_item_after_kalender_without_rebuilding_the_menu(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', 'Tata Cara Pengaduan Penyalahgunaan Wewenang')->delete();

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $before = $this->get('/')->assertOk()->getContent();
        $this->assertDoesNotMatchRegularExpression(
            '/href="[^"]*\/halaman\/'.self::SLUG.'"/',
            $before
        );

        $this->seed(EnsureTataCaraPengaduanMenuItemSeeder::class);
        $this->seed(EnsureTataCaraPengaduanMenuItemSeeder::class);
        PublicNavigation::forget();

        $this->assertSame(1, MenuItem::query()->where('label', 'Tata Cara Pengaduan Penyalahgunaan Wewenang')->count());

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString(route('pages.show', ['slug' => self::SLUG], false), $html);
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Kalender Akademik[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Kalender Akademik[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang/u',
            $html
        ));
    }

    #[Test]
    public function page_seeder_does_not_overwrite_admin_content(): void
    {
        $page = Page::query()->where('slug', self::SLUG)->firstOrFail();
        $page->update([
            'title' => 'Judul disunting admin',
            'excerpt' => 'Ringkasan disunting admin PPID.',
            'body' => '<p>Konten admin.</p>',
        ]);

        $this->seed(TataCaraPengaduanPenyalahgunaanWewenangPageSeeder::class);

        $fresh = $page->fresh();
        $this->assertSame('Judul disunting admin', $fresh->title);
        $this->assertSame('Ringkasan disunting admin PPID.', $fresh->excerpt);
        $this->assertSame('<p>Konten admin.</p>', $fresh->body);
        $this->assertSame(1, Page::query()->where('slug', self::SLUG)->count());
    }

    #[Test]
    public function related_routes_stay_available_and_the_page_route_is_not_duplicated(): void
    {
        $this->seed(CategorySeeder::class);

        $this->get('/halaman/kalender-akademik')->assertOk();
        $this->get('/halaman/kebijakan-mengikat-bagi-publik')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();

        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => $route->uri() === 'halaman/{slug}' || $route->getName() === 'pages.show');

        $this->assertCount(1, $routes);
        $this->assertSame('pages.show', $routes->first()->getName());
        $this->assertSame('halaman/{slug}', $routes->first()->uri());
    }

    private function withInfographic(callable $callback): void
    {
        $path = public_path('docs/tata-cara-pengaduan-penyalahgunaan-wewenang.png');
        $existed = is_file($path);

        if (! $existed) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }

            file_put_contents($path, base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
            ));
        }

        try {
            $callback();
        } finally {
            if (! $existed && is_file($path)) {
                unlink($path);
            }
        }
    }

    private function withoutInfographic(callable $callback): void
    {
        $path = public_path('docs/tata-cara-pengaduan-penyalahgunaan-wewenang.png');
        $backup = $path.'.bak-test';
        $moved = false;

        if (is_file($path)) {
            rename($path, $backup);
            $moved = true;
        }

        try {
            $callback();
        } finally {
            if ($moved && is_file($backup)) {
                rename($backup, $path);
            }
        }
    }
}
