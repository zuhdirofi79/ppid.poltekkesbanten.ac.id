<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\DaftarInformasiPublik;
use App\Support\PublicNavigation;
use App\Support\RapatKoordinasiPpid;
use Database\Seeders\CategorySeeder;
use Database\Seeders\EnsureRapatKoordinasiPpidMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\RapatKoordinasiPpidPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RapatKoordinasiPpidPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function rapat_koordinasi_page_shows_the_pdf_preview_when_the_file_exists(): void
    {
        $this->withTemporaryPdf(function () {
            $response = $this->get('/halaman/'.RapatKoordinasiPpid::SLUG);

            $response->assertOk();
            $response->assertSee('PPID-RAPAT-KOORDINASI-PPID-DEPLOY-20261006-A', false);
            $response->assertSee(RapatKoordinasiPpid::TITLE, false);
            $response->assertSee(RapatKoordinasiPpid::EYEBROW, false);
            $response->assertSee(RapatKoordinasiPpid::EXCERPT, false);
            $response->assertSee(RapatKoordinasiPpid::SECTION_HEADING, false);
            $response->assertSee(RapatKoordinasiPpid::DOCUMENT_DESCRIPTION, false);
            $response->assertSee('TERSEDIA', false);
            $response->assertSee(RapatKoordinasiPpid::FORMAT_LABEL, false);
            $response->assertSee(RapatKoordinasiPpid::OPEN_LABEL, false);
            $response->assertSee(RapatKoordinasiPpid::DOWNLOAD_LABEL, false);
            $response->assertSee('RAPAT%20KOORDINASI%20PPID.pdf', false);
            $response->assertSee('docs/RAPAT%20KOORDINASI%20PPID.pdf', false);
            $response->assertSee('download="'.RapatKoordinasiPpid::FILENAME.'"', false);
            $response->assertSee('target="_blank"', false);
            $response->assertSee('rel="noopener noreferrer"', false);
            $response->assertSee('aria-label="'.RapatKoordinasiPpid::OPEN_ARIA.'"', false);
            $response->assertSee('aria-label="'.RapatKoordinasiPpid::DOWNLOAD_ARIA.'"', false);
            $response->assertSee(RapatKoordinasiPpid::BROWSER_FALLBACK, false);
            $response->assertSee('ppid-sk-preview-frame', false);
            $response->assertSee('<iframe', false);
            $response->assertDontSee(RapatKoordinasiPpid::UNAVAILABLE, false);
            $response->assertDontSee('docs.google.com', false);

            $html = $response->getContent();
            $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
            $this->assertSame(3, substr_count($html, 'docs/RAPAT%20KOORDINASI%20PPID.pdf'));
            $this->assertSame(1, preg_match(
                '/aria-label="Breadcrumb"[\s\S]+?Beranda[\s\S]+?Informasi[\s\S]+?Rapat Koordinasi PPID/u',
                $html
            ));
            $this->assertSame(2, preg_match_all(
                '/href="[^"]*\/halaman\/'.RapatKoordinasiPpid::SLUG.'"[^>]*aria-current="page"/u',
                $html
            ));
            $this->assertSame(1, preg_match(
                '/aria-label="Navigasi utama"[\s\S]+?aria-current="page"[^>]*>\s*Informasi\s*</u',
                $html
            ));
        });
    }

    #[Test]
    public function rapat_koordinasi_page_stays_available_when_the_pdf_is_missing(): void
    {
        $this->withoutPdf(function () {
            $this->get('/halaman/'.RapatKoordinasiPpid::SLUG)
                ->assertOk()
                ->assertSee(RapatKoordinasiPpid::SECTION_HEADING, false)
                ->assertSee(RapatKoordinasiPpid::UNAVAILABLE, false)
                ->assertDontSee('<iframe', false)
                ->assertDontSee('TERSEDIA', false);
        });
    }

    #[Test]
    public function informasi_menu_places_the_item_directly_after_daftar_informasi_publik(): void
    {
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID[\s\S]+?aria-label="Navigasi seluler"/u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Inventaris Barang Milik Negara[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID/u',
            $home
        ));
        $this->assertSame(1, preg_match('/diumumkan-berkala-dropdown[\s\S]*?<\/ul>/u', $home, $berkala));
        $this->assertStringNotContainsString('/halaman/'.RapatKoordinasiPpid::SLUG, $berkala[0]);

        $this->get('/halaman/'.DaftarInformasiPublik::SLUG)->assertOk()->assertSee('Daftar Informasi Publik', false);
        $this->get('/informasi-publik/setiap-saat')->assertOk();
    }

    #[Test]
    public function database_navigation_inserts_the_item_after_daftar_informasi_publik(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', RapatKoordinasiPpid::SLUG)
            ->delete();

        $this->seed(EnsureRapatKoordinasiPpidMenuItemSeeder::class);
        $this->seed(EnsureRapatKoordinasiPpidMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $dip = MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', DaftarInformasiPublik::SLUG)
            ->firstOrFail();
        $item = MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', RapatKoordinasiPpid::SLUG)
            ->firstOrFail();

        $this->assertSame(1, MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', RapatKoordinasiPpid::SLUG)
            ->count());
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame('route', $item->type);
        $this->assertSame('pages.show', $item->route_name);
        $this->assertGreaterThan($dip->sort_order, $item->sort_order);
        $this->assertSame(0, MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('sort_order', '>', $dip->sort_order)
            ->where('sort_order', '<', $item->sort_order)
            ->count());

        $informasiPublik = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi Publik')->firstOrFail();
        $this->assertSame(1, MenuItem::query()
            ->where('parent_id', $informasiPublik->id)
            ->where('label', 'Daftar Informasi Publik')
            ->where('route_name', 'information.index')
            ->count());

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID[\s\S]+?aria-label="Navigasi seluler"/u',
            $html
        ));
    }

    #[Test]
    public function page_seeder_is_idempotent_and_does_not_overwrite_admin_copy(): void
    {
        $this->seed(RapatKoordinasiPpidPageSeeder::class);

        $page = Page::query()->where('slug', RapatKoordinasiPpid::SLUG)->firstOrFail();
        $page->update([
            'title' => 'Judul admin',
            'excerpt' => 'Cuplikan admin',
        ]);

        $this->seed(RapatKoordinasiPpidPageSeeder::class);

        $this->assertSame(1, Page::query()->where('slug', RapatKoordinasiPpid::SLUG)->count());
        $page->refresh();
        $this->assertSame('Judul admin', $page->title);
        $this->assertSame('Cuplikan admin', $page->excerpt);
    }

    private function withTemporaryPdf(callable $callback): void
    {
        $path = public_path('docs/'.RapatKoordinasiPpid::FILENAME);
        $existed = is_file($path);

        if (! $existed) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            file_put_contents($path, "%PDF-1.4\n%%EOF\n");
        }

        try {
            $callback();
        } finally {
            if (! $existed && is_file($path)) {
                unlink($path);
            }
        }
    }

    private function withoutPdf(callable $callback): void
    {
        $path = public_path('docs/'.RapatKoordinasiPpid::FILENAME);
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
