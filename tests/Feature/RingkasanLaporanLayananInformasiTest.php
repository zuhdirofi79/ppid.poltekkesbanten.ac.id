<?php

namespace Tests\Feature;

use App\Models\InformationServiceSummary;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\PublicNavigation;
use App\Support\ServiceSummaryReport;
use Database\Seeders\CategorySeeder;
use Database\Seeders\EnsureRingkasanLaporanLayananInformasiMenuItemSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\RingkasanLaporanLayananInformasiPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RingkasanLaporanLayananInformasiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
    }

    #[Test]
    public function public_page_is_ok_and_does_not_invent_numbers_when_empty(): void
    {
        $response = $this->get('/halaman/'.ServiceSummaryReport::SLUG);

        $response->assertOk();
        $response->assertSee('Ringkasan Laporan Layanan Informasi Publik', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee(ServiceSummaryReport::UNAVAILABLE, false);
        $response->assertDontSee('128', false);
    }

    #[Test]
    public function year_filter_shows_only_the_published_record_and_keeps_null_distinct_from_zero(): void
    {
        InformationServiceSummary::query()->create([
            'year' => 2026,
            'total_received' => 10,
            'fulfilled_full' => null,
            'fulfilled_partial' => 2,
            'rejected' => 0,
            'average_response_days' => null,
            'response_time_description' => 'Sebagian besar permohonan diselesaikan dalam waktu tiga hari.',
            'rejection_reasons' => "Informasi dikecualikan\nDokumen tidak dikuasai",
            'rejection_reason_breakdown' => [
                ['reason' => 'Informasi dikecualikan', 'count' => 0],
            ],
            'is_published' => true,
            'published_at' => now(),
        ]);
        InformationServiceSummary::query()->create([
            'year' => 2027,
            'total_received' => 99,
            'fulfilled_full' => 99,
            'fulfilled_partial' => 0,
            'rejected' => 0,
            'is_published' => false,
        ]);
        InformationServiceSummary::query()->create([
            'year' => 2025,
            'total_received' => 4,
            'is_published' => true,
            'published_at' => now(),
        ]);

        $page = $this->get('/halaman/'.ServiceSummaryReport::SLUG.'?tahun=2026');
        $page->assertOk();
        $page->assertSee('Data belum tersedia', false);
        $page->assertSee('0 permohonan', false);
        $page->assertSee('Sebagian besar permohonan diselesaikan dalam waktu tiga hari.', false);
        $page->assertSee('Informasi dikecualikan', false);
        $page->assertSee('Dokumen tidak dikuasai', false);
        $page->assertSee('Status Pemenuhan Permohonan Informasi', false);
        $page->assertSee('Jumlah Permohonan Informasi Publik', false);
        $page->assertSee('Waktu Pemenuhan Permohonan Informasi', false);
        $page->assertSee('Alasan Penolakan Permohonan Informasi', false);
        $page->assertSee('Diagram status pemenuhan permohonan informasi tahun 2026', false);
        $page->assertDontSee('>99<', false);

        $this->get('/halaman/'.ServiceSummaryReport::SLUG.'?tahun=2099')
            ->assertOk()
            ->assertSee(ServiceSummaryReport::UNAVAILABLE, false)
            ->assertDontSee('Sebagian besar permohonan diselesaikan', false);

        $this->get('/halaman/'.ServiceSummaryReport::SLUG.'?tahun=bukan-tahun')
            ->assertOk()
            ->assertSee(ServiceSummaryReport::UNAVAILABLE, false);

        $this->get('/halaman/'.ServiceSummaryReport::SLUG)
            ->assertOk()
            ->assertSee('2026', false)
            ->assertDontSee('>99<', false);
    }

    #[Test]
    public function navbar_places_the_item_after_tata_cara_pengaduan(): void
    {
        $this->seed(MenuSeeder::class);

        $html = $this->get('/halaman/'.ServiceSummaryReport::SLUG)->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang[\s\S]+?Ringkasan Laporan Layanan Informasi/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi seluler"[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang[\s\S]+?Ringkasan Laporan Layanan Informasi/u',
            $html
        ));
        $this->assertSame(1, preg_match(
            '/href="[^"]*\/halaman\/'.ServiceSummaryReport::SLUG.'"[^>]*aria-current="page"/',
            $html
        ));
    }

    #[Test]
    public function database_navigation_inserts_the_item_once_after_tata_cara(): void
    {
        $this->seed(MenuSeeder::class);
        MenuItem::query()->where('label', 'Ringkasan Laporan Layanan Informasi')->delete();

        SiteSetting::query()->updateOrCreate(
            ['key' => 'navigation.public_source'],
            ['group' => 'navigation', 'value' => 'database', 'type' => 'text', 'label' => 'Sumber navigasi publik'],
        );
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $this->seed(EnsureRingkasanLaporanLayananInformasiMenuItemSeeder::class);
        $this->seed(EnsureRingkasanLaporanLayananInformasiMenuItemSeeder::class);
        PublicNavigation::forget();

        $this->assertSame(1, MenuItem::query()->where('label', 'Ringkasan Laporan Layanan Informasi')->count());

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertSame(1, preg_match(
            '/aria-label="Navigasi utama"[\s\S]+?Tata Cara Pengaduan Penyalahgunaan Wewenang[\s\S]+?Ringkasan Laporan Layanan Informasi/u',
            $html
        ));
    }

    #[Test]
    public function page_seeder_does_not_overwrite_admin_content_or_seed_statistics(): void
    {
        $page = Page::query()->where('slug', ServiceSummaryReport::SLUG)->firstOrFail();
        $page->update(['excerpt' => 'Ringkasan disunting admin PPID.']);
        InformationServiceSummary::query()->create([
            'year' => 2024,
            'total_received' => 3,
            'is_published' => true,
        ]);

        $this->seed(RingkasanLaporanLayananInformasiPageSeeder::class);

        $this->assertSame('Ringkasan disunting admin PPID.', $page->fresh()->excerpt);
        $this->assertSame(3, InformationServiceSummary::query()->where('year', 2024)->value('total_received'));
        $this->assertSame(1, InformationServiceSummary::query()->count());
    }

    #[Test]
    public function guests_are_redirected_and_admins_can_create_update_and_reject_invalid_totals(): void
    {
        $this->get(route('admin.service-summaries.index'))
            ->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->operator()->create())
            ->get(route('admin.service-summaries.index'))
            ->assertForbidden();

        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)
            ->get(route('admin.service-summaries.index'))
            ->assertOk()
            ->assertSee('Laporan Layanan Informasi', false);

        $this->actingAs($admin)
            ->post(route('admin.service-summaries.store'), [
                'year' => 2026,
                'total_received' => 10,
                'fulfilled_full' => 8,
                'fulfilled_partial' => 3,
                'rejected' => 2,
                'status' => 'published',
            ])
            ->assertSessionHasErrors('total_received');

        $this->assertDatabaseMissing('information_service_summaries', ['year' => 2026]);

        $this->actingAs($admin)
            ->post(route('admin.service-summaries.store'), $this->validPayload())
            ->assertRedirect(route('admin.service-summaries.index'));

        $summary = InformationServiceSummary::query()->where('year', 2026)->firstOrFail();
        $this->assertSame(10, $summary->total_received);
        $this->assertTrue($summary->is_published);
        $this->assertSame(4, $summary->rejection_reason_breakdown[0]['count']);

        $this->actingAs($admin)
            ->put(route('admin.service-summaries.update', $summary), [
                'year' => 2026,
                'total_received' => 5,
                'fulfilled_full' => 4,
                'fulfilled_partial' => 2,
                'rejected' => 1,
                'status' => 'draft',
                'response_time_description' => 'Diperbarui admin.',
            ])
            ->assertSessionHasErrors('total_received');

        $this->assertSame(10, $summary->fresh()->total_received);
        $this->assertTrue($summary->fresh()->is_published);

        $this->actingAs($admin)
            ->put(route('admin.service-summaries.update', $summary), [
                'year' => 2026,
                'total_received' => 12,
                'fulfilled_full' => 7,
                'fulfilled_partial' => 2,
                'rejected' => 1,
                'average_response_days' => '4.50',
                'response_time_description' => 'Diperbarui admin.',
                'rejection_reasons' => 'Informasi dikecualikan',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.service-summaries.index'));

        $summary->refresh();
        $this->assertSame(12, $summary->total_received);
        $this->assertSame('Diperbarui admin.', $summary->response_time_description);
    }

    #[Test]
    public function pdf_download_returns_a_pdf_document(): void
    {
        InformationServiceSummary::query()->create([
            'year' => 2026,
            'total_received' => 10,
            'fulfilled_full' => 6,
            'fulfilled_partial' => 2,
            'rejected' => 1,
            'average_response_days' => 3.4,
            'response_time_description' => 'Selesai dalam beberapa hari kerja.',
            'rejection_reasons' => 'Informasi dikecualikan',
            'is_published' => true,
            'published_at' => now(),
        ]);

        $response = $this->get(route('pages.service-summary.pdf', ['tahun' => 2026]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        $this->assertStringContainsString('Jumlah permohonan diterima', $response->getContent());
        $disposition = (string) $response->headers->get('content-disposition');
        $this->assertStringContainsString('ringkasan-layanan-informasi-PPID-Poltekkes-Banten-2026.pdf', $disposition);
    }

    #[Test]
    public function related_public_pages_still_return_ok(): void
    {
        $this->seed(CategorySeeder::class);

        $this->get('/halaman/kalender-akademik')->assertOk();
        $this->get('/halaman/kebijakan-mengikat-bagi-publik')->assertOk();
        $this->get('/halaman/tata-cara-pengaduan-penyalahgunaan-wewenang')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'year' => 2026,
            'total_received' => 10,
            'fulfilled_full' => 6,
            'fulfilled_partial' => 2,
            'rejected' => 1,
            'average_response_days' => '3.40',
            'response_time_description' => 'Sebagian besar permohonan diselesaikan dalam waktu tiga hari.',
            'rejection_reasons' => 'Informasi dikecualikan',
            'rejection_reason_breakdown' => [
                ['reason' => 'Informasi dikecualikan', 'count' => 4],
            ],
            'notes' => '',
            'status' => 'published',
        ];
    }
}
