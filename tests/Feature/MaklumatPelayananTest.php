<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaklumatPelayananTest extends TestCase
{
    use RefreshDatabase;

    public function test_maklumat_page_is_public_and_ok(): void
    {
        $response = $this->get('/profil/maklumat-pelayanan');

        $response->assertOk();
        $response->assertSee('Maklumat Pelayanan', false);
        $response->assertSee('images/ppid/maklumat-pelayanan-poltekkes-banten.webp', false);
        $response->assertSee('Beranda', false);
        $response->assertDontSee('Poltekkes Semarang', false);
    }

    public function test_maklumat_download_returns_file(): void
    {
        $response = $this->get('/profil/maklumat-pelayanan/unduh');

        $response->assertOk();
        $response->assertDownload('maklumat-pelayanan-poltekkes-kemenkes-banten.webp');
    }

    public function test_homepage_includes_maklumat_section(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Lihat Maklumat Pelayanan', false)
            ->assertSee(route('profil.maklumat-pelayanan', [], false), false);
    }
}
