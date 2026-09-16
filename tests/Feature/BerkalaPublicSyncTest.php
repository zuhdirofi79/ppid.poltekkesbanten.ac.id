<?php

namespace Tests\Feature;

use App\Models\PublicDocument;
use App\Models\User;
use App\Services\PpidDocumentNotificationService;
use Database\Seeders\CategorySeeder;
use Database\Seeders\InformasiBerkalaSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BerkalaPublicSyncTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalog(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);
    }

    /**
     * Reproduces production: admin marks tariff PDFs TERSEDIA after upload/rename,
     * but a draft/future-published row is dropped by the public published() scope,
     * so the whole Pola Tarif section disappears.
     */
    #[Test]
    public function uploaded_berkala_document_still_appears_on_public_page_when_status_is_draft(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $akademik = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();
        $penunjang = PublicDocument::query()->where('slug', 'tarif-layanan-penunjang-akademik')->firstOrFail();

        $path = 'ppid/documents/2026/09/sk-tarif-blu-akademik.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4');

        $akademik->update([
            'title' => 'SK Tarif BLU Akademik',
            'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => $path,
            'file_name' => 'sk-tarif-blu-akademik.pdf',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $path2 = 'ppid/documents/2026/09/sk-tarif-blu-penunjang.pdf';
        Storage::disk('public')->put($path2, '%PDF-1.4');
        $penunjang->update([
            'title' => 'SK Tarif BLU Penunjang Akademik Rev 9',
            'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => $path2,
            'file_name' => 'sk-tarif-blu-penunjang.pdf',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->assertTrue($akademik->fresh()->isAvailable());
        $this->assertTrue($penunjang->fresh()->isAvailable());

        $admin = User::factory()->adminPpid()->create();
        $this->actingAs($admin)
            ->get(route('admin.informasi-publik.berkala'))
            ->assertOk()
            ->assertSee('Pola Tarif Poltekkes Kemenkes Banten', false)
            ->assertSee('SK Tarif BLU Akademik', false)
            ->assertSee('SK Tarif BLU Penunjang Akademik Rev 9', false)
            ->assertSee('TERSEDIA', false);

        $public = $this->get('/informasi-publik/berkala')->assertOk();
        $public->assertSee('PPID-BERKALA-PUBLIC-SYNC-FIX-20260916-A', false);
        $public->assertSee('Pola Tarif Poltekkes Kemenkes Banten', false);
        $public->assertSee('SK Tarif BLU Akademik', false);
        $public->assertSee('SK Tarif BLU Penunjang Akademik Rev 9', false);
        $public->assertSee('Lihat Dokumen', false);

        $this->get(route('documents.download', $akademik->fresh()->slug))->assertOk();
        $this->get(route('documents.download', $penunjang->fresh()->slug))->assertOk();
    }

    #[Test]
    public function uploaded_berkala_document_still_appears_when_published_at_is_in_the_future(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $akademik = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();
        $path = 'ppid/documents/2026/09/sk-tarif-future.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4');
        $akademik->update([
            'title' => 'SK Tarif BLU Akademik',
            'file_path' => $path,
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'status' => 'published',
            'published_at' => now()->addHours(8),
        ]);

        $this->get('/informasi-publik/berkala')
            ->assertOk()
            ->assertSee('Pola Tarif Poltekkes Kemenkes Banten', false)
            ->assertSee('SK Tarif BLU Akademik', false);

        $this->get(route('documents.download', $akademik->fresh()->slug))->assertOk();
    }

    #[Test]
    public function admin_upload_keeps_berkala_document_publicly_listed(): void
    {
        Storage::fake('public');
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.documents.update', $doc), [
                'title' => 'SK Tarif BLU Akademik',
                'slug' => $doc->slug,
                'public_information_category_id' => $doc->public_information_category_id,
                'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
                'source_type' => PublicDocument::SOURCE_UPLOADED,
                'status' => 'draft',
                'sort_order' => $doc->sort_order,
                'file' => UploadedFile::fake()->create('sk-tarif.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect(route('admin.informasi-publik.berkala'));

        $fresh = $doc->fresh();
        $this->assertTrue($fresh->isAvailable());
        $this->assertSame('published', $fresh->status);
        $this->assertNotNull($fresh->published_at);
        $this->assertTrue($fresh->published_at->lte(now()->addMinute()));

        $this->get('/informasi-publik/berkala')
            ->assertSee('SK Tarif BLU Akademik', false)
            ->assertSee('Pola Tarif Poltekkes Kemenkes Banten', false);

        $this->get(route('documents.download', $fresh->slug))->assertOk();
    }

    #[Test]
    public function notification_and_search_treat_available_tariff_docs_as_complete(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $akademik = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();
        $path = 'ppid/documents/2026/09/sk-tarif.pdf';
        Storage::disk('public')->put($path, '%PDF-1.4');
        $akademik->update([
            'title' => 'SK Tarif BLU Akademik',
            'file_path' => $path,
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'status' => 'draft',
        ]);

        $missing = app(PpidDocumentNotificationService::class)->missingDocuments()->pluck('title');
        $this->assertFalse($missing->contains('SK Tarif BLU Akademik'));

        $this->get('/cari?q=tarif')
            ->assertOk()
            ->assertSee('SK Tarif BLU Akademik', false);
    }

    #[Test]
    public function unpublished_non_berkala_document_stays_hidden(): void
    {
        Storage::fake('public');
        $this->seedCatalog();

        $doc = PublicDocument::query()->create([
            'title' => 'Dokumen Internal Draft',
            'slug' => 'dokumen-internal-draft',
            'status' => 'draft',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => 'ppid/documents/2026/09/internal.pdf',
        ]);
        Storage::disk('public')->put($doc->file_path, '%PDF-1.4');

        $this->get('/informasi-publik/berkala')->assertDontSee('Dokumen Internal Draft', false);
        $this->get(route('documents.download', $doc->slug))->assertNotFound();
    }

    #[Test]
    public function missing_published_berkala_docs_remain_visible(): void
    {
        $this->seedCatalog();

        $this->get('/informasi-publik/berkala')
            ->assertOk()
            ->assertSee('Laporan PPID Tahun 2024', false)
            ->assertSee('Dokumen belum tersedia', false);
    }
}
