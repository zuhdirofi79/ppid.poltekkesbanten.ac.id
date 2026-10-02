<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\ServiceLink;
use App\Support\PublicNavigation;
use Database\Seeders\EnsurePengaduanPenyalahgunaanWewenangMenuSeeder;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlurPengajuanSengketaPageTest extends TestCase
{
    use RefreshDatabase;

    private const INFORMATION_FORM = '1FAIpQLSffivoxDcFT-iDrc8eb1GKvkqhx-LenWuaQkE826D5uSbOG3A';

    private const OBJECTION_FORM = '1FAIpQLScE8lH-ihPiy_FbzWJ6zQIk8eiFgd9bUeckJerQPsNHNIMbew';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function page_shows_the_official_complaint_procedure(): void
    {
        $response = $this->get('/layanan/alur-pengajuan-sengketa');

        $response->assertOk();
        $response->assertSee('<title>Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran | PPID Poltekkes Kemenkes Banten</title>', false);
        $response->assertSee('Informasi tata cara pengaduan dugaan penyalahgunaan wewenang, pelanggaran, pungutan liar, konflik kepentingan, gratifikasi, dan pelanggaran pelayanan publik di lingkungan Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Layanan PPID', false);
        $response->assertSee('Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran', false);
        $response->assertSee('bebas dari penyalahgunaan wewenang', false);
        $response->assertSee('Jenis Pengaduan', false);
        $response->assertSee('Tata Cara Penyampaian Pengaduan', false);
        $response->assertSee('Kanal Pengaduan', false);
        $response->assertSee('Proses Penanganan Pengaduan', false);
        $response->assertSee('Kerahasiaan Pelapor', false);
        $response->assertSee('Komitmen Poltekkes Kemenkes Banten', false);
        $response->assertSee('https://wbs.kemkes.go.id/', false);
        $response->assertSee('https://www.lapor.go.id/', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Form Pengaduan Sedang Disiapkan', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Layanan', false);
        $response->assertSee('aria-label="Navigasi utama"', false);
        $response->assertSee('<footer', false);
        $response->assertDontSee(self::INFORMATION_FORM, false);
        $response->assertDontSee(self::OBJECTION_FORM, false);
        $response->assertDontSee('javascript:void', false);
        $response->assertDontSee('href=""', false);
        $response->assertDontSee('komisiinformasi.go.id', false);
        $response->assertDontSee('hstgr.io', false);
        $response->assertDontSee('Pengajuan sengketa dilakukan setelah', false);
        $response->assertDontSee('ppid-alur-sengketa-page', false);
        $response->assertDontSee('Mekanisme Pendaftaran Sengketa Informasi Publik', false);
        $response->assertDontSee('Identitas dijamin 100% rahasia', false);

        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
        $this->assertSame(0, preg_match_all('/<a\b[^>]*href="\s*"/i', $response->getContent()));
    }

    #[Test]
    public function configured_complaint_form_is_linked_and_other_forms_stay_unused(): void
    {
        config(['ppid.complaint_request.external_url' => 'https://example.test/form-pengaduan-wewenang']);

        $response = $this->get('/layanan/alur-pengajuan-sengketa');

        $response->assertOk();
        $response->assertSee('https://example.test/form-pengaduan-wewenang', false);
        $response->assertSee('Buka Form Pengaduan', false);
        $response->assertDontSee('Form Pengaduan Sedang Disiapkan', false);
        $response->assertDontSee(self::INFORMATION_FORM, false);
        $response->assertDontSee(self::OBJECTION_FORM, false);
    }

    #[Test]
    public function legacy_pengaduan_service_redirects_to_the_existing_route(): void
    {
        $this->get('/layanan/pengaduan-penyalahgunaan-wewenang')
            ->assertStatus(301)
            ->assertRedirect('/layanan/alur-pengajuan-sengketa');
    }

    #[Test]
    public function layanan_menu_uses_the_existing_pengaduan_label_once(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(2, preg_match_all(
            '/href="[^"]*\/layanan\/alur-pengajuan-sengketa"[^>]*>\s*Pengaduan Penyalahgunaan Wewenang/u',
            $html
        ));
        $this->assertStringNotContainsString(
            'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi',
            $html
        );
    }

    #[Test]
    public function database_menu_keeps_one_pengaduan_item_on_the_existing_route(): void
    {
        $this->seed(MenuSeeder::class);

        $item = MenuItem::query()->where('label', 'Pengaduan Penyalahgunaan Wewenang')->firstOrFail();
        $item->update(['route_params' => ['slug' => 'pengaduan-penyalahgunaan-wewenang']]);
        MenuItem::query()->create([
            'menu_id' => $item->menu_id,
            'parent_id' => $item->parent_id,
            'label' => 'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi',
            'type' => 'route',
            'route_name' => 'services.show',
            'route_params' => ['slug' => 'alur-pengajuan-sengketa'],
            'sort_order' => $item->sort_order + 1,
            'is_active' => true,
        ]);
        ServiceLink::query()->where('slug', 'alur-pengajuan-sengketa')->update([
            'title' => 'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi',
        ]);
        PublicNavigation::forget();

        $this->seed(EnsurePengaduanPenyalahgunaanWewenangMenuSeeder::class);
        $this->seed(EnsurePengaduanPenyalahgunaanWewenangMenuSeeder::class);

        $this->assertSame(1, MenuItem::query()->where('label', 'Pengaduan Penyalahgunaan Wewenang')->count());
        $this->assertSame(0, MenuItem::query()->where('label', 'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi')->count());
        $this->assertSame(
            'alur-pengajuan-sengketa',
            MenuItem::query()->where('label', 'Pengaduan Penyalahgunaan Wewenang')->firstOrFail()->route_params['slug']
        );
        $this->assertSame(
            'Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran',
            ServiceLink::query()->where('slug', 'alur-pengajuan-sengketa')->value('title')
        );
    }

    #[Test]
    public function existing_alur_pages_are_unchanged(): void
    {
        $permohonan = $this->get('/layanan/alur-permohonan-informasi');
        $permohonan->assertOk();
        $permohonan->assertSee('ppid-alur-page', false);
        $permohonan->assertDontSee('ppid-pengaduan-page', false);

        $keberatan = $this->get('/layanan/alur-pengajuan-keberatan');
        $keberatan->assertOk();
        $keberatan->assertSee('ppid-alur-keberatan-page', false);
        $keberatan->assertDontSee('ppid-pengaduan-page', false);
    }
}
