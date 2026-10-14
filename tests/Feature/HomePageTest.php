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
        $response->assertDontSee('Buka layanan', false);
        foreach (config('ppid.services', []) as $service) {
            $response->assertSee($service['title'], false);
            if (blank($service['external_url'] ?? null)) {
                $response->assertSee(route('services.show', ['slug' => $service['slug']], false), false);
            }
        }
        $response->assertSee('Shortcut Formulir', false);
        $response->assertSee('Berita Terbaru', false);
        $response->assertSee('Pengumuman Terbaru', false);
        $response->assertSee('Statistik PPID', false);
        $response->assertSee('PPID Pelaksana', false);
        $response->assertSee('Jurusan Teknologi Laboratorium Medis', false);
        $response->assertSee('Peta dan Kontak', false);
        $response->assertSee('CONTOH', false);
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
