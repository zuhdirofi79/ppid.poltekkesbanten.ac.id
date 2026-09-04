<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiBerkalaSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InformasiBerkalaCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalog(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);
    }

    #[Test]
    public function berkala_page_lists_available_and_missing_documents(): void
    {
        $this->seedCatalog();

        $response = $this->get('/informasi-publik/berkala');

        $response->assertOk();
        $response->assertSee('PPID-INFORMASI-BERKALA-DEPLOY-20260904-A', false);
        $response->assertSee('Akreditasi Poltekkes Banten', false);
        $response->assertSee('RAK Poltekkes Banten 2020–2024', false);
        $response->assertSee('RSB Poltekkes Banten 2022–2026', false);
        $response->assertSee('RSB Poltekkes Banten 2025–2029', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2022', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2023', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2024', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2023', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2024', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2025', false);
        $response->assertSee('Panduan Akademik 2023/2024', false);
        $response->assertSee('Tarif Layanan Akademik', false);
        $response->assertSee('Tarif Layanan Penunjang Akademik', false);
        $response->assertSee('Laporan PPID Tahun 2024', false);
        $response->assertSee('Dokumen belum tersedia', false);
        $response->assertDontSee('WAJIB UNGGAH', false);
        $response->assertDontSee('Admin PPID wajib', false);

        $html = $response->getContent();
        $this->assertStringContainsString('https://poltekkesbanten.ac.id/wp-content/uploads/2026/06/RAK-Poltekkes-Banten-2020-2024.pdf', $html);
        $this->assertStringContainsString('/halaman/akreditasi', $html);

        // Missing docs must not be wrapped as clickable href to empty destinations.
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function missing_document_has_no_lihat_dokumen_link(): void
    {
        $this->seedCatalog();

        $response = $this->get('/informasi-publik/berkala');
        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/LAKIN Poltekkes Kemenkes Banten 2023[\s\S]*?Dokumen belum tersedia/',
            $html,
        );

        $pos = strpos($html, 'LAKIN Poltekkes Kemenkes Banten 2023');
        $chunk = substr($html, $pos, 800);
        $this->assertStringNotContainsString('Lihat Dokumen', $chunk);
    }

    #[Test]
    public function akreditasi_page_is_published(): void
    {
        $this->seed(PageSeeder::class);

        $this->get('/halaman/akreditasi')
            ->assertOk()
            ->assertSee('PPID-AKREDITASI-DEPLOY-20260904-A', false)
            ->assertSee('Akreditasi Poltekkes Kemenkes Banten', false)
            ->assertSee('sedang dalam proses pemutakhiran', false);
    }

    #[Test]
    public function seeder_is_idempotent_and_preserves_admin_upload(): void
    {
        $this->seedCatalog();

        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->first();
        $this->assertNotNull($doc);

        $doc->update([
            'file_path' => 'ppid/documents/2026/09/admin-uploaded.pdf',
            'file_name' => 'admin-uploaded.pdf',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'is_external' => false,
        ]);

        $this->seed(InformasiBerkalaSeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);

        $this->assertSame(1, PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->count());
        $fresh = $doc->fresh();
        $this->assertSame('ppid/documents/2026/09/admin-uploaded.pdf', $fresh->file_path);
        $this->assertTrue($fresh->isAvailable());
    }

    #[Test]
    public function admin_can_upload_pdf_and_public_gets_link(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();

        $file = UploadedFile::fake()->create('lakin-2023.pdf', 200, 'application/pdf');

        $this->actingAs($admin)
            ->put(route('admin.documents.update', $doc), [
                'title' => $doc->title,
                'slug' => $doc->slug,
                'public_information_category_id' => $doc->public_information_category_id,
                'document_category_id' => $doc->document_category_id,
                'section_title' => $doc->section_title,
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'status' => 'published',
                'sort_order' => $doc->sort_order,
                'file' => $file,
            ])
            ->assertRedirect(route('admin.informasi-publik.berkala'));

        $doc->refresh();
        $this->assertTrue($doc->isAvailable());
        $this->assertNotNull($doc->file_path);
        Storage::disk('public')->assertExists($doc->file_path);

        $public = $this->get('/informasi-publik/berkala');
        $public->assertOk();
        $public->assertSee(route('documents.download', $doc->slug, false), false);
    }

    #[Test]
    public function invalid_mime_is_rejected(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();

        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/x-msdownload');

        $this->actingAs($admin)
            ->from(route('admin.documents.edit', $doc))
            ->put(route('admin.documents.update', $doc), [
                'title' => $doc->title,
                'slug' => $doc->slug,
                'public_information_category_id' => $doc->public_information_category_id,
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'status' => 'published',
                'file' => $file,
            ])
            ->assertSessionHasErrors('file');

        $this->assertFalse($doc->fresh()->isAvailable());
    }

    #[Test]
    public function javascript_url_is_rejected(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.documents.update', $doc), [
                'title' => $doc->title,
                'slug' => $doc->slug,
                'public_information_category_id' => $doc->public_information_category_id,
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'javascript:alert(1)',
                'status' => 'published',
            ])
            ->assertSessionHasErrors('external_url');
    }

    #[Test]
    public function https_external_url_is_accepted(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.documents.update', $doc), [
                'title' => $doc->title,
                'slug' => $doc->slug,
                'public_information_category_id' => $doc->public_information_category_id,
                'section_title' => $doc->section_title,
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://example.com/tarif.pdf',
                'status' => 'published',
                'sort_order' => 1,
            ])
            ->assertRedirect();

        $this->assertTrue($doc->fresh()->isAvailable());
        $this->get('/informasi-publik/berkala')->assertSee('https://example.com/tarif.pdf', false);
    }

    #[Test]
    public function unauthorized_upload_is_rejected(): void
    {
        $this->seedCatalog();
        $editor = User::factory()->editor()->create();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();

        $this->actingAs($editor)
            ->put(route('admin.documents.update', $doc), [
                'title' => $doc->title,
                'source_type' => PublicDocument::SOURCE_EXTERNAL,
                'external_url' => 'https://example.com/x.pdf',
                'status' => 'published',
            ])
            ->assertForbidden();
    }

    #[Test]
    public function remove_file_marks_document_missing(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();

        $path = 'ppid/documents/2026/09/temp.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4');
        $doc->update([
            'file_path' => $path,
            'file_name' => 'temp.pdf',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'external_url' => null,
            'internal_url' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.documents.remove-file', $doc))
            ->assertRedirect();

        $doc->refresh();
        $this->assertFalse($doc->isAvailable());
        Storage::disk('public')->assertMissing($path);
    }

    #[Test]
    public function admin_dashboard_shows_missing_count(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)
            ->get('/admin/ppid')
            ->assertOk()
            ->assertSee('WAJIB UNGGAH', false)
            ->assertSee('LAKIN Poltekkes Kemenkes Banten 2023', false);
    }
}
