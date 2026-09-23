<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AlurPermohonanInformasiPageTest extends TestCase
{
    use RefreshDatabase;

    private const IMAGE_RELATIVE = 'images/ppid/alur-permohonan-informasi.png';

    private const FALLBACK_URL = 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-permohonan-informasi.png';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function page_shows_infographic_instead_of_placeholder(): void
    {
        $response = $this->get('/layanan/alur-permohonan-informasi');

        $response->assertOk();
        $response->assertSee('PPID-ALUR-PERMOHONAN-INFORMASI-DEPLOY-20260923-A', false);
        $response->assertSee('ppid-alur-page', false);
        $response->assertSee('Alur Permohonan Informasi', false);
        $response->assertSee('Pelajari tahapan permohonan informasi publik dari pengajuan hingga jawaban.', false);
        $response->assertSee('Infografis berikut menjelaskan alur permohonan informasi di PPID Poltekkes Kemenkes Banten secara singkat dan mudah dipahami.', false);
        $response->assertSee('Beranda', false);
        $response->assertSee('Layanan', false);
        $response->assertSee('alt="Infografis Alur Permohonan Informasi PPID Poltekkes Kemenkes Banten"', false);
        $response->assertSee('object-contain', false);
        $response->assertSee('overflow-x-auto', false);
        $response->assertSee('Unduh Gambar', false);
        $response->assertSee('download="alur-permohonan-informasi-ppid-poltekkes-kemenkes-banten.png"', false);
        $response->assertSee('Buka Gambar Penuh', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee('Permohonan Informasi', false);
        $response->assertSee(route('services.show', ['slug' => 'permohonan-informasi'], false), false);
        $response->assertSee('Hubungi PPID', false);
        $response->assertSee(route('contact', absolute: false), false);
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

        $response = $this->get('/layanan/alur-permohonan-informasi');

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
            $response = $this->get('/layanan/alur-permohonan-informasi');

            $response->assertOk();
            $response->assertSee(self::FALLBACK_URL, false);
            $response->assertSee('Infografis Alur Permohonan Informasi PPID Poltekkes Kemenkes Banten', false);
        } finally {
            if ($backup !== null && is_file($backup)) {
                File::move($backup, $path);
            }
        }
    }

    #[Test]
    public function other_service_pages_keep_placeholder(): void
    {
        $response = $this->get('/layanan/keluhan-pelayanan');

        $response->assertOk();
        $response->assertSee('Keluhan Pelayanan', false);
        $response->assertSee('Kembali ke beranda', false);
        $response->assertDontSee('ppid-alur-page', false);
        $response->assertDontSee('alur-permohonan-informasi.png', false);
        $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
    }
}
