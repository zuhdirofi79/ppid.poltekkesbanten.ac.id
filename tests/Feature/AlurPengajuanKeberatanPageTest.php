<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlurPengajuanKeberatanPageTest extends TestCase
{
    use RefreshDatabase;

    private const IMAGE_RELATIVE = 'images/ppid/alur-pengajuan-keberatan.png';

    private const FALLBACK_URL = 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-pengajuan-keberatan.png';

    private const SOP_URL = 'https://poltekkesbanten.ac.id/wp-content/uploads/2025/07/SOP-Penanganan-Sengketa-Informasi-Publik.pdf';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function page_shows_existing_infographic_and_explanation(): void
    {
        $response = $this->get('/layanan/alur-pengajuan-keberatan');

        $response->assertOk();
        $response->assertSee('PPID-ALUR-PENGAJUAN-KEBERATAN-DEPLOY-20260923-A', false);
        $response->assertSee('ppid-alur-keberatan-page', false);
        $response->assertSee('<title>Alur Pengajuan Keberatan | PPID Poltekkes Kemenkes Banten</title>', false);
        $response->assertSee('Tahapan dan tata cara pengajuan keberatan atas layanan informasi publik PPID Poltekkes Kemenkes Banten.', false);
        $response->assertSee('Alur Pengajuan Keberatan', false);
        $response->assertSee('Pelajari alur dan tata cara pengajuan keberatan atas layanan informasi publik secara jelas, ringkas, dan mudah dipahami.', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Layanan', false);
        $response->assertSee('alt="Infografis Alur Pengajuan Keberatan PPID Poltekkes Kemenkes Banten"', false);
        $response->assertSee('object-contain', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('Kapan Waktu Pengajuan', false);
        $response->assertSee('paling lambat 10 (sepuluh) hari kerja', false);
        $response->assertSee('Syarat Dokumen', false);
        $response->assertSee('Mengisi Formulir Pendaftaran atau Pengajuan Keberatan secara lengkap.', false);
        $response->assertSee('Melampirkan salinan/fotokopi identitas diri (KTP) pemohon.', false);
        $response->assertSee('Menyertakan alasan pengajuan keberatan secara jelas.', false);
        $response->assertSee('Proses Penanganan', false);
        $response->assertSee('Standar Operasional Prosedur (SOP) penanganan sengketa informasi publik yang berlaku.', false);
        $response->assertSee('Ajukan Keberatan', false);
        $response->assertSee('Isi Form Pengajuan Keberatan', false);
        $response->assertSee(route('services.show', ['slug' => 'pengajuan-keberatan'], false), false);
        $response->assertSee('Lihat SOP Penanganan Sengketa', false);
        $response->assertSee(self::SOP_URL, false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertDontSee('Kembali ke beranda', false);
        $response->assertDontSee('hstgr.io', false);

        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }

    #[Test]
    public function local_asset_is_used_when_image_file_exists(): void
    {
        if (! is_file(public_path(self::IMAGE_RELATIVE))) {
            $this->markTestSkipped('File infografis lokal tidak ada di public path.');
        }

        $response = $this->get('/layanan/alur-pengajuan-keberatan');

        $response->assertOk();
        $response->assertSee('/'.self::IMAGE_RELATIVE, false);
        $response->assertDontSee(self::FALLBACK_URL, false);
    }

    #[Test]
    public function fallback_url_is_used_when_local_image_is_missing(): void
    {
        $path = public_path(self::IMAGE_RELATIVE);
        $existed = is_file($path);
        $backup = null;

        if ($existed) {
            $backup = $path.'.bak-test';
            File::move($path, $backup);
        }

        try {
            $response = $this->get('/layanan/alur-pengajuan-keberatan');

            $response->assertOk();
            $response->assertSee(self::FALLBACK_URL, false);
            $response->assertSee('Infografis Alur Pengajuan Keberatan PPID Poltekkes Kemenkes Banten', false);
        } finally {
            if ($backup !== null && is_file($backup)) {
                File::move($backup, $path);
            }
        }
    }

    #[Test]
    public function layanan_menu_lists_alur_keberatan_after_alur_permohonan(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/Alur Permohonan Informasi[\s\S]+?Alur Pengajuan Keberatan/u',
            $html
        ));
        $this->assertStringContainsString(
            route('services.show', ['slug' => 'alur-pengajuan-keberatan'], false),
            $html
        );
    }

    #[Test]
    public function layanan_parent_is_active_on_this_page(): void
    {
        $response = $this->get('/layanan/alur-pengajuan-keberatan');

        $response->assertOk();
        $html = $response->getContent();
        $this->assertSame(1, preg_match('/aria-current="page"\s*>\s*Layanan/u', $html));
        $this->assertSame(1, preg_match('/aria-current="page"\s*>\s*<span>Layanan<\/span>/u', $html));
        $response->assertSee(route('services.show', ['slug' => 'alur-pengajuan-keberatan'], false), false);
    }

    #[Test]
    public function other_service_pages_keep_their_layout(): void
    {
        $permohonan = $this->get('/layanan/alur-permohonan-informasi');
        $permohonan->assertOk();
        $permohonan->assertSee('ppid-alur-page', false);
        $permohonan->assertDontSee('ppid-alur-keberatan-page', false);

        $placeholder = $this->get('/layanan/keluhan-pelayanan');
        $placeholder->assertOk();
        $placeholder->assertSee('Kembali ke beranda', false);
        $placeholder->assertDontSee('alur-pengajuan-keberatan.png', false);
    }
}
