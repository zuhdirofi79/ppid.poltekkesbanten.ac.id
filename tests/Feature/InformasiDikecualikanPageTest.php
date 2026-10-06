<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Models\User;
use App\Support\DaftarInformasiPublik;
use App\Support\InformasiDikecualikan;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiDikecualikanSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InformasiDikecualikanPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(InformasiDikecualikanSeeder::class);
    }

    #[Test]
    public function dikecualikan_page_shows_the_2026_docx_without_an_iframe(): void
    {
        $this->withTemporaryDocx(function () {
            $response = $this->get('/informasi-publik/dikecualikan');

            $response->assertOk();
            $response->assertSee(InformasiDikecualikan::TITLE, false);
            $response->assertSee(InformasiDikecualikan::EXCERPT, false);
            $response->assertSee(InformasiDikecualikan::HEADING_KICKER, false);
            $response->assertSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2026', false);
            $response->assertSee(InformasiDikecualikan::DOCUMENT_TITLE, false);
            $response->assertSee(InformasiDikecualikan::DOCUMENT_DESCRIPTION, false);
            $response->assertSee('DIK%20PPID%20POLKESTEN.docx', false);
            $response->assertSee('TERSEDIA', false);
            $response->assertSee('DOCX / Dokumen', false);
            $response->assertSee('Buka Dokumen', false);
            $response->assertSee('Unduh Dokumen', false);
            $response->assertSee('aria-label="Buka '.InformasiDikecualikan::DOCUMENT_TITLE.'"', false);
            $response->assertSee('aria-label="Unduh '.InformasiDikecualikan::DOCUMENT_TITLE.'"', false);
            $response->assertSee('download="'.InformasiDikecualikan::FILENAME.'"', false);
            $response->assertDontSee('<iframe', false);
            $response->assertDontSee(InformasiDikecualikan::MISSING_FILE, false);

            $html = $response->getContent();
            $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
            $this->assertSame(1, preg_match(
                '/aria-label="Breadcrumb"[\s\S]+?Beranda[\s\S]+?Informasi Publik[\s\S]+?Informasi yang Dikecualikan/u',
                $html
            ));
        });

        $this->withTemporaryDocx(function () {
            $this->get('/informasi-publik/dikecualikan?tahun=2026')
                ->assertOk()
                ->assertSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2026', false);
        });
    }

    #[Test]
    public function a_later_year_can_be_added_from_data_without_a_blade_change(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superAdmin()->create();
        $category = PublicInformationCategory::query()->where('slug', InformasiDikecualikan::SLUG)->firstOrFail();

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'Daftar Informasi yang Dikecualikan Tahun 2027',
                'description' => 'Dokumen DIK tahun 2027.',
                'public_information_category_id' => $category->id,
                'year' => 2027,
                'status' => 'published',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'file' => UploadedFile::fake()->create(
                    'dik-2027.docx',
                    20,
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ),
            ])
            ->assertRedirect();

        $this->assertSame(1, PublicDocument::query()->where('year', 2027)->count());

        $default = $this->get('/informasi-publik/dikecualikan')->assertOk();
        $default->assertSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2027', false);
        $default->assertSee('Daftar Informasi yang Dikecualikan Tahun 2027', false);
        $default->assertSee('Pilih Tahun', false);
        $default->assertDontSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2026', false);

        $this->get('/informasi-publik/dikecualikan?tahun=2026')
            ->assertOk()
            ->assertSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2026', false)
            ->assertDontSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2027', false);

        $html = $default->getContent();
        $year2027 = strpos($html, '>2027<');
        $year2026 = strpos($html, '>2026<');
        $this->assertNotFalse($year2027);
        $this->assertNotFalse($year2026);
        $this->assertLessThan($year2026, $year2027);
    }

    #[Test]
    public function an_invalid_year_query_does_not_fail_or_echo_the_input(): void
    {
        foreach (['abc', '999999', '<script>alert(1)</script>'] as $year) {
            $response = $this->get('/informasi-publik/dikecualikan?tahun='.urlencode($year));

            $response->assertOk();
            $response->assertSee(InformasiDikecualikan::MISSING_YEAR, false);
            $response->assertDontSee($year, false);
            $response->assertDontSee('alert(1)', false);
        }
    }

    #[Test]
    public function a_missing_docx_does_not_render_a_broken_link(): void
    {
        $this->withoutDocx(function () {
            $this->get('/informasi-publik/dikecualikan')
                ->assertOk()
                ->assertSee(InformasiDikecualikan::MISSING_FILE, false)
                ->assertSee('POLITEKNIK KESEHATAN KEMENKES BANTEN TAHUN 2026', false)
                ->assertDontSee('DIK%20PPID%20POLKESTEN.docx', false)
                ->assertDontSee('<iframe', false);
        });
    }

    #[Test]
    public function docx_upload_is_limited_to_the_excluded_information_category(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superAdmin()->create();
        $berkala = PublicInformationCategory::query()->where('slug', 'berkala')->firstOrFail();
        $file = UploadedFile::fake()->create(
            'dik.docx',
            20,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'Bukan DIK',
                'public_information_category_id' => $berkala->id,
                'year' => 2027,
                'status' => 'draft',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'file' => $file,
            ])
            ->assertSessionHasErrors('file');
    }

    #[Test]
    public function seeder_is_idempotent_and_keeps_an_admin_title(): void
    {
        $this->seed(InformasiDikecualikanSeeder::class);

        $document = PublicDocument::query()
            ->where('slug', PublicDocument::makeCatalogSlug(InformasiDikecualikan::DOCUMENT_TITLE))
            ->firstOrFail();
        $document->update(['title' => 'Judul admin 2026']);

        $this->seed(InformasiDikecualikanSeeder::class);

        $this->assertSame(1, PublicDocument::query()->where('year', InformasiDikecualikan::YEAR)->where('public_information_category_id', $document->public_information_category_id)->count());
        $document->refresh();
        $this->assertSame('Judul admin 2026', $document->title);
    }

    #[Test]
    public function related_public_pages_stay_available(): void
    {
        $this->seed(PageSeeder::class);

        $this->get('/informasi-publik/berkala')->assertOk();
        $this->get('/informasi-publik/setiap-saat')->assertOk();
        $this->get('/informasi-publik/serta-merta')->assertOk();
        $this->get('/halaman/'.DaftarInformasiPublik::SLUG)->assertOk();
        $this->assertNotNull(Page::query()->where('slug', DaftarInformasiPublik::SLUG)->first());
    }

    private function withTemporaryDocx(callable $callback): void
    {
        $path = public_path('docs/'.InformasiDikecualikan::FILENAME);
        $existed = is_file($path);

        if (! $existed) {
            if (! is_dir(dirname($path))) {
                mkdir(dirname($path), 0775, true);
            }
            file_put_contents($path, 'temporary-dik-docx');
        }

        try {
            $callback();
        } finally {
            if (! $existed && is_file($path)) {
                unlink($path);
            }
        }
    }

    private function withoutDocx(callable $callback): void
    {
        $path = public_path('docs/'.InformasiDikecualikan::FILENAME);
        $backup = $path.'.bak-test';
        $moved = false;

        if (is_file($path)) {
            rename($path, $backup);
            $moved = true;
        }

        try {
            $callback();
        } finally {
            if ($moved && is_file($backup)) {
                rename($backup, $path);
            }
        }
    }
}
