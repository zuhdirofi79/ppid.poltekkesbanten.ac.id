<?php

namespace Tests\Feature;

use App\Models\ServiceLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PermohonanInformasiPageTest extends TestCase
{
    use RefreshDatabase;

    private const FORM_URL = 'https://docs.google.com/forms/d/e/1FAIpQLSffivoxDcFT-iDrc8eb1GKvkqhx-LenWuaQkE826D5uSbOG3A/viewform';

    private const EMAIL = 'humas@poltekkesbanten.ac.id';

    private const RECEIPT_URL = 'https://ppid.poltekkesbanten.ac.id/docs/TANDA%20TERIMA%20PERMOHONAN%20INFORMASI.docx';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function information_request_page_renders_landing_content(): void
    {
        $response = $this->get('/layanan/permohonan-informasi');

        $response->assertOk();
        $response->assertSee('PPID-PERMOHONAN-INFORMASI-DEPLOY-20260907-A', false);
        $response->assertSee('PPID-PERMOHONAN-FORM-CTA-DEPLOY-20260907-A', false);
        $response->assertSee('ppid-request-page', false);
        $response->assertSee('ppid-request-form-cta', false);
        $response->assertSee('Permohonan Informasi Publik', false);
        $response->assertSee('Ajukan permohonan informasi publik secara resmi melalui PPID.', false);
        $response->assertSee('Ajukan Permohonan Informasi Publik', false);
        $response->assertSee('Formulir Online', false);
        $response->assertSee('Isi Form Permohonan Informasi Publik', false);
        $response->assertSee('Silakan isi formulir resmi permohonan informasi publik melalui tautan berikut.', false);
        $response->assertSee(self::FORM_URL, false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Waktu Pelayanan Informasi', false);
        $response->assertSee('Senin – Kamis', false);
        $response->assertSee('Jumat', false);
        $response->assertSee('09.00 – 15.00 WIB', false);
        $response->assertSee('Istirahat: 12.00 – 13.00 WIB', false);
        $response->assertSee('Istirahat: 11.00 – 13.00 WIB', false);
        $response->assertSee('id="tanda-terima-permohonan-informasi"', false);
        $response->assertSee('<h2 id="tanda-terima-heading" class="ppid-request-section-title">Tanda Terima Permohonan Informasi</h2>', false);
        $response->assertSee('PPID-TANDA-TERIMA-PERMOHONAN-DEPLOY-20261006-A', false);
        $response->assertSee('Tanda Terima Permohonan Informasi', false);
        $response->assertSee('Tanda Terima Permohonan Informasi Publik', false);
        $response->assertSee('Formulir tanda terima digunakan sebagai bukti penerimaan permohonan informasi publik oleh PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Dokumen dapat dibuka atau diunduh dalam format Microsoft Word (DOCX).', false);
        $response->assertSee('TERSEDIA', false);
        $response->assertSee('DOCX / Dokumen', false);
        $response->assertSee('Buka Dokumen', false);
        $response->assertSee('Unduh DOCX', false);
        $response->assertSee(self::RECEIPT_URL, false);
        $response->assertSee('download="TANDA TERIMA PERMOHONAN INFORMASI.docx"', false);
        $response->assertSee('aria-label="Buka dokumen Tanda Terima Permohonan Informasi Publik"', false);
        $response->assertSee('aria-label="Unduh dokumen Tanda Terima Permohonan Informasi Publik format DOCX"', false);
        $response->assertSee('Pelayanan Informasi', false);
        $response->assertSee('Undang-Undang Nomor 14 Tahun 2008', false);
        $response->assertSee(self::EMAIL, false);
        $response->assertSee('mailto:'.self::EMAIL, false);
        $response->assertSee('Persyaratan Permohonan Informasi Publik', false);
        $response->assertSee('Ajukan Permohonan Sekarang', false);
        $response->assertSee('Hubungi PPID', false);
        $response->assertSee(route('contact', absolute: false), false);

        $response->assertDontSee('Formulir belum terhubung', false);
        $response->assertDontSee('config/ppid.php', false);
        $response->assertDontSee('services.*.external_url', false);
        $response->assertDontSee('ppid.yankes@gmail.com', false);
        $response->assertDontSee('PPID_INFORMATION_REQUEST_FORM_URL', false);

        $html = $response->getContent();
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
        $this->assertSame(6, preg_match_all('/<li\b[^>]*class="ppid-requirement-item"/', $html));
        $this->assertSame(1, preg_match_all('/<ol\b[^>]*class="ppid-requirement-list"/', $html));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'target="_blank"'));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'rel="noopener noreferrer"'));
        $this->assertSame(2, substr_count($html, self::RECEIPT_URL));
        $this->assertStringNotContainsString('http://ppid.poltekkesbanten.ac.id/docs/', $html);
        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('application/pdf', $html);

        $formHeading = strpos($html, 'Ajukan Permohonan Informasi Publik');
        $receiptHeading = strpos($html, 'Tanda Terima Permohonan Informasi');
        $hoursHeading = strpos($html, 'Waktu Pelayanan Informasi');
        $this->assertNotFalse($formHeading);
        $this->assertNotFalse($receiptHeading);
        $this->assertNotFalse($hoursHeading);
        $this->assertLessThan(
            $receiptHeading,
            $formHeading,
            'Ajukan Permohonan Informasi Publik must appear before Tanda Terima Permohonan Informasi'
        );
        $this->assertLessThan(
            $hoursHeading,
            $receiptHeading,
            'Tanda Terima Permohonan Informasi must appear before Waktu Pelayanan Informasi'
        );
        $this->assertLessThan(
            $hoursHeading,
            strpos($html, 'Isi Form Permohonan Informasi Publik'),
            'CTA must appear before Waktu Pelayanan Informasi'
        );
    }

    #[Test]
    public function information_request_page_does_not_redirect_even_if_service_has_external_url(): void
    {
        ServiceLink::query()->where('slug', 'permohonan-informasi')->update([
            'external_url' => 'https://example.com/should-not-redirect',
            'is_external' => true,
        ]);

        $response = $this->get('/layanan/permohonan-informasi');

        $response->assertOk();
        $response->assertSee('Isi Form Permohonan Informasi Publik', false);
        $response->assertDontSee('https://example.com/should-not-redirect', false);
        $response->assertSee(self::FORM_URL, false);
    }

    #[Test]
    public function unsafe_form_url_is_not_rendered(): void
    {
        config(['ppid.information_request.external_url' => 'javascript:alert(1)']);

        $response = $this->get('/layanan/permohonan-informasi');

        $response->assertOk();
        $response->assertSee('Permohonan Informasi Publik', false);
        $response->assertSee('Tanda Terima Permohonan Informasi', false);
        $response->assertSee(self::RECEIPT_URL, false);
        $response->assertDontSee('javascript:alert(1)', false);
        $response->assertDontSee('Isi Form Permohonan Informasi Publik', false);
    }

    #[Test]
    public function other_service_pages_are_unaffected(): void
    {
        $response = $this->get('/layanan/keluhan-pelayanan');

        $response->assertOk();
        $response->assertSee('Keluhan Pelayanan', false);
        $response->assertSee('Hubungi PPID', false);
        $response->assertSee('Kembali ke beranda', false);
        $response->assertDontSee('PPID-PERMOHONAN-INFORMASI-DEPLOY-20260907-A', false);
        $response->assertDontSee('PPID-PERMOHONAN-FORM-CTA-DEPLOY-20260907-A', false);
        $response->assertDontSee('ppid-request-page', false);
        $response->assertDontSee('Isi Form Permohonan Informasi Publik', false);
        $response->assertDontSee('Tanda Terima Permohonan Informasi', false);
        $response->assertDontSee('PPID-TANDA-TERIMA-PERMOHONAN-DEPLOY-20261006-A', false);
        $response->assertDontSee('Persyaratan Permohonan Informasi Publik', false);
        $response->assertDontSee('Formulir belum terhubung', false);
        $response->assertDontSee('config/ppid.php', false);

        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function homepage_still_points_to_landing_page_not_google_form(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('services.show', ['slug' => 'permohonan-informasi'], false), false);
        $response->assertDontSee(self::FORM_URL, false);
    }
}
