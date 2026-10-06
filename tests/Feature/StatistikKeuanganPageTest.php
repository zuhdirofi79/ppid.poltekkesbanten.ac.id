<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\InformationDesk;
use App\Support\PublicNavigation;
use App\Support\ServiceSummaryReport;
use App\Support\StatistikKeuangan;
use Database\Seeders\EnsureStatistikKeuanganMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\StatistikKeuanganPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StatistikKeuanganPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function statistik_keuangan_page_shows_the_official_dipa_document(): void
    {
        $response = $this->get('/halaman/'.StatistikKeuangan::SLUG);

        $response->assertOk();
        $response->assertSee('PPID-STATISTIK-KEUANGAN-DEPLOY-20261006-A', false);
        $response->assertSee(StatistikKeuangan::TITLE, false);
        $response->assertSee(StatistikKeuangan::EXCERPT, false);
        $response->assertSee(StatistikKeuangan::EXPLANATION, false);
        $response->assertSee(StatistikKeuangan::DOCUMENT_HEADING, false);
        $response->assertSee(StatistikKeuangan::DOCUMENT_TITLE, false);
        $response->assertSee(StatistikKeuangan::DOCUMENT_DESCRIPTION, false);
        $response->assertSee('Periode Data: '.StatistikKeuangan::PERIOD, false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee('PDF / Dokumen Keuangan', false);
        $response->assertSee('Lihat Dokumen', false);
        $response->assertSee('Unduh PDF', false);
        $response->assertSee('DIPA%20POLKESTEN.pdf', false);
        $response->assertSee('docs/DIPA%20POLKESTEN.pdf', false);
        $response->assertSee('download="'.StatistikKeuangan::FILENAME.'"', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('aria-label="Lihat DIPA Poltekkes Kemenkes Banten Tahun Anggaran 2026"', false);
        $response->assertSee('aria-label="Unduh DIPA Poltekkes Kemenkes Banten Tahun Anggaran 2026"', false);
        $response->assertSee('title="DIPA Tahun Anggaran 2026 Poltekkes Kemenkes Banten"', false);
        $response->assertSee('Pratinjau Dokumen', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Informasi', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(4, substr_count($html, 'docs/DIPA%20POLKESTEN.pdf'));
        $this->assertStringContainsString('<iframe', $html);
        $this->assertStringNotContainsString('Total Anggaran', $html);
        $this->assertStringNotContainsString('Rp', $html);
        $this->assertSame(1, preg_match(
            '/aria-label="Breadcrumb"[\s\S]+?Beranda[\s\S]+?Informasi[\s\S]+?Statistik Keuangan/u',
            $html
        ));
    }

    #[Test]
    public function informasi_menu_places_statistik_keuangan_directly_under_information_desk(): void
    {
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertSame(2, substr_count($home, 'Statistik Keuangan'));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Statistik Layanan Informasi[\s\S]+?Information Desk[\s\S]+?Statistik Keuangan[\s\S]+?aria-label="Navigasi seluler"/u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Information Desk[\s\S]+?Statistik Keuangan/u',
            $home
        ));
        $this->assertSame(1, preg_match('/diumumkan-berkala-dropdown[\s\S]*?<\/ul>/u', $home, $berkalaDropdown));
        $this->assertStringNotContainsString('Statistik Keuangan', $berkalaDropdown[0]);

        $desk = strpos($home, 'Information Desk');
        $finance = strpos($home, 'Statistik Keuangan');
        $this->assertNotFalse($desk);
        $this->assertNotFalse($finance);
        $this->assertLessThan($finance, $desk);

        $page = $this->get('/halaman/'.StatistikKeuangan::SLUG)->assertOk()->getContent();
        $this->assertSame(2, preg_match_all(
            '/href="[^"]*\/halaman\/'.StatistikKeuangan::SLUG.'"[^>]*aria-current="page"/u',
            $page
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?aria-current="page"[^>]*>\s*Informasi\s*</u',
            $page
        ));
    }

    #[Test]
    public function information_desk_and_service_statistics_pages_stay_available(): void
    {
        $this->get('/halaman/'.InformationDesk::SLUG)
            ->assertOk()
            ->assertSee('Information Desk', false)
            ->assertDontSee('PPID-STATISTIK-KEUANGAN-DEPLOY-20261006-A', false);

        $this->get('/halaman/'.ServiceSummaryReport::SLUG)
            ->assertOk()
            ->assertSee('Ringkasan Laporan Layanan Informasi', false);
    }

    #[Test]
    public function database_navigation_inserts_the_item_after_information_desk(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', StatistikKeuangan::TITLE)->delete();

        $this->seed(EnsureStatistikKeuanganMenuItemSeeder::class);
        $this->seed(EnsureStatistikKeuanganMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $desk = MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('label', 'Information Desk')
            ->firstOrFail();
        $item = MenuItem::query()->where('label', StatistikKeuangan::TITLE)->firstOrFail();

        $this->assertSame(1, MenuItem::query()->where('label', StatistikKeuangan::TITLE)->count());
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame('pages.show', $item->route_name);
        $this->assertSame(StatistikKeuangan::SLUG, $item->route_params['slug']);
        $this->assertGreaterThan($desk->sort_order, $item->sort_order);

        $berkala = MenuItem::query()->whereNull('parent_id')->where('label', 'Diumumkan Berkala')->firstOrFail();
        $this->assertSame(0, MenuItem::query()
            ->where('parent_id', $berkala->id)
            ->where('label', StatistikKeuangan::TITLE)
            ->count());

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Information Desk[\s\S]+?Statistik Keuangan[\s\S]+?aria-label="Navigasi seluler"/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Information Desk[\s\S]+?Statistik Keuangan/u',
            $html
        ));
    }

    #[Test]
    public function page_seeder_is_idempotent_and_does_not_overwrite_admin_copy(): void
    {
        $this->seed(StatistikKeuanganPageSeeder::class);

        $page = Page::query()->where('slug', StatistikKeuangan::SLUG)->firstOrFail();
        $page->update([
            'title' => 'Judul admin',
            'excerpt' => 'Cuplikan admin',
        ]);

        $this->seed(StatistikKeuanganPageSeeder::class);

        $this->assertSame(1, Page::query()->where('slug', StatistikKeuangan::SLUG)->count());
        $page->refresh();
        $this->assertSame('Judul admin', $page->title);
        $this->assertSame('published', $page->status);
    }
}
