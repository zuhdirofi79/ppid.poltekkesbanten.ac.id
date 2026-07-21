<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PublicDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_create_post(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->post('/admin/posts', [
                'title' => 'CONTOH Berita Baru',
                'excerpt' => 'Ringkasan',
                'body' => 'Isi berita',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'CONTOH Berita Baru',
            'status' => 'published',
        ]);
    }

    public function test_operator_cannot_create_post(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->post('/admin/posts', [
                'title' => 'Tidak boleh',
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_operator_can_create_document_with_external_url(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->post('/admin/documents', [
                'title' => 'CONTOH Dokumen Eksternal',
                'status' => 'published',
                'external_url' => 'https://example.com/doc.pdf',
                'is_external' => '1',
            ])
            ->assertRedirect(route('admin.documents.index'));

        $this->assertDatabaseHas('public_documents', [
            'title' => 'CONTOH Dokumen Eksternal',
            'is_external' => 1,
        ]);
    }

    public function test_document_upload_rejects_executable(): void
    {
        Storage::fake('public');
        $admin = User::factory()->superAdmin()->create();

        $this->actingAs($admin)
            ->post('/admin/documents', [
                'title' => 'File berbahaya',
                'status' => 'draft',
                'file' => UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors('file');
    }

    public function test_editor_cannot_access_users(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_settings_and_update_post(): void
    {
        $admin = User::factory()->superAdmin()->create();
        $post = Post::query()->create([
            'title' => 'Awal',
            'slug' => 'awal',
            'status' => 'draft',
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get('/admin/settings')
            ->assertOk();

        $this->actingAs($admin)
            ->put('/admin/posts/'.$post->id, [
                'title' => 'Diperbarui',
                'slug' => 'diperbarui',
                'status' => 'published',
                'body' => 'Konten',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertSame('Diperbarui', $post->fresh()->title);
    }

    public function test_unique_slug_helper_avoids_collision(): void
    {
        Post::query()->create([
            'title' => 'Sama',
            'slug' => 'sama',
            'status' => 'draft',
        ]);

        $slug = \App\Support\UniqueSlug::make('Sama', Post::class);
        $this->assertSame('sama-2', $slug);
    }
}
