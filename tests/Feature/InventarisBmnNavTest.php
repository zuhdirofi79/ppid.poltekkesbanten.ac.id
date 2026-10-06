<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\SiteSetting;
use App\Support\PublicNavigation;
use Database\Seeders\EnsureInventarisBmnMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventarisBmnNavTest extends TestCase
{
    use RefreshDatabase;

    private const LABEL = 'Inventaris Barang Milik Negara';

    private const URL = 'https://bmn.poltekkesbanten.ac.id/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function informasi_dropdown_links_bmn_inventory_directly_under_statistik_keuangan(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(self::LABEL, false);
        $response->assertSee(self::URL, false);
        $response->assertSee('Information Desk', false);
        $response->assertSee('Statistik Keuangan', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('aria-label="Inventaris Barang Milik Negara Poltekkes Kemenkes Banten — buka Sistem BMN"', false);
        $response->assertDontSee('/halaman/inventaris-barang-milik-negara', false);
        $response->assertDontSee('http://bmn.poltekkesbanten.ac.id', false);

        $html = $response->getContent();
        $this->assertSame(2, preg_match_all('/>\s*'.preg_quote(self::LABEL, '/').'\s*</', $html));
        $this->assertSame(2, preg_match_all(
            '/<a[^>]*href="'.preg_quote(self::URL, '/').'"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Statistik Keuangan[\s\S]+?'.preg_quote(self::LABEL, '/').'[\s\S]+?Daftar Informasi Publik[\s\S]+?Rapat Koordinasi PPID[\s\S]+?aria-label="Navigasi seluler"/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Statistik Keuangan[\s\S]+?'.preg_quote(self::LABEL, '/').'/u',
            $html
        ));
        $this->assertSame(1, preg_match('/diumumkan-berkala-dropdown[\s\S]*?<\/ul>/u', $html, $berkala));
        $this->assertStringNotContainsString(self::LABEL, $berkala[0]);

        $finance = strpos($html, 'Statistik Keuangan');
        $inventory = strpos($html, self::LABEL);
        $this->assertNotFalse($finance);
        $this->assertNotFalse($inventory);
        $this->assertLessThan($inventory, $finance);
    }

    #[Test]
    public function database_navigation_inserts_the_external_link_after_statistik_keuangan(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', self::LABEL)->delete();

        $this->seed(EnsureInventarisBmnMenuItemSeeder::class);
        $this->seed(EnsureInventarisBmnMenuItemSeeder::class);

        $parent = MenuItem::query()->whereNull('parent_id')->where('label', 'Informasi')->firstOrFail();
        $finance = MenuItem::query()
            ->where('parent_id', $parent->id)
            ->where('label', 'Statistik Keuangan')
            ->firstOrFail();
        $item = MenuItem::query()->where('label', self::LABEL)->firstOrFail();

        $this->assertSame(1, MenuItem::query()->where('label', self::LABEL)->count());
        $this->assertSame($parent->id, $item->parent_id);
        $this->assertSame('url', $item->type);
        $this->assertSame(self::URL, $item->url);
        $this->assertNull($item->route_name);
        $this->assertGreaterThan($finance->sort_order, $item->sort_order);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(2, preg_match_all(
            '/<a[^>]*href="'.preg_quote(self::URL, '/').'"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Statistik Keuangan[\s\S]+?'.preg_quote(self::LABEL, '/').'/u',
            $html
        ));
        $this->assertStringNotContainsString('/halaman/inventaris-barang-milik-negara', $html);
    }
}
