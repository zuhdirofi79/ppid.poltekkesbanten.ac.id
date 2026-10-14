<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\DaftarInformasiPublik;
use App\Support\InformationDesk;
use App\Support\PublicNavigation;
use App\Support\StatistikKeuangan;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DaftarInformasiPublikPageSeeder;
use Database\Seeders\EnsureDaftarInformasiPublikMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DaftarInformasiPublikPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function daftar_informasi_publik_page_shows_the_pdf_card_without_an_iframe(): void
    {
        $response = $this->get('/halaman/'.DaftarInformasiPublik::SLUG);

        $response->assertOk();
        $response->assertSee('PPID-DAFTAR-INFORMASI-PUBLIK-DEPLOY-20261006-A', false);
        $response->assertSee(DaftarInformasiPublik::TITLE, false);
        $response->assertSee(DaftarInformasiPublik::EYEBROW, false);
        $response->assertSee(DaftarInformasiPublik::EXCERPT, false);
        $response->assertSee(DaftarInformasiPublik::SECTION_HEADING, false);
        $response->assertSee(DaftarInformasiPublik::DOCUMENT_TITLE, false);
        $response->assertSee(DaftarInformasiPublik::DOCUMENT_DESCRIPTION, false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee(DaftarInformasiPublik::FORMAT_LABEL, false);
        $response->assertSee(DaftarInformasiPublik::OPEN_LABEL, false);
        $response->assertSee(DaftarInformasiPublik::DOWNLOAD_LABEL, false);
        $response->assertSee('DAFTAR%20INFORMASI%20PUBLIK%202026.pdf', false);
        $response->assertSee('docs/DAFTAR%20INFORMASI%20PUBLIK%202026.pdf', false);
        $response->assertDontSee('DAFTAR INFORMASI PUBLIK.docx', false);
        $response->assertDontSee('DAFTAR%20INFORMASI%20PUBLIK.docx', false);
        $response->assertDontSee('DOCX / Dokumen', false);
        $response->assertSee('download="'.DaftarInformasiPublik::FILENAME.'"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('aria-label="'.DaftarInformasiPublik::OPEN_ARIA.'"', false);
        $response->assertSee('aria-label="'.DaftarInformasiPublik::DOWNLOAD_ARIA.'"', false);
        $response->assertDontSee('<iframe', false);
        $response->assertDontSee('docs.google.com', false);
        $response->assertDontSee('view.officeapps.live.com', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(2, substr_count($html, 'docs/DAFTAR%20INFORMASI%20PUBLIK%202026.pdf'));
        $this->assertSame(1, preg_match(
            '/aria-label="Breadcrumb"[\s\S]+?Beranda[\s\S]+?Informasi[\s\S]+?Daftar Informasi Publik/u',
            $html
        ));
        $this->assertSame(2, preg_match_all(
            '/href="[^"]*\/halaman\/'.DaftarInformasiPublik::SLUG.'"[^>]*aria-current="page"/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?aria-current="page"[^>]*>\s*Informasi\s*</u',
            $html
        ));
    }

    #[Test]
    public function informasi_menu_places_the_item_directly_after_inventaris_barang_milik_negara(): void
    {
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?>\s*Daftar Informasi Publik\s*<[\s\S]+?aria-label="Navigasi seluler"/u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?>\s*Daftar Informasi Publik\s*</u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Statistik Keuangan[\s\S]+?Inventaris Barang Milik Negara[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID[\s\S]+?aria-label="Navigasi seluler"/u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Inventaris Barang Milik Negara[\s\S]+?Daftar Informasi Publik/u',
            $home
        ));
        $this->assertSame(1, preg_match('/diumumkan-berkala-dropdown[\s\S]*?<\/ul>/u', $home, $berkala));
        $this->assertStringNotContainsString('/halaman/'.DaftarInformasiPublik::SLUG, $berkala[0]);

        $this->get('/informasi-publik/setiap-saat')->assertOk();
        $this->get('/halaman/'.StatistikKeuangan::SLUG)->assertOk()->assertSee('Statistik Keuangan', false);
        $this->get('/halaman/'.InformationDesk::SLUG)->assertOk()->assertSee('Information Desk', false);
    }

    #[Test]
    public function database_navigation_inserts_the_item_after_inventaris_barang_milik_negara(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', DaftarInformasiPublik::SLUG)
            ->delete();

        $this->seed(EnsureDaftarInformasiPublikMenuItemSeeder::class);
        $this->seed(EnsureDaftarInformasiPublikMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $inventory = MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('label', 'Inventaris Barang Milik Negara')
            ->firstOrFail();
        $item = MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', DaftarInformasiPublik::SLUG)
            ->firstOrFail();

        $this->assertSame(1, MenuItem::query()
            ->where('route_name', 'pages.show')
            ->where('route_params->slug', DaftarInformasiPublik::SLUG)
            ->count());
        $informasiPublik = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi Publik')->firstOrFail();
        $this->assertSame(1, MenuItem::query()
            ->where('parent_id', $informasiPublik->id)
            ->where('label', 'Daftar Informasi Publik')
            ->where('route_name', 'information.index')
            ->count());
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame('route', $item->type);
        $this->assertSame('pages.show', $item->route_name);
        $this->assertSame(DaftarInformasiPublik::SLUG, $item->route_params['slug']);
        $this->assertGreaterThan($inventory->sort_order, $item->sort_order);
        $this->assertSame(0, MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('sort_order', '>', $inventory->sort_order)
            ->where('sort_order', '<', $item->sort_order)
            ->count());

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Inventaris Barang Milik Negara[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID[\s\S]+?aria-label="Navigasi seluler"/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Inventaris Barang Milik Negara[\s\S]+?Daftar Informasi Publik/u',
            $html
        ));
    }

    #[Test]
    public function page_seeder_is_idempotent_and_does_not_overwrite_admin_copy(): void
    {
        $this->seed(DaftarInformasiPublikPageSeeder::class);

        $page = Page::query()->where('slug', DaftarInformasiPublik::SLUG)->firstOrFail();
        $page->update([
            'title' => 'Judul admin',
            'excerpt' => 'Cuplikan admin',
        ]);

        $this->seed(DaftarInformasiPublikPageSeeder::class);

        $this->assertSame(1, Page::query()->where('slug', DaftarInformasiPublik::SLUG)->count());
        $page->refresh();
        $this->assertSame('Judul admin', $page->title);
        $this->assertSame('Cuplikan admin', $page->excerpt);
    }
}
