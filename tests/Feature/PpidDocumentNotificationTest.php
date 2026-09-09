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

class PpidDocumentNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function seedCatalog(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(PageSeeder::class);
        $this->seed(InformasiBerkalaSeeder::class);
    }

    #[Test]
    public function guest_sees_login_admin_and_not_bell(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Login Admin', false)
            ->assertSee('PPID-ADMIN-LOGIN-NAV-DEPLOY-20260909-A', false)
            ->assertSee(route('admin.login', absolute: false), false)
            ->assertDontSee('Notifikasi dokumen PPID', false)
            ->assertDontSee('Perhatian — Dokumen Belum Lengkap', false);
    }

    #[Test]
    public function authenticated_admin_sees_bell_not_login_admin(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create(['name' => 'Admin PPID Uji']);

        $html = $this->actingAs($admin)->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('Login Admin', $html);
        $this->assertStringContainsString('Notifikasi dokumen PPID', $html);
        $this->assertStringContainsString('PPID-ADMIN-NOTIFICATION-DEPLOY-20260909-A', $html);
        $this->assertStringContainsString('Admin PPID Uji', $html);
    }

    #[Test]
    public function notification_count_matches_missing_query(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $service = app(PpidDocumentNotificationService::class);

        $count = $service->missingCount();
        $this->assertGreaterThan(0, $count);

        $html = $this->actingAs($admin)->get('/')->assertOk()->getContent();
        $this->assertStringContainsString($count.' dokumen belum lengkap', $html);
        $this->assertStringContainsString('LAKIN Poltekkes Kemenkes Banten 2023', $html);
    }

    #[Test]
    public function available_document_is_not_listed_as_missing(): void
    {
        $this->seedCatalog();
        $available = PublicDocument::query()
            ->get()
            ->first(fn (PublicDocument $document) => $document->isAvailable() && str_contains($document->title, 'RAK'));
        $this->assertNotNull($available);
        $this->assertTrue($available->isAvailable());

        $titles = app(PpidDocumentNotificationService::class)
            ->missingDocuments()
            ->pluck('title');

        $this->assertFalse($titles->contains($available->title));
    }

    #[Test]
    public function upload_pdf_reduces_count_and_public_shows_lihat_dokumen(): void
    {
        Storage::fake('public');
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $service = app(PpidDocumentNotificationService::class);

        $before = $service->missingCount();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();
        $this->assertFalse($doc->isAvailable());

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
            ->assertRedirect(route('admin.informasi-publik.berkala'))
            ->assertSessionHas('status', 'Dokumen berhasil dilengkapi dan telah tersedia pada portal PPID.');

        $service->forgetCache();
        $this->assertTrue($doc->fresh()->isAvailable());
        $this->assertSame($before - 1, $service->missingCount());

        $missingTitles = $service->missingDocuments()->pluck('slug');
        $this->assertFalse($missingTitles->contains('lakin-poltekkes-kemenkes-banten-2023'));

        $public = $this->get('/informasi-publik/berkala')->assertOk()->getContent();
        $pos = strpos($public, 'LAKIN Poltekkes Kemenkes Banten 2023');
        $this->assertNotFalse($pos);
        $chunk = substr($public, $pos, 1600);
        $this->assertStringContainsString('is-available', $chunk);
        $this->assertStringContainsString('/dokumen-publik/lakin-poltekkes-kemenkes-banten-2023/unduh', $chunk);
        $this->assertStringNotContainsString('Perhatian — Dokumen Belum Lengkap', $public);
    }

    #[Test]
    public function remove_source_increases_count(): void
    {
        Storage::fake('public');
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $service = app(PpidDocumentNotificationService::class);

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
        $service->forgetCache();

        $afterUpload = $service->missingCount();

        $this->actingAs($admin)
            ->delete(route('admin.documents.remove-file', $doc))
            ->assertRedirect();

        $service->forgetCache();
        $this->assertFalse($doc->fresh()->isAvailable());
        $this->assertSame($afterUpload + 1, $service->missingCount());
    }

    #[Test]
    public function count_zero_shows_empty_success_state(): void
    {
        $admin = User::factory()->adminPpid()->create();

        $html = $this->actingAs($admin)->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Seluruh dokumen Informasi Berkala telah tersedia', $html);
        $this->assertStringNotContainsString('Masih terdapat', $html);
    }

    #[Test]
    public function notification_item_links_to_admin_edit(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $doc = PublicDocument::query()->where('slug', 'lakin-poltekkes-kemenkes-banten-2023')->firstOrFail();

        $this->actingAs($admin)
            ->get('/')
            ->assertSee(route('admin.documents.edit', $doc, false), false)
            ->assertSee(route('admin.informasi-publik.berkala', ['status' => 'missing'], false), false);
    }

    #[Test]
    public function missing_filter_page_and_dashboard_warning_are_dynamic(): void
    {
        $this->seedCatalog();
        $admin = User::factory()->adminPpid()->create();
        $count = app(PpidDocumentNotificationService::class)->missingCount();

        $this->actingAs($admin)
            ->get(route('admin.informasi-publik.berkala', ['status' => 'missing']))
            ->assertOk()
            ->assertSee('Dokumen Wajib Dilengkapi', false)
            ->assertSee('LAKIN Poltekkes Kemenkes Banten 2023', false)
            ->assertDontSee('RAK Poltekkes Banten 2020–2024', false);

        $this->actingAs($admin)
            ->get('/admin/ppid')
            ->assertOk()
            ->assertSee('Perhatian — Dokumen Belum Lengkap', false)
            ->assertSee((string) $count, false)
            ->assertSee('Kelola Dokumen Sekarang', false);
    }

    #[Test]
    public function verify_documents_command_is_dynamic(): void
    {
        $this->seedCatalog();
        $stats = app(PpidDocumentNotificationService::class)->stats();

        $this->artisan('ppid:verify-documents')
            ->expectsOutputToContain('Total .................... '.$stats['total'])
            ->expectsOutputToContain('Belum tersedia ........... '.$stats['missing'])
            ->expectsOutputToContain('Notification service ..... PASS')
            ->assertSuccessful();
    }
}
