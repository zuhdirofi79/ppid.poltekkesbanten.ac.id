<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_homepage_is_successful(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PPID POLTEKKES KEMENKES BANTEN', false);
        $response->assertSee('Informasi Serta-Merta', false);
        $response->assertSee('Layanan Informasi Publik', false);
        $response->assertSee('Pilih layanan sesuai kebutuhan Anda.', false);
        $response->assertSee('ppid-home-service-card', false);
        $response->assertSee('Statistik PPID', false);
        $response->assertSee('CONTOH', false);
        $response->assertSee('ppid-maklumat-popup', false);
        $response->assertSee('Saya Mengerti', false);
        $response->assertDontSee('Buka layanan', false);
        $response->assertDontSee('Shortcut Formulir', false);
        $response->assertDontSee('Berita Terbaru', false);
        $response->assertDontSee('Pengumuman Terbaru', false);
        $response->assertDontSee('id="unit-heading"', false);
        $response->assertDontSee('Peta dan Kontak', false);
        $response->assertDontSee('Lihat Maklumat Pelayanan', false);

        $html = $response->getContent();
        $this->assertSame(6, substr_count($html, 'class="ppid-home-service-card"'));
        $hero = strpos($html, 'PPID POLTEKKES KEMENKES BANTEN');
        $categories = strpos($html, 'id="kategori-heading"');
        $services = strpos($html, 'id="layanan-heading"');
        $statistics = strpos($html, 'id="statistik-heading"');
        $footer = strpos($html, '<footer');
        $this->assertNotFalse($hero);
        $this->assertTrue($hero < $categories && $categories < $services && $services < $statistics && $statistics < $footer);

        foreach ([
            'permohonan-informasi',
            'pengajuan-keberatan',
            'pengaduan-penyalahgunaan-wewenang',
            'keluhan-pelayanan',
            'surat-keterangan',
            'alur-permohonan-informasi',
        ] as $slug) {
            $response->assertSee(route('services.show', ['slug' => $slug], false), false);
        }

        $response->assertDontSee('Poltekkes Semarang', false);
        $response->assertDontSee('Gizi', false);
        $response->assertDontSee('RMIK', false);
    }

    public function test_placeholder_routes_respond(): void
    {
        $this->get('/informasi-publik')->assertOk();
        $this->get('/dokumen-publik')->assertOk();
        $this->get('/kontak')->assertOk();
        $this->get('/faq')->assertOk();
        $this->get('/layanan/permohonan-informasi')->assertOk();
        $this->get('/informasi-publik/serta-merta')->assertOk()
            ->assertSee('Informasi Serta-Merta', false);
    }
}
