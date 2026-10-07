<?php

namespace Tests\Feature;

use App\Models\SaqItem;
use App\Support\DokumenPengadaanBarangJasa;
use App\Support\InventarisBarangMilikNegara;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DokumenPengadaanBarangJasaSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\SaqItemSeeder;
use Database\Seeders\StatistikKepegawaianSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventarisBarangMilikNegaraSectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(DokumenPengadaanBarangJasaSeeder::class);
        $this->seed(StatistikKepegawaianSeeder::class);
    }

    #[Test]
    public function setiap_saat_shows_the_bmn_landing_between_procurement_and_staffing(): void
    {
        $response = $this->get('/informasi-publik/setiap-saat');

        $response->assertOk();
        $response->assertSee(InventarisBarangMilikNegara::HEADING, false);
        $response->assertSee('PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee('Portal BMN Poltekkes Kemenkes Banten', false);
        $response->assertSee(InventarisBarangMilikNegara::CTA_LABEL, false);
        $response->assertSee(InventarisBarangMilikNegara::PORTAL_URL, false);
        $response->assertSee('id="'.InventarisBarangMilikNegara::ANCHOR.'"', false);
        $response->assertSee(InventarisBarangMilikNegara::CTA_ARIA, false);
        $response->assertSee('Statistik Kepegawaian', false);
        $response->assertSee(DokumenPengadaanBarangJasa::SECTION, false);
        $response->assertSee('aria-label="Inventaris Barang Milik Negara Poltekkes Kemenkes Banten — buka Sistem BMN"', false);
        $response->assertDontSee('http-equiv="refresh"', false);
        $response->assertDontSee('window.location', false);
        $response->assertDontSee('location.href', false);
        $this->assertFalse($response->isRedirection());

        $html = $response->getContent();
        $procurement = strpos($html, DokumenPengadaanBarangJasa::SECTION);
        $bmn = strpos($html, InventarisBarangMilikNegara::HEADING);
        $staffing = strpos($html, 'Statistik Kepegawaian');

        $this->assertNotFalse($procurement);
        $this->assertNotFalse($bmn);
        $this->assertNotFalse($staffing);
        $this->assertLessThan($bmn, $procurement);
        $this->assertLessThan($staffing, $bmn);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertMatchesRegularExpression(
            '/<a\b[^>]*class="inventaris-bmn-cta"[^>]*href="'.preg_quote(InventarisBarangMilikNegara::PORTAL_URL, '/').'"[^>]*target="_blank"[^>]*rel="noopener noreferrer"/',
            $html
        );
        $this->assertStringContainsString('scroll-margin-top: 7.5rem', $html);

        $sectionStart = strpos($html, 'id="'.InventarisBarangMilikNegara::ANCHOR.'"');
        $sectionEnd = strpos($html, 'id="statistik-kepegawaian"');
        $section = substr($html, $sectionStart, $sectionEnd - $sectionStart);
        $this->assertDoesNotMatchRegularExpression('/jumlah aset|jumlah gedung|jumlah kendaraan|nilai BMN|Rp\s*\d/i', $section);
    }

    #[Test]
    public function query_parameters_cannot_replace_the_hardcoded_portal_url(): void
    {
        $this->get('/informasi-publik/setiap-saat?redirect=https://evil.example&url=https://evil.example&target=https://evil.example')
            ->assertOk()
            ->assertSee(InventarisBarangMilikNegara::PORTAL_URL, false)
            ->assertDontSee('evil.example', false);
    }

    #[Test]
    public function saq_evidence_route_points_at_the_ppid_anchor_without_changing_other_items(): void
    {
        $this->seed(SaqItemSeeder::class);

        $item = SaqItem::query()->where('code', 'PENYEDIAAN-13')->firstOrFail();
        $other = SaqItem::query()->where('code', 'PENYEDIAAN-12A')->firstOrFail();

        $this->assertSame(InventarisBarangMilikNegara::EVIDENCE_PATH, $item->recommended_public_route);
        $this->assertSame('/halaman/statistik-kepegawaian', $other->recommended_public_route);

        $item->update([
            'recommended_public_route' => '/halaman/data-perbendaharaan-inventaris-bmn',
            'answer' => 'yes',
            'status' => 'complete',
        ]);
        $other->update([
            'recommended_public_route' => '/halaman/custom-admin',
            'answer' => 'partial',
        ]);

        $this->seed(SaqItemSeeder::class);

        $item->refresh();
        $other->refresh();

        $this->assertSame(InventarisBarangMilikNegara::EVIDENCE_PATH, $item->recommended_public_route);
        $this->assertSame('yes', $item->answer);
        $this->assertSame('complete', $item->status);
        $this->assertSame('/halaman/custom-admin', $other->recommended_public_route);
        $this->assertSame('partial', $other->answer);
        $this->assertSame(
            1,
            SaqItem::query()->where('recommended_public_route', InventarisBarangMilikNegara::EVIDENCE_PATH)->count()
        );
    }

    #[Test]
    public function related_public_pages_stay_available(): void
    {
        $this->seed(PageSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/informasi-publik/setiap-saat')->assertOk();
        $this->get('/halaman/statistik-keuangan')->assertOk();
        $this->get('/halaman/daftar-informasi-publik')->assertOk();
        $this->get('/halaman/rapat-koordinasi-ppid')->assertOk();
    }
}
