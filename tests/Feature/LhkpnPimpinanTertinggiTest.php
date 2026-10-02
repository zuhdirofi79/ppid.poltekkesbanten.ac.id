<?php

namespace Tests\Feature;

use App\Models\LhkpnDocument;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiBerkalaSeeder;
use Database\Seeders\LhkpnDocumentSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LhkpnPimpinanTertinggiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
        $this->seed(LhkpnDocumentSeeder::class);
    }

    #[Test]
    public function public_page_lists_eight_officials_in_required_order_without_personal_data(): void
    {
        $response = $this->get('/halaman/lhkpn-pimpinan-tertinggi');
        $response->assertOk();
        $html = $response->getContent();

        $response->assertSee('PPID-LHKPN-PIMPINAN-V1', false);
        $response->assertSee('LHKPN Pimpinan Tertinggi', false);
        $response->assertSee('Diumumkan Berkala', false);
        $response->assertSee('Laporan Harta Kekayaan Penyelenggara Negara (LHKPN) pimpinan dan pejabat terkait Poltekkes Kemenkes Banten berdasarkan dokumen resmi yang tersedia.', false);
        $response->assertSee('tanda terima pelaporan e-LHKPN', false);
        $response->assertDontSee('Konten halaman ini sedang disiapkan', false);
        $response->assertDontSee('telah diverifikasi KPK', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));

        $names = [
            'Dr. Dahrizal, S.Kp., MPH',
            'Kusniawati, S.Kep., Ners., M.Kep',
            'Sendy Cokro Wibowo, S.E.',
            'Asmawati, S.Kp., M.Kep',
            'Ahmad, SKM., M.Kes',
            'Marlin Brigita',
            'Ida Lindawati',
            'Indah Tryana',
        ];

        $previous = -1;
        foreach ($names as $name) {
            $position = strpos($html, $name);
            $this->assertNotFalse($position, $name);
            $this->assertGreaterThan($previous, $position, $name);
            $previous = $position;
        }

        $response->assertSee('Direktur Poltekkes Kemenkes Banten', false);
        $response->assertSee('Wakil Direktur I Poltekkes Kemenkes Banten', false);
        $response->assertSee('Wakil Direktur II Poltekkes Kemenkes Banten', false);
        $response->assertSee('Wakil Direktur III Poltekkes Kemenkes Banten', false);
        $response->assertSee('Ketua Satuan Pengawas Internal', false);
        $response->assertSee('Sekretaris Satuan Pengawas Internal', false);
        $response->assertSee('Kepala Pusat Pengembangan Pendidikan', false);
        $response->assertSee('Bendahara', false);
        $response->assertSee('Pimpinan Direktorat', false);
        $response->assertSee('Pejabat Terkait', false);

        foreach (['direktur', 'wakil-direktur-1', 'wakil-direktur-2', 'ketua-spi', 'sekretaris-spi', 'kepala-pusat-pengembangan-pendidikan', 'bendahara'] as $key) {
            $document = LhkpnDocument::query()->where('position_key', $key)->firstOrFail();
            $url = $document->documentUrl();
            $this->assertNotNull($url);
            $response->assertSee($url, false);
            $response->assertSee('Buka Dokumen', false);
            $response->assertSee('Preview', false);
            $response->assertSee('Download PDF', false);
        }

        $this->assertStringContainsString('DAHRIZAL.pdf', $html);
        $this->assertStringContainsString('SENDY%20COKRO%20WIBOWO.pdf', $html);
        $this->assertStringContainsString('AHMAD.pdf', $html);
        $this->assertStringContainsString('MARLIN%20BRIGITA.pdf', $html);
        $this->assertStringContainsString('IDA%20LINDAWATI.pdf', $html);
        $this->assertStringContainsString('INDAH%20TRYANA.pdf', $html);
        $this->assertStringContainsString('Kusniawati%2C%20S.Kep.%2C%20Ners.%2C%20M.Kep%20_Tanda_Terima_LHKPN_', $html);

        $asmawatiAt = strpos($html, 'id="lhkpn-wakil-direktur-3"');
        $ahmadAt = strpos($html, 'id="lhkpn-ketua-spi"');
        $chunk = substr($html, $asmawatiAt, $ahmadAt - $asmawatiAt);
        $this->assertStringContainsString('Dokumen belum tersedia', $chunk);
        $this->assertStringContainsString('Dokumen LHKPN belum tersedia pada portal PPID.', $chunk);
        $this->assertStringNotContainsString('href=', $chunk);
        $this->assertStringNotContainsString('Buka Dokumen', $chunk);

        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
        foreach ([
            '3603176611740005',
            '3671101005860004',
            '3602180510670004',
            '7371105503890006',
            '3672016811740001',
            '3273016211840001',
            'dahrizal26@gmail.com',
        ] as $sensitive) {
            $this->assertStringNotContainsString($sensitive, $text);
        }
    }

    #[Test]
    public function admin_can_open_management_and_guest_cannot_upload(): void
    {
        $admin = User::factory()->adminPpid()->create();
        $asmawati = LhkpnDocument::query()->where('position_key', 'wakil-direktur-3')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.lhkpn.index'))
            ->assertOk()
            ->assertSee('Asmawati, S.Kp., M.Kep', false)
            ->assertSee('WAJIB DILENGKAPI', false)
            ->assertSee('Upload Dokumen', false);

        auth()->logout();

        $this->post(route('admin.lhkpn.upload', $asmawati), [
            'file' => $this->pdfUpload(),
        ])->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function admin_pdf_upload_publishes_asmawati_and_rejects_non_pdf(): void
    {
        Storage::fake('public');
        $admin = User::factory()->adminPpid()->create();
        $asmawati = LhkpnDocument::query()->where('position_key', 'wakil-direktur-3')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.lhkpn.upload', $asmawati), [
                'file' => UploadedFile::fake()->create('catatan.txt', 12, 'text/plain'),
            ])
            ->assertSessionHasErrors('file');

        $asmawati->refresh();
        $this->assertFalse($asmawati->isAvailable());

        $this->actingAs($admin)
            ->post(route('admin.lhkpn.upload', $asmawati), [
                'file' => $this->pdfUpload(),
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $asmawati->refresh();
        $this->assertTrue($asmawati->isAvailable());
        $this->assertNotNull($asmawati->documentUrl());
        $this->assertStringEndsWith('.pdf', (string) $asmawati->file_path);

        $html = $this->get('/halaman/lhkpn-pimpinan-tertinggi')->assertOk()->getContent();
        $asmawatiAt = strpos($html, 'id="lhkpn-wakil-direktur-3"');
        $ahmadAt = strpos($html, 'id="lhkpn-ketua-spi"');
        $chunk = substr($html, $asmawatiAt, $ahmadAt - $asmawatiAt);
        $this->assertStringContainsString('Tersedia', $chunk);
        $this->assertStringContainsString('Buka Dokumen', $chunk);
        $this->assertStringNotContainsString('Dokumen LHKPN belum tersedia pada portal PPID.', $chunk);
        $this->assertStringContainsString($asmawati->documentUrl(), $chunk);
    }

    #[Test]
    public function reseeding_does_not_duplicate_rows_or_replace_admin_edits(): void
    {
        Storage::fake('public');
        $admin = User::factory()->adminPpid()->create();
        $asmawati = LhkpnDocument::query()->where('position_key', 'wakil-direktur-3')->firstOrFail();
        $dahrizal = LhkpnDocument::query()->where('position_key', 'direktur')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.lhkpn.upload', $asmawati), [
            'file' => $this->pdfUpload('asmawati-resmi.pdf'),
        ])->assertRedirect();

        $asmawati->refresh();
        $uploadedPath = $asmawati->file_path;
        $asmawati->update([
            'person_name' => 'Asmawati, S.Kp., M.Kep',
            'notes' => 'Catatan admin tidak boleh hilang.',
            'sort_order' => 41,
        ]);
        $dahrizal->update([
            'person_name' => 'Dr. Dahrizal, S.Kp., MPH',
            'position' => 'Direktur — disunting admin',
        ]);

        $this->seed(LhkpnDocumentSeeder::class);
        $this->seed(LhkpnDocumentSeeder::class);

        $this->assertSame(8, LhkpnDocument::query()->count());
        $asmawati->refresh();
        $dahrizal->refresh();
        $this->assertSame($uploadedPath, $asmawati->file_path);
        $this->assertSame('Catatan admin tidak boleh hilang.', $asmawati->notes);
        $this->assertSame(41, $asmawati->sort_order);
        $this->assertTrue($asmawati->isAvailable());
        $this->assertNull($asmawati->public_asset_path);
        $this->assertSame('Direktur — disunting admin', $dahrizal->position);
        $this->assertSame('docs/LHKPN_verif_KPK/DAHRIZAL.pdf', $dahrizal->public_asset_path);
    }

    #[Test]
    public function related_public_pages_still_return_ok(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);

        $this->get('/halaman/profil-pimpinan-satuan-kerja')->assertOk();
        $this->get('/halaman/kalender-akademik')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();
    }

    #[Test]
    public function search_finds_lhkpn_people_without_exposing_a_new_search_system(): void
    {
        $this->get('/pencarian?q=Asmawati')
            ->assertOk()
            ->assertSee('Asmawati, S.Kp., M.Kep', false)
            ->assertSee('/halaman/lhkpn-pimpinan-tertinggi#lhkpn-wakil-direktur-3', false);

        $this->get('/pencarian?q=LHKPN')
            ->assertOk()
            ->assertSee('LHKPN Pimpinan Tertinggi', false);
    }

    private function pdfUpload(string $name = 'lhkpn.pdf'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF");
    }
};
