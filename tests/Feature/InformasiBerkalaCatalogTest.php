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
        $response->assertSee('Rencana Kerja dan Anggaran', false);
        $response->assertSee('Rencana Kerja dan Anggaran 2026', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2022', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2023', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2024', false);
        $response->assertSee('LAKIN Poltekkes Kemenkes Banten 2025', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2023', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2024', false);
        $response->assertSee('Perjanjian Kinerja Tahun 2025', false);
        $response->assertSee('Panduan Akademik 2023/2024', false);
        $response->assertSee('Laporan Keuangan', false);
        $response->assertSee('Laporan Keuangan Tahun 2025 (Audited)', false);
        $response->assertSee('Tarif Layanan Akademik', false);
        $response->assertDontSee('Tarif Layanan Penunjang Akademik', false);
        $response->assertSee('Laporan PPID Tahun 2024', false);
        $response->assertSee('Dokumen belum tersedia', false);
        $response->assertDontSee('WAJIB UNGGAH', false);
        $response->assertDontSee('Admin PPID wajib', false);

        $html = $response->getContent();
        $this->assertStringContainsString('https://poltekkesbanten.ac.id/wp-content/uploads/2026/06/RAK-Poltekkes-Banten-2020-2024.pdf', $html);
        $this->assertStringContainsString('/halaman/akreditasi', $html);

        // Section order: RSB → Rencana Kerja dan Anggaran → LAKIN
        $posRsb = strpos($html, 'Rencana Strategis Bisnis (RSB) Poltekkes Kemenkes Banten');
        $posRka = strpos($html, 'Rencana Kerja dan Anggaran');
        $posLakin = strpos($html, 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten');
        $this->assertNotFalse($posRsb);
        $this->assertNotFalse($posRka);
        $this->assertNotFalse($posLakin);
        $this->assertTrue($posRsb < $posRka && $posRka < $posLakin);

        // Section order: Panduan Akademik → Laporan Keuangan → Pola Tarif
        $posPanduan = strpos($html, 'Panduan Akademik');
        $posKeuangan = strpos($html, 'Laporan Keuangan');
        $posTarif = strpos($html, 'Pola Tarif Poltekkes Kemenkes Banten');
        $this->assertNotFalse($posPanduan);
        $this->assertNotFalse($posKeuangan);
        $this->assertNotFalse($posTarif);
        $this->assertTrue($posPanduan < $posKeuangan && $posKeuangan < $posTarif);

        // Missing docs must not be wrapped as clickable href to empty destinations.
        $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
    }

    #[Test]
    public function laporan_keuangan_2025_is_missing_until_admin_upload(): void
    {
        $this->seedCatalog();

        $doc = PublicDocument::query()
            ->where('slug', PublicDocument::makeCatalogSlug('Laporan Keuangan Tahun 2025 (Audited)'))
            ->first();

        $this->assertNotNull($doc);
        $this->assertSame('Laporan Keuangan', $doc->section_title);
        $this->assertSame(2025, $doc->year);
        $this->assertSame(PublicDocument::SOURCE_UPLOADED, $doc->source_type);
        $this->assertFalse($doc->isAvailable());

        $html = $this->get('/informasi-publik/berkala')->assertOk()->getContent();
        $pos = strpos($html, 'Laporan Keuangan Tahun 2025 (Audited)');
        $this->assertNotFalse($pos);
        $chunk = substr($html, $pos, 800);
        $this->assertStringContainsString('Dokumen belum tersedia', $chunk);
        $this->assertStringNotContainsString('Lihat Dokumen', $chunk);

        $admin = User::factory()->adminPpid()->create();
        $this->actingAs($admin)
            ->get(route('admin.informasi-publik.berkala', ['status' => 'missing']))
            ->assertOk()
            ->assertSee('Laporan Keuangan Tahun 2025 (Audited)', false)
            ->assertSee('WAJIB UNGGAH', false);
    }

    #[Test]
    public function rencana_kerja_anggaran_2026_is_missing_until_admin_upload(): void
    {
        $this->seedCatalog();

        $doc = PublicDocument::query()
            ->where('slug', PublicDocument::makeCatalogSlug('Rencana Kerja dan Anggaran 2026'))
            ->first();

        $this->assertNotNull($doc);
        $this->assertSame('Rencana Kerja dan Anggaran', $doc->section_title);
        $this->assertSame(PublicDocument::SOURCE_UPLOADED, $doc->source_type);
        $this->assertFalse($doc->isAvailable());

        $html = $this->get('/informasi-publik/berkala')->assertOk()->getContent();
        $pos = strpos($html, 'Rencana Kerja dan Anggaran 2026');
        $this->assertNotFalse($pos);
        $chunk = substr($html, $pos, 800);
        $this->assertStringContainsString('Dokumen belum tersedia', $chunk);
        $this->assertStringNotContainsString('Lihat Dokumen', $chunk);

        $admin = User::factory()->adminPpid()->create();
        $this->actingAs($admin)
            ->get(route('admin.informasi-publik.berkala', ['status' => 'missing']))
            ->assertOk()
            ->assertSee('Rencana Kerja dan Anggaran 2026', false)
            ->assertSee('WAJIB UNGGAH', false);
    }

    #[Test]
    public function missing_document_has_no_lihat_dokumen_link(): void
    {
        $this->seedCatalog();

        $response = $this->get('/informasi-publik/berkala');
        $html = $response->getContent();

        $this->assertMatchesRegularExpression(
            '/LAKIN Poltekkes Kemenkes Banten 2025[\s\S]*?Dokumen belum tersedia/',
            $html,
        );

        $pos = strpos($html, 'LAKIN Poltekkes Kemenkes Banten 2025');
        $chunk = substr($html, $pos, 800);
        $this->assertStringNotContainsString('Lihat Dokumen', $chunk);
    }

    #[Test]
    public function lakin_section_drops_empty_duplicates_when_available_file_exists(): void
    {
        $this->seedCatalog();

        $empty2023 = PublicDocument::query()
            ->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')
            ->firstOrFail();
        $this->assertFalse($empty2023->isAvailable());

        PublicDocument::query()->create([
            'title' => 'LAKIP Poltekkes Kemenkes Banten 2023',
            'slug' => 'lakip-poltekkes-kemenkes-banten-2023',
            'public_information_category_id' => $empty2023->public_information_category_id,
            'document_category_id' => $empty2023->document_category_id,
            'section_title' => 'Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten',
            'year' => 2023,
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => 'ppid/documents/2026/09/lakip-2023.pdf',
            'file_name' => 'lakip-2023.pdf',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 55,
        ]);

        $this->seed(InformasiBerkalaSeeder::class);

        $this->assertNull(
            PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->whereNull('file_path')->first()
        );
        $this->assertNull(PublicDocument::query()->where('title', 'LAKIN Poltekkes Kemenkes Banten 2023')->whereNull('file_path')->whereNull('external_url')->first());

        $available = PublicDocument::query()
            ->where('title', 'LAKIN Poltekkes Kemenkes Banten 2023')
            ->first();
        $this->assertNotNull($available);
        $this->assertTrue($available->isAvailable());

        $html = $this->get('/informasi-publik/berkala')->assertOk()->getContent();
        $this->assertSame(1, substr_count($html, 'LAKIN Poltekkes Kemenkes Banten 2023'));
        $this->assertStringNotContainsString('LAKIP Poltekkes Kemenkes Banten 2023', $html);
        $this->assertStringContainsString('LAKIN Poltekkes Kemenkes Banten 2025', $html);
    }

    #[Test]
    public function unavailable_tarif_penunjang_placeholder_is_removed_without_touching_available_tariff_documents(): void
    {
        $this->seedCatalog();

        $this->assertNull(PublicDocument::query()->where('title', 'Tarif Layanan Penunjang Akademik')->first());

        $akademik = PublicDocument::query()->where('slug', 'tarif-layanan-akademik')->firstOrFail();
        $akademik->update([
            'title' => 'SK Tarif BLU Akademik',
            'file_path' => 'ppid/documents/sk-tarif-blu-akademik.pdf',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
        ]);
        $penunjang = PublicDocument::query()->create([
            'title' => 'SK Tarif BLU Penunjang Akademik',
            'slug' => 'sk-tarif-blu-penunjang-akademik',
            'public_information_category_id' => $akademik->public_information_category_id,
            'document_category_id' => $akademik->document_category_id,
            'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'file_path' => 'ppid/documents/sk-tarif-blu-penunjang.pdf',
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 20,
        ]);
        PublicDocument::query()->create([
            'title' => 'Tarif Layanan Penunjang Akademik',
            'slug' => 'tarif-layanan-penunjang-akademik',
            'public_information_category_id' => $akademik->public_information_category_id,
            'document_category_id' => $akademik->document_category_id,
            'section_title' => 'Pola Tarif Poltekkes Kemenkes Banten',
            'source_type' => PublicDocument::SOURCE_UPLOADED,
            'status' => 'published',
            'published_at' => now(),
            'sort_order' => 30,
        ]);

        $this->seed(InformasiBerkalaSeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);

        $this->assertNull(PublicDocument::query()->where('slug', 'tarif-layanan-penunjang-akademik')->first());
        $this->assertNotNull(PublicDocument::onlyTrashed()->where('slug', 'tarif-layanan-penunjang-akademik')->first());
        $this->assertTrue($akademik->fresh()->isAvailable());
        $this->assertSame('SK Tarif BLU Akademik', $akademik->fresh()->title);
        $this->assertTrue($penunjang->fresh()->isAvailable());
        $this->assertNull($penunjang->fresh()->deleted_at);

        $html = $this->get('/informasi-publik/berkala')->assertOk()->getContent();
        $this->assertStringNotContainsString('Tarif Layanan Penunjang Akademik', $html);
        $this->assertSame(1, substr_count($html, 'SK Tarif BLU Penunjang Akademik'));
        $this->assertSame(1, substr_count($html, 'SK Tarif BLU Akademik'));
        $this->assertStringContainsString('Pola Tarif Poltekkes Kemenkes Banten', $html);
        $this->assertStringContainsString('Panduan Akademik', $html);
        $this->assertStringContainsString('Laporan Keuangan Tahun 2025 (Audited)', $html);
        $this->assertStringContainsString('LAKIN Poltekkes Kemenkes Banten 2025', $html);
        $this->assertStringContainsString('Dokumen belum tersedia', $html);
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
