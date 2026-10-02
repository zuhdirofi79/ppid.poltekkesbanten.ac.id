<?php

namespace Tests\Feature;

use App\Models\LeadershipProfile;
use App\Models\User;
use Database\Seeders\LeadershipProfileSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LeadershipProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PageSeeder::class);
        $this->seed(LeadershipProfileSeeder::class);
    }

    #[Test]
    public function public_page_lists_leaders_in_order_with_education_and_work_history(): void
    {
        $response = $this->get('/halaman/profil-pimpinan-satuan-kerja');

        $response->assertOk();
        $response->assertSee('PPID-PROFIL-PIMPINAN-DEPLOY-20261002-A', false);
        $response->assertSee('Dr. Dahrizal', false);
        $response->assertSee('Kusniawati', false);
        $response->assertSee('Sendy Cokro Wibowo', false);
        $response->assertSee('Riwayat Pendidikan', false);
        $response->assertSee('Riwayat Pekerjaan', false);
        $response->assertSee('Universitas Padjadjaran', false);
        $response->assertSee('Universitas Islam Negeri Syarif Hidayatullah Jakarta', false);
        $response->assertSee('Sarjana Ekonomi', false);
        $response->assertDontSee('Konten halaman ini sedang disiapkan', false);
        $response->assertSee('direktur-dahrizal.png', false);

        $html = $response->getContent();
        $director = strpos($html, 'Dr. Dahrizal');
        $viceOne = strpos($html, 'Kusniawati');
        $viceTwo = strpos($html, 'Sendy Cokro Wibowo');
        $this->assertNotFalse($director);
        $this->assertLessThan($viceOne, $director);
        $this->assertLessThan($viceTwo, $viceOne);
    }

    #[Test]
    public function unpublished_profile_is_hidden_and_seeder_does_not_overwrite_admin_edits(): void
    {
        $hidden = LeadershipProfile::query()->create([
            'position_key' => 'sekretaris-uji',
            'name' => 'Nama Rahasia Uji',
            'position' => 'Sekretaris Uji',
            'is_published' => false,
            'sort_order' => 5,
        ]);

        $this->get('/halaman/profil-pimpinan-satuan-kerja')
            ->assertOk()
            ->assertDontSee('Nama Rahasia Uji', false);

        $director = LeadershipProfile::query()->where('position_key', 'director')->firstOrFail();
        $director->update([
            'name' => 'Nama Hasil Suntingan Admin',
            'education' => 'Pendidikan yang ditulis admin',
        ]);

        $this->seed(LeadershipProfileSeeder::class);

        $director->refresh();
        $this->assertSame('Nama Hasil Suntingan Admin', $director->name);
        $this->assertSame('Pendidikan yang ditulis admin', $director->education);
        $this->assertSame(4, LeadershipProfile::query()->count());
        $this->assertTrue($hidden->fresh()->exists);
    }

    #[Test]
    public function guest_is_redirected_and_admin_can_manage_profiles(): void
    {
        $this->get('/admin/profil-pimpinan')->assertRedirect(route('admin.login'));

        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)->get('/admin/profil-pimpinan')->assertOk()->assertSee('Dr. Dahrizal', false);

        $this->actingAs($admin)->post(route('admin.leadership-profiles.store'), [
            'name' => 'Profil Baru',
            'position' => 'Kepala Unit Uji',
            'short_summary' => 'Ringkasan baru',
            'education' => "Satu baris pendidikan",
            'work_history' => "Satu baris pekerjaan",
            'sort_order' => 40,
            'is_published' => '1',
        ])->assertRedirect(route('admin.leadership-profiles.index'));

        $created = LeadershipProfile::query()->where('name', 'Profil Baru')->firstOrFail();
        $this->assertTrue($created->is_published);

        $this->actingAs($admin)->put(route('admin.leadership-profiles.update', $created), [
            'name' => 'Profil Baru',
            'position' => 'Kepala Unit Uji',
            'detail_body' => '<script>alert(1)</script><p onclick="alert(2)">Aman</p>',
            'education' => 'Pendidikan aman',
            'work_history' => 'Pekerjaan aman',
            'sort_order' => 40,
            'is_published' => '1',
        ])->assertRedirect();

        $created->refresh();
        $this->assertStringNotContainsString('<script', (string) $created->detail_body);
        $this->assertStringNotContainsString('onclick', (string) $created->detail_body);

        $this->get('/halaman/profil-pimpinan-satuan-kerja')
            ->assertOk()
            ->assertSee('Profil Baru', false)
            ->assertSee('Aman', false)
            ->assertDontSee('<script>alert(1)</script>', false);

        $this->actingAs($admin)->post(route('admin.leadership-profiles.publish', $created))
            ->assertRedirect();
        $this->assertFalse($created->fresh()->is_published);
        $this->get('/halaman/profil-pimpinan-satuan-kerja')->assertDontSee('Profil Baru', false);
    }

    #[Test]
    public function admin_can_upload_a_photo_and_invalid_files_are_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->adminPpid()->create();
        $profile = LeadershipProfile::query()->where('position_key', 'director')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.leadership-profiles.update', $profile), [
            'name' => $profile->name,
            'position' => $profile->position,
            'sort_order' => $profile->sort_order,
            'is_published' => '1',
            'photo' => UploadedFile::fake()->create('foto.php', 10, 'text/x-php'),
        ])->assertSessionHasErrors('photo');

        $this->actingAs($admin)->put(route('admin.leadership-profiles.update', $profile), [
            'name' => $profile->name,
            'position' => $profile->position,
            'sort_order' => $profile->sort_order,
            'is_published' => '1',
            'is_featured' => '1',
            'photo' => UploadedFile::fake()->createWithContent('foto-pimpinan.png', 'png-foto-unik')->mimeType('image/png'),
        ])->assertRedirect();

        $profile->refresh();
        $this->assertNotNull($profile->photo_media_asset_id);
        $this->get('/halaman/profil-pimpinan-satuan-kerja')
            ->assertOk()
            ->assertSee('/media/'.$profile->photo->uuid, false);
    }
}
