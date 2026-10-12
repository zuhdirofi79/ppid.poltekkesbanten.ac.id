<?php

namespace Tests\Feature;

use Database\Seeders\CategorySeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MaklumatPelayananTest extends TestCase
{
    use RefreshDatabase;

    private const IMAGE_URL_PATH = '/docs/MAKLUMAT%20PELAYANAN.png';

    private const IMAGE_FILENAME = 'MAKLUMAT PELAYANAN.png';

    private const DESCRIPTION = 'Maklumat Pelayanan merupakan pernyataan kesanggupan Poltekkes Kemenkes Banten dalam menyelenggarakan pelayanan sesuai standar yang telah ditetapkan, melakukan perbaikan secara berkelanjutan, serta menerima konsekuensi sesuai ketentuan apabila pelayanan tidak dilaksanakan sebagaimana mestinya.';

    public function test_maklumat_page_is_public_and_ok(): void
    {
        $response = $this->get('/profil/maklumat-pelayanan');

        $response->assertOk();
        $response->assertSee('Maklumat Pelayanan', false);
        $response->assertSee(self::DESCRIPTION, false);
        $response->assertSee(self::IMAGE_URL_PATH, false);
        $response->assertSee('object-contain', false);
        $response->assertSee('h-auto w-full max-w-full', false);
        $response->assertSee('Lihat Ukuran Penuh', false);
        $response->assertSee('Unduh Maklumat Pelayanan', false);
        $response->assertSee('Beranda', false);
        $response->assertDontSee('Poltekkes Semarang', false);
        $response->assertDontSee('maklumat-pelayanan-poltekkes-banten.webp', false);
        $response->assertDontSee('%2520', false);

        $html = $response->getContent();
        $this->assertSame(2, preg_match_all(
            '/<img\b[^>]*src="[^"]*'.preg_quote(self::IMAGE_URL_PATH, '/').'"[^>]*class="[^"]*object-contain/',
            $html
        ));
        $this->assertDoesNotMatchRegularExpression('/<img\b[^>]*object-cover/', $html);
    }

    public function test_maklumat_download_returns_file(): void
    {
        $directory = public_path('docs');
        $path = $directory.DIRECTORY_SEPARATOR.self::IMAGE_FILENAME;
        $created = false;
        $createdDirectory = ! is_dir($directory);

        if (! is_file($path)) {
            File::ensureDirectoryExists($directory);
            File::put($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
            $created = true;
        }

        try {
            $response = $this->get('/profil/maklumat-pelayanan/unduh');

            $response->assertOk();
            $response->assertDownload('maklumat-pelayanan-poltekkes-kemenkes-banten.png');
            $this->assertStringContainsString('image/png', (string) $response->headers->get('content-type'));
        } finally {
            if ($created && is_file($path)) {
                File::delete($path);
            }
            if ($createdDirectory && is_dir($directory) && File::files($directory) === [] && File::directories($directory) === []) {
                File::deleteDirectory($directory);
            }
        }
    }

    public function test_homepage_includes_maklumat_section(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Lihat Maklumat Pelayanan', false)
            ->assertSee(route('profil.maklumat-pelayanan', [], false), false);
    }

    public function test_related_public_pages_stay_available(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);

        $this->get('/')->assertOk();
        $this->get('/profil/maklumat-pelayanan')->assertOk();
        $this->get('/halaman/struktur-organisasi')->assertOk();
        $this->get('/informasi-publik/berkala')->assertOk();
        $this->get('/informasi-publik/setiap-saat')->assertOk();
    }
}
