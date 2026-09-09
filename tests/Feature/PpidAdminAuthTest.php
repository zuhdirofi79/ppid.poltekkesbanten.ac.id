<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PpidAdminAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_login_with_email(): void
    {
        $user = User::factory()->adminPpid()->create([
            'email' => 'ppid-admin@example.test',
            'username' => 'admin_ppid_test',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $response = $this->post('/admin/login', [
            'login' => 'ppid-admin@example.test',
            'password' => 'SecretPass123!',
        ]);

        $response->assertRedirect(route('admin.ppid.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function admin_can_login_with_username(): void
    {
        $user = User::factory()->adminPpid()->create([
            'email' => 'ppid-admin2@example.test',
            'username' => 'admin_ppid_test2',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $this->post('/admin/login', [
            'login' => 'admin_ppid_test2',
            'password' => 'SecretPass123!',
        ])->assertRedirect(route('admin.ppid.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function wrong_password_is_rejected(): void
    {
        User::factory()->adminPpid()->create([
            'email' => 'ppid-admin3@example.test',
            'username' => 'admin_ppid_test3',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $this->post('/admin/login', [
            'login' => 'admin_ppid_test3',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    #[Test]
    public function guest_cannot_open_ppid_dashboard(): void
    {
        $this->get('/admin/ppid')->assertRedirect(route('admin.login'));
    }

    #[Test]
    public function non_admin_ppid_without_documents_ability_gets_403(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)
            ->get('/admin/ppid')
            ->assertForbidden();
    }

    #[Test]
    public function admin_ppid_can_open_dashboard(): void
    {
        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)
            ->get('/admin/ppid')
            ->assertOk()
            ->assertSee('Dashboard Admin PPID', false);
    }

    #[Test]
    public function session_is_regenerated_after_login(): void
    {
        User::factory()->adminPpid()->create([
            'email' => 'regen@example.test',
            'username' => 'regen_admin',
            'password' => Hash::make('SecretPass123!'),
        ]);

        $this->startSession();
        $before = session()->getId();

        $this->post('/admin/login', [
            'login' => 'regen_admin',
            'password' => 'SecretPass123!',
        ])->assertRedirect();

        $this->assertNotSame($before, session()->getId());
    }

    #[Test]
    public function logout_invalidates_session(): void
    {
        $admin = User::factory()->adminPpid()->create();

        $this->actingAs($admin)
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    #[Test]
    public function ensure_admin_command_syncs_from_config_without_printing_password(): void
    {
        config([
            'ppid.admin.email' => 'ensure@example.test',
            'ppid.admin.username' => 'ensure_admin',
            'ppid.admin.password' => 'EnsurePass123!',
            'ppid.admin.name' => 'Ensure Admin',
        ]);

        $this->artisan('ppid:ensure-admin')
            ->expectsOutputToContain('Protected Admin #1')
            ->doesntExpectOutputToContain('EnsurePass123!')
            ->assertSuccessful();

        $user = User::query()->where('email', 'ensure@example.test')->first();
        $this->assertNotNull($user);
        $this->assertSame('ensure_admin', $user->username);
        $this->assertSame(UserRole::AdminPpid, $user->role);
        $this->assertTrue(Hash::check('EnsurePass123!', $user->password));

        $this->artisan('ppid:verify-admin')
            ->expectsOutputToContain('VALID')
            ->doesntExpectOutputToContain('EnsurePass123!')
            ->assertSuccessful();
    }
}
