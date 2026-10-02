<?php

namespace Tests\Feature;

use App\Models\InformationServiceSummary;
use App\Models\MediaAsset;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SaqEvidence;
use App\Models\SaqItem;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MediaLibrary;
use App\Services\SaqCompleteness;
use App\Support\PublicNavigation;
use Database\Seeders\SaqItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CmsPpidTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_cannot_open_the_cms(): void
    {
        $this->get('/admin/saq')->assertRedirect(route('admin.login'));
        $this->get('/admin/media')->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk();
    }

    #[Test]
    public function admin_ppid_can_open_the_cms_and_create_a_draft_page(): void
    {
        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)->get('/admin/saq')->assertOk();
        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Halaman Uji CMS',
            'status' => 'draft',
        ])->assertRedirect();

        $page = Page::query()->where('title', 'Halaman Uji CMS')->first();
        $this->assertNotNull($page);
        $this->assertSame('draft', $page->status);
        $this->get('/halaman/'.$page->slug)->assertNotFound();
    }

    #[Test]
    public function published_page_is_public_and_duplicate_or_unsafe_html_is_rejected(): void
    {
        $admin = User::factory()->adminPpid()->create();
        Page::query()->create([
            'title' => 'Sudah ada',
            'slug' => 'sudah-ada',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Duplikat',
            'slug' => 'sudah-ada',
            'status' => 'draft',
        ])->assertSessionHasErrors('slug');

        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Terbit aman',
            'slug' => 'terbit-aman',
            'status' => 'published',
            'body' => '<p onclick="alert(1)">Halo</p><script>alert(2)</script>',
        ])->assertRedirect();

        $page = Page::query()->where('slug', 'terbit-aman')->firstOrFail();
        $this->assertStringNotContainsString('<script', $page->body);
        $this->assertStringNotContainsString('onclick', $page->body);
        $this->get('/halaman/terbit-aman')->assertOk()->assertSee('Halo');
    }

    #[Test]
    public function media_accepts_allowed_types_and_hides_internal_files(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $admin = User::factory()->adminPpid()->create();

        foreach ([
            ['foto.png', 'image/png', 'png-bytes'],
            ['berkas.pdf', 'application/pdf', 'pdf-bytes'],
            ['surat.doc', 'application/msword', 'doc-bytes'],
            ['surat.docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'docx-bytes'],
        ] as [$name, $mime, $content]) {
            $this->actingAs($admin)->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->createWithContent($name, $content)->mimeType($mime),
                'visibility' => 'public',
            ])->assertRedirect();
        }

        $this->assertSame(4, MediaAsset::query()->count());
        $public = MediaAsset::query()->where('extension', 'png')->firstOrFail();
        $this->get(route('media.show', $public->uuid))->assertOk();

        $this->actingAs($admin)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->create('rahasia.pdf', 20, 'application/pdf'),
            'visibility' => 'internal',
        ])->assertRedirect();

        $internal = MediaAsset::query()->where('visibility', 'internal')->firstOrFail();
        $this->assertNull($internal->publicUrl());
        auth()->logout();
        $this->get('/media/'.$internal->uuid)->assertNotFound();
    }

    #[Test]
    public function media_rejects_executable_markup_and_oversized_files(): void
    {
        Storage::fake('public');
        $admin = User::factory()->adminPpid()->create();

        foreach (['shell.php', 'halaman.html', 'ikon.svg', 'arsip.zip'] as $name) {
            $this->actingAs($admin)->post(route('admin.media.store'), [
                'file' => UploadedFile::fake()->create($name, 10, 'text/plain'),
                'visibility' => 'public',
            ])->assertSessionHasErrors('file');
        }

        config(['ppid.max_upload_mb' => 1]);
        $this->actingAs($admin)->post(route('admin.media.store'), [
            'file' => UploadedFile::fake()->create('besar.pdf', 1100, 'application/pdf'),
            'visibility' => 'public',
        ])->assertSessionHasErrors('file');
    }

    #[Test]
    public function failed_replace_keeps_the_previous_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('ppid/media/lama.png', 'lama');

        $asset = MediaAsset::query()->create([
            'uuid' => '11111111-1111-1111-1111-111111111111',
            'original_name' => 'lama.png',
            'stored_name' => 'lama.png',
            'disk' => 'public',
            'path' => 'ppid/media/lama.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size' => 4,
            'checksum' => str_repeat('a', 64),
            'visibility' => 'public',
        ]);

        $library = new class extends MediaLibrary
        {
            protected function persist(MediaAsset $asset, array $attributes): void
            {
                throw new \RuntimeException('db fail');
            }
        };

        try {
            $library->replace($asset, UploadedFile::fake()->create('baru.png', 20, 'image/png'));
            $this->fail('Penggantian seharusnya gagal.');
        } catch (\RuntimeException) {
            Storage::disk('public')->assertExists('ppid/media/lama.png');
            $this->assertSame('ppid/media/lama.png', $asset->fresh()->path);
        }
    }

    #[Test]
    public function menu_supports_submenu_order_and_rejects_bad_targets(): void
    {
        $admin = User::factory()->adminPpid()->create();
        $menu = Menu::query()->create(['name' => 'Navigasi Utama', 'location' => 'primary', 'is_active' => true]);
        $parent = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Induk',
            'type' => 'route',
            'route_name' => 'home',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        $second = MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'Kedua',
            'type' => 'route',
            'route_name' => 'contact',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post(route('admin.menus.items.store'), [
            'label' => 'Anak',
            'parent_id' => $parent->id,
            'type' => 'route',
            'route_name' => 'faqs.index',
            'location' => 'primary',
        ])->assertRedirect();

        $this->assertDatabaseHas('menu_items', ['label' => 'Anak', 'parent_id' => $parent->id]);

        $this->actingAs($admin)->post(route('admin.menus.items.move', $second), [
            'direction' => 'up',
        ])->assertRedirect();
        $this->assertTrue($second->fresh()->sort_order < $parent->fresh()->sort_order);

        $this->actingAs($admin)->post(route('admin.menus.items.store'), [
            'label' => 'Buruk',
            'type' => 'url',
            'url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors('url');

        Page::query()->create([
            'title' => 'Masih draft',
            'slug' => 'masih-draft',
            'status' => 'draft',
        ]);
        $this->actingAs($admin)->post(route('admin.menus.items.store'), [
            'label' => 'Draft',
            'type' => 'page',
            'page_slug' => 'masih-draft',
        ])->assertSessionHasErrors('page_slug');
    }

    #[Test]
    public function desktop_and_mobile_share_the_navigation_source(): void
    {
        $html = view('components.layout.header')->render();
        $this->assertStringNotContainsString('NAVDB-MARKER-77', $html);
        $this->assertStringContainsString('Beranda', $html);

        $menu = Menu::query()->create(['name' => 'Navigasi Utama', 'location' => 'primary', 'is_active' => true]);
        MenuItem::query()->create([
            'menu_id' => $menu->id,
            'label' => 'NAVDB-MARKER-77',
            'type' => 'route',
            'route_name' => 'home',
            'sort_order' => 0,
            'is_active' => true,
        ]);
        SiteSetting::query()->create([
            'group' => 'navigation',
            'key' => 'navigation.public_source',
            'value' => 'database',
            'type' => 'text',
        ]);
        SiteSetting::forgetCache();
        PublicNavigation::forget();

        $databaseHtml = view('components.layout.header')->render();
        $this->assertSame(2, substr_count($databaseHtml, 'NAVDB-MARKER-77'));
    }

    #[Test]
    public function saq_completion_requires_public_evidence_and_seed_is_idempotent(): void
    {
        $page = Page::query()->create([
            'title' => 'Tidak ditimpa',
            'slug' => 'tidak-ditimpa',
            'status' => 'published',
            'published_at' => now(),
            'body' => 'ADMIN-BODY',
        ]);

        $this->seed(SaqItemSeeder::class);
        $count = SaqItem::query()->count();
        $this->seed(SaqItemSeeder::class);
        $this->assertSame($count, SaqItem::query()->count());
        $this->assertSame(1, SaqItem::query()->where('code', 'BERKALA-02')->count());
        $this->assertSame('ADMIN-BODY', $page->fresh()->body);

        $service = app(SaqCompleteness::class);
        $missing = SaqItem::query()->where('code', 'BERKALA-02')->firstOrFail();
        $this->assertSame('missing', $service->assess($missing)['status']);

        $draft = Page::query()->create([
            'title' => 'Draft bukti',
            'slug' => 'draft-bukti',
            'status' => 'draft',
        ]);
        SaqEvidence::query()->create([
            'saq_item_id' => $missing->id,
            'evidence_type' => 'page',
            'page_id' => $draft->id,
            'visibility' => 'public',
        ]);
        $missing->unsetRelation('evidences');
        $this->assertSame('draft', $service->assess($missing->fresh())['status']);

        $published = Page::query()->create([
            'title' => 'Bukti terbit',
            'slug' => 'bukti-terbit',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $item = SaqItem::query()->where('code', 'BERKALA-03')->firstOrFail();
        SaqEvidence::query()->create([
            'saq_item_id' => $item->id,
            'evidence_type' => 'page',
            'page_id' => $published->id,
            'visibility' => 'public',
        ]);
        $done = $service->assess($item->fresh());
        $this->assertSame('complete', $done['status']);
        $this->assertSame(route('pages.show', 'bukti-terbit'), $done['public_url']);

        $internalItem = SaqItem::query()->where('code', 'BERKALA-05')->firstOrFail();
        SaqEvidence::query()->create([
            'saq_item_id' => $internalItem->id,
            'evidence_type' => 'page',
            'page_id' => $published->id,
            'visibility' => 'internal',
        ]);
        $internal = $service->assess($internalItem->fresh());
        $this->assertNotSame('complete', $internal['status']);
        $this->assertNotSame('published', $internal['status']);
        $this->assertNull($internal['public_url']);

        $admin = User::factory()->adminPpid()->create();
        $this->actingAs($admin)
            ->get(route('admin.saq.show', $item))
            ->assertOk()
            ->assertSee(route('pages.show', 'bukti-terbit'));
    }

    #[Test]
    public function public_register_summary_has_no_personal_fields(): void
    {
        $this->assertNotContains('nik', (new InformationServiceSummary)->getFillable());
        $this->assertNotContains('email', (new InformationServiceSummary)->getFillable());
        $this->assertNotContains('phone', (new InformationServiceSummary)->getFillable());

        InformationServiceSummary::query()->create([
            'year' => 2025,
            'total_received' => 4,
            'fulfilled_full' => 2,
            'fulfilled_partial' => 1,
            'rejected' => 1,
            'rejection_reasons' => 'Dokumen dikecualikan.',
        ]);
        Page::query()->create([
            'title' => 'Ringkasan Layanan',
            'slug' => 'ringkasan-layanan-informasi-publik',
            'status' => 'published',
            'published_at' => now(),
            'body' => '<p>Ringkasan resmi</p>',
        ]);

        $this->get('/halaman/ringkasan-layanan-informasi-publik')
            ->assertOk()
            ->assertSee('Identitas pemohon tidak ditampilkan')
            ->assertSee('Dokumen dikecualikan.')
            ->assertDontSee('NIK');
    }

    #[Test]
    public function saq_audit_command_prints_dynamic_counts(): void
    {
        $this->seed(SaqItemSeeder::class);

        $this->artisan('ppid:saq-audit', ['--year' => 2026])
            ->expectsOutputToContain('BERKALA-02')
            ->expectsOutputToContain('Missing:')
            ->assertSuccessful();

        $this->artisan('ppid:saq-audit', ['--year' => 2026, '--json' => true])
            ->assertSuccessful();

        $output = \Illuminate\Support\Facades\Artisan::output();
        $this->assertStringNotContainsString('password', strtolower($output));
        $this->assertStringNotContainsString('storage\\app\\private', $output);
    }
}
