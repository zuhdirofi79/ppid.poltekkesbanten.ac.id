<?php

namespace Tests\Feature;

use App\Models\ServiceLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PengajuanKeberatanPageTest extends TestCase
{
    use RefreshDatabase;

    private const FORM_URL = 'https://docs.google.com/forms/d/e/1FAIpQLScE8lH-ihPiy_FbzWJ6zQIk8eiFgd9bUeckJerQPsNHNIMbew/viewform';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function objection_page_renders_landing_content(): void
    {
        $response = $this->get('/layanan/pengajuan-keberatan');

        $response->assertOk();
        $response->assertSee('PPID-PENGAJUAN-KEBERATAN-DEPLOY-20260907-A', false);
        $response->assertSee('ppid-objection-page', false);
        $response->assertSee('Pengajuan Keberatan', false);
        $response->assertSee('Ajukan keberatan atas permohonan informasi yang ditolak atau tidak ditanggapi.', false);
        $response->assertSee('Formulir Online', false);
        $response->assertSee('Ajukan Keberatan Layanan Informasi Publik', false);
        $response->assertSee('Isi Form Pengajuan Keberatan', false);
        $response->assertSee(self::FORM_URL, false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Formulir Pengajuan Keberatan PPID Poltekkes Kemenkes Banten', false);
        $response->assertSee('Fungsi Utama', false);
        $response->assertSee('Mengajukan protes resmi jika permohonan informasi ditolak.', false);
        $response->assertSee('Melaporkan informasi yang tidak ditanggapi atau tidak dipenuhi sesuai ketentuan waktu.', false);
        $response->assertSee('Menyampaikan ketidakpuasan terhadap tanggapan atau keputusan Pejabat Pengelola Informasi dan Dokumentasi (PPID).', false);
        $response->assertSee('Alasan Pengajuan Keberatan', false);
        $response->assertSee('Permohonan informasi ditolak tanpa alasan yang jelas.', false);
        $response->assertSee('Informasi yang diminta tidak disediakan atau tidak diberikan.', false);
        $response->assertSee('Tanggapan melewati batas waktu yang ditentukan berdasarkan ketentuan yang berlaku.', false);
        $response->assertSee('Biaya layanan informasi yang dikenakan tidak wajar.', false);
        $response->assertSee('Ingin Mengajukan Keberatan?', false);
        $response->assertSee('Hubungi PPID', false);
        $response->assertSee(route('contact', absolute: false), false);

        $response->assertDontSee('Kembali ke beranda', false);
        $response->assertDontSee('Formulir belum terhubung', false);
        $response->assertDontSee('config/ppid.php', false);
        $response->assertDontSee('PPID_OBJECTION_FORM_URL', false);
        $response->assertDontSee('Isi Form Permohonan Informasi Publik', false);
        $response->assertDontSee('PPID-PERMOHONAN-FORM-CTA-DEPLOY-20260907-A', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(3, preg_match_all('/class="ppid-objection-function-item"/', $html));
        $this->assertSame(4, preg_match_all('/class="ppid-objection-reason-item"/', $html));
        $this->assertLessThan(
            strpos($html, 'Fungsi Utama'),
            strpos($html, 'Isi Form Pengajuan Keberatan'),
            'CTA must appear before Fungsi Utama'
        );
    }

    #[Test]
    public function objection_page_does_not_redirect_even_if_service_has_external_url(): void
    {
        ServiceLink::query()->where('slug', 'pengajuan-keberatan')->update([
            'external_url' => 'https://example.com/should-not-redirect',
            'is_external' => true,
        ]);

        $response = $this->get('/layanan/pengajuan-keberatan');

        $response->assertOk();
        $response->assertSee('Isi Form Pengajuan Keberatan', false);
        $response->assertDontSee('https://example.com/should-not-redirect', false);
        $response->assertSee(self::FORM_URL, false);
    }

    #[Test]
    public function unsafe_form_url_is_not_rendered(): void
    {
        config(['ppid.objection_request.external_url' => 'javascript:alert(1)']);

        $response = $this->get('/layanan/pengajuan-keberatan');

        $response->assertOk();
        $response->assertSee('Pengajuan Keberatan', false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertDontSee('Isi Form Pengajuan Keberatan', false);
    }

    #[Test]
    public function other_service_pages_are_unaffected(): void
    {
        $response = $this->get('/layanan/keluhan-pelayanan');

        $response->assertOk();
        $response->assertSee('Keluhan Pelayanan', false);
        $response->assertSee('Kembali ke beranda', false);
        $response->assertSee('Hubungi PPID', false);
        $response->assertDontSee('PPID-PENGAJUAN-KEBERATAN-DEPLOY-20260907-A', false);
        $response->assertDontSee('ppid-objection-page', false);
        $response->assertDontSee('Isi Form Pengajuan Keberatan', false);
        $response->assertDontSee('Alasan Pengajuan Keberatan', false);

        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }
}
