<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Support\InformationDesk;
use App\Support\PublicNavigation;
use App\Support\ServiceSummaryReport;
use Database\Seeders\EnsureInformationDeskMenuItemSeeder;
use Database\Seeders\InformationDeskPageSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InformationDeskPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function information_desk_page_is_published_with_facility_copy_and_photos(): void
    {
        $response = $this->get('/halaman/'.InformationDesk::SLUG);

        $response->assertOk();
        $response->assertSee('Information Desk', false);
        $response->assertSee('Meja, kursi, kursi tunggu dan sarana penerimaan', false);
        $response->assertSee('Fasilitas dan kelengkapan layanan informasi publik PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Information Desk PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee(InformationDesk::META_DESCRIPTION, false);
        $response->assertSee('/halaman/'.InformationDesk::SLUG, false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertStringContainsString(
            'Information Desk | '.config('ppid.name'),
            $html
        );

        foreach (InformationDesk::photos() as $photo) {
            $response->assertSee($photo['path'], false);
            $response->assertSee($photo['alt'], false);
            $response->assertSee($photo['caption'], false);
        }
    }

    #[Test]
    public function informasi_dropdown_places_information_desk_directly_under_statistik_on_desktop_and_mobile(): void
    {
        $home = $this->get('/')->assertOk()->getContent();

        $this->assertSame(2, preg_match_all('/>\s*Information Desk\s*</', $home));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Statistik Layanan Informasi[\s\S]+?Information Desk[\s\S]+?aria-label="Navigasi seluler"/u',
            $home
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Statistik Layanan Informasi[\s\S]+?Information Desk/u',
            $home
        ));
        $this->assertSame(2, preg_match_all(
            '/href="[^"]*\/halaman\/'.ServiceSummaryReport::SLUG.'"[^>]*>\s*Statistik Layanan Informasi/u',
            $home
        ));
        $this->assertSame(1, preg_match('/diumumkan-berkala-dropdown[\s\S]*?<\/ul>/u', $home, $berkalaDropdown));
        $this->assertStringNotContainsString('Information Desk', $berkalaDropdown[0]);

        $page = $this->get('/halaman/'.InformationDesk::SLUG)->assertOk()->getContent();
        $this->assertSame(2, preg_match_all(
            '/href="[^"]*\/halaman\/'.InformationDesk::SLUG.'"[^>]*aria-current="page"/u',
            $page
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?aria-current="page"[^>]*>\s*Informasi\s*</u',
            $page
        ));
    }

    #[Test]
    public function database_navigation_inserts_information_desk_under_informasi_after_statistik(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', InformationDesk::TITLE)->delete();

        $this->seed(EnsureInformationDeskMenuItemSeeder::class);
        $this->seed(EnsureInformationDeskMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $statistik = MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('label', 'Statistik Layanan Informasi')
            ->firstOrFail();
        $item = MenuItem::query()->where('label', InformationDesk::TITLE)->firstOrFail();

        $this->assertSame(1, MenuItem::query()->where('label', InformationDesk::TITLE)->count());
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame('pages.show', $item->route_name);
        $this->assertSame(InformationDesk::SLUG, $item->route_params['slug']);
        $this->assertSame(ServiceSummaryReport::SLUG, $statistik->route_params['slug']);
        $this->assertGreaterThan($statistik->sort_order, $item->sort_order);

        $berkalaParent = MenuItem::query()->whereNull('parent_id')->where('label', 'Diumumkan Berkala')->firstOrFail();
        $this->assertSame(0, MenuItem::query()
            ->where('parent_id', $berkalaParent->id)
            ->where('label', InformationDesk::TITLE)
            ->count());

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Statistik Layanan Informasi[\s\S]+?Information Desk[\s\S]+?aria-label="Navigasi seluler"/u',
            $html
        ));
    }

    #[Test]
    public function misplaced_information_desk_item_is_moved_under_informasi(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', InformationDesk::TITLE)->delete();

        $berkala = MenuItem::query()->whereNull('parent_id')->where('label', 'Diumumkan Berkala')->firstOrFail();
        MenuItem::query()->create([
            'menu_id' => $berkala->menu_id,
            'parent_id' => $berkala->id,
            'label' => InformationDesk::TITLE,
            'type' => 'route',
            'route_name' => 'pages.show',
            'route_params' => ['slug' => InformationDesk::SLUG],
            'sort_order' => 999,
            'is_active' => true,
        ]);

        $this->seed(EnsureInformationDeskMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $item = MenuItem::query()->where('label', InformationDesk::TITLE)->firstOrFail();
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame(0, MenuItem::query()->where('parent_id', $berkala->id)->where('label', InformationDesk::TITLE)->count());
    }

    #[Test]
    public function page_seeder_is_idempotent_and_keeps_the_published_record(): void
    {
        $this->seed(InformationDeskPageSeeder::class);

        $this->assertSame(1, Page::query()->where('slug', InformationDesk::SLUG)->count());
        $page = Page::query()->where('slug', InformationDesk::SLUG)->firstOrFail();
        $this->assertSame(InformationDesk::TITLE, $page->title);
        $this->assertSame('published', $page->status);
        $this->assertSame(InformationDesk::META_DESCRIPTION, $page->meta_description);
    }
}
