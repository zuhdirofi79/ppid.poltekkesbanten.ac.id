<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlurPengajuanSengketaPageTest extends TestCase
{
    use RefreshDatabase;

    private const IMAGE_RELATIVE = 'images/ppid/alur-pengajuan-permohonan-penyelesaian-sengketa-ke-komisi-informasi.png';

    private const FALLBACK_URL = 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-pengajuan-permohonan-penyelesaian-sengketa-ke-komisi-informasi.png';

    private const KOMISI_URL = 'https://komisiinformasi.go.id/read/14/03/2023/Mekanisme-Pendaftaran-Sengketa-Informasi-Publik';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function page_shows_existing_infographic(): void
    {
        $response = $this->get('/layanan/alur-pengajuan-sengketa');

        $response->assertOk();
        $response->assertSee('PPID-ALUR-PENGAJUAN-SENGKETA-DEPLOY-20260923-A', false);
        $response->assertSee('ppid-alur-sengketa-page', false);
        $response->assertSee('<title>Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi | PPID Poltekkes Kemenkes Banten</title>', false);
        $response->assertSee('Pelajari tahapan penyelesaian sengketa informasi publik melalui Komisi Informasi.', false);
        $response->assertSee('Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi', false);
        $response->assertSee('Pemohon informasi publik dapat mengajukan penyelesaian sengketa informasi apabila keberatan atas layanan informasi belum memperoleh penyelesaian sesuai ketentuan yang berlaku.', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Layanan', false);
        $response->assertSee('alt="Alur dan Tata Cara Pengajuan Sengketa Informasi Publik PPID Poltekkes Kemenkes Banten"', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertSee('object-contain', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('Informasi Penting', false);
        $response->assertSee('Pengajuan sengketa dilakukan setelah pemohon menempuh proses keberatan kepada Atasan PPID terlebih dahulu.', false);
        $response->assertSee('Permohonan penyelesaian sengketa informasi dapat diajukan kepada Komisi Informasi sesuai ketentuan yang berlaku.', false);
        $response->assertSee('Pemohon wajib menyiapkan dokumen pendukung seperti identitas, bukti permohonan informasi, bukti keberatan, dan tanggapan PPID.', false);
        $response->assertSee('Mekanisme Pendaftaran Sengketa Informasi Publik', false);
        $response->assertSee(self::KOMISI_URL, false);
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

        $response = $this->get('/layanan/alur-pengajuan-sengketa');

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
            $response = $this->get('/layanan/alur-pengajuan-sengketa');

            $response->assertOk();
            $response->assertSee(self::FALLBACK_URL, false);
        } finally {
            if ($backup !== null && is_file($backup)) {
                File::move($backup, $path);
            }
        }
    }

    #[Test]
    public function layanan_menu_lists_sengketa_after_alur_keberatan(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match(
            '/Alur Pengajuan Keberatan[\s\S]+?Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi/u',
            $html
        ));
        $this->assertStringContainsString(
            route('services.show', ['slug' => 'alur-pengajuan-sengketa'], false),
            $html
        );
    }

    #[Test]
    public function existing_alur_pages_are_unchanged(): void
    {
        $permohonan = $this->get('/layanan/alur-permohonan-informasi');
        $permohonan->assertOk();
        $permohonan->assertSee('ppid-alur-page', false);
        $permohonan->assertDontSee('ppid-alur-sengketa-page', false);

        $keberatan = $this->get('/layanan/alur-pengajuan-keberatan');
        $keberatan->assertOk();
        $keberatan->assertSee('ppid-alur-keberatan-page', false);
        $keberatan->assertDontSee('ppid-alur-sengketa-page', false);
        $keberatan->assertDontSee('alur-pengajuan-permohonan-penyelesaian-sengketa-ke-komisi-informasi.png', false);
    }
}
