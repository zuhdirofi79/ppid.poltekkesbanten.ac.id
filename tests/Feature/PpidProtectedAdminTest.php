<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\PpidAdminService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PpidProtectedAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: User, 1: User, 2: User}
     */
    private function seedProtectedAdmins(): array
    {
        config([
            'ppid.admin.email' => 'core1@example.test',
            'ppid.admin.username' => 'ppid_core_1',
            'ppid.admin.password' => 'CorePass1!Test',
            'ppid.admin.name' => 'Admin Inti 1',
            'ppid.protected_admins' => [
                [
                    'email' => 'core1@example.test',
                    'username' => 'ppid_core_1',
                    'name' => 'Admin Inti 1',
                    'password' => 'CorePass1!Test',
                ],
                [
                    'email' => 'core2@example.test',
                    'username' => 'ppid_core_2',
                    'name' => 'Admin Inti 2',
                    'password' => 'CorePass2!Test',
                ],
                [
                    'email' => 'core3@example.test',
                    'username' => 'ppid_core_3',
                    'name' => 'Admin Inti 3',
                    'password' => 'CorePass3!Test',
                ],
            ],
        ]);

        $this->artisan('ppid:ensure-admin')->assertSuccessful();

        return [
            User::query()->where('email', 'core1@example.test')->firstOrFail(),
            User::query()->where('email', 'core2@example.test')->firstOrFail(),
            User::query()->where('email', 'core3@example.test')->firstOrFail(),
        ];
    }

    private function extraAdmin(): User
    {
        return User::factory()->adminPpid()->create([
            'name' => 'Admin Tambahan',
            'email' => 'extra-ppid@example.test',
            'username' => 'ppid_extra',
            'password' => Hash::make('ExtraPass123!'),
        ]);
    }

    #[Test]
    public function protected_admin_one_can_login_with_email(): void
    {
        [$one] = $this->seedProtectedAdmins();

        $this->post('/admin/login', [
            'login' => 'core1@example.test',
            'password' => 'CorePass1!Test',
        ])->assertRedirect(route('admin.ppid.dashboard'));

        $this->assertAuthenticatedAs($one);
    }

    #[Test]
    public function protected_admin_two_can_login_with_username(): void
    {
        [, $two] = $this->seedProtectedAdmins();

        $this->post('/admin/login', [
            'login' => 'ppid_core_2',
            'password' => 'CorePass2!Test',
        ])->assertRedirect(route('admin.ppid.dashboard'));

        $this->assertAuthenticatedAs($two);
    }

    #[Test]
    public function protected_admin_three_can_login(): void
    {
        [, , $three] = $this->seedProtectedAdmins();

        $this->post('/admin/login', [
            'login' => 'core3@example.test',
            'password' => 'CorePass3!Test',
        ])->assertRedirect(route('admin.ppid.dashboard'));

        $this->assertAuthenticatedAs($three);
    }

    #[Test]
    public function protected_admin_can_create_additional_admin(): void
    {
        [$one] = $this->seedProtectedAdmins();

        $this->actingAs($one)
            ->post(route('admin.ppid.admins.store'), [
                'name' => 'Admin Baru',
                'username' => 'ppid_baru',
                'email' => 'baru@example.test',
                'password' => 'BaruPass123!',
                'password_confirmation' => 'BaruPass123!',
            ])
            ->assertRedirect(route('admin.ppid.admins.index'));

        $created = User::query()->where('email', 'baru@example.test')->first();
        $this->assertNotNull($created);
        $this->assertSame(UserRole::AdminPpid, $created->role);
        $this->assertFalse(app(PpidAdminService::class)->isProtectedAdmin($created));
        $this->assertTrue(Hash::check('BaruPass123!', $created->password));
    }

    #[Test]
    public function additional_admin_cannot_create_admin(): void
    {
        $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();

        $this->actingAs($extra)
            ->post(route('admin.ppid.admins.store'), [
                'name' => 'Tidak Boleh',
                'username' => 'tidak_boleh',
                'email' => 'tidak@example.test',
                'password' => 'BaruPass123!',
                'password_confirmation' => 'BaruPass123!',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'tidak@example.test']);
    }

    #[Test]
    public function additional_admin_cannot_open_admin_management(): void
    {
        $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();

        $this->actingAs($extra)
            ->get(route('admin.ppid.admins.index'))
            ->assertForbidden();
    }

    #[Test]
    public function protected_admins_cannot_delete_each_other_or_themselves(): void
    {
        [$one, $two, $three] = $this->seedProtectedAdmins();

        $this->actingAs($one)->delete(route('admin.ppid.admins.destroy', $two))->assertForbidden();
        $this->actingAs($one)->delete(route('admin.ppid.admins.destroy', $three))->assertForbidden();
        $this->actingAs($two)->delete(route('admin.ppid.admins.destroy', $one))->assertForbidden();
        $this->actingAs($three)->delete(route('admin.ppid.admins.destroy', $one))->assertForbidden();
        $this->actingAs($one)->delete(route('admin.ppid.admins.destroy', $one))->assertForbidden();

        $this->assertDatabaseHas('users', ['email' => 'core1@example.test']);
        $this->assertDatabaseHas('users', ['email' => 'core2@example.test']);
        $this->assertDatabaseHas('users', ['email' => 'core3@example.test']);
    }

    #[Test]
    public function protected_admin_can_delete_additional_admin(): void
    {
        [$one] = $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();

        $this->actingAs($one)
            ->delete(route('admin.ppid.admins.destroy', $extra))
            ->assertRedirect(route('admin.ppid.admins.index'));

        $this->assertDatabaseMissing('users', ['email' => 'extra-ppid@example.test']);
    }

    #[Test]
    public function additional_admin_cannot_delete_anyone(): void
    {
        [$one] = $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();
        $other = User::factory()->adminPpid()->create([
            'email' => 'other-extra@example.test',
            'username' => 'ppid_other',
        ]);

        $this->actingAs($extra)->delete(route('admin.ppid.admins.destroy', $other))->assertForbidden();
        $this->actingAs($extra)->delete(route('admin.ppid.admins.destroy', $one))->assertForbidden();
        $this->assertDatabaseHas('users', ['email' => 'other-extra@example.test']);
    }

    #[Test]
    public function direct_http_delete_protected_returns_403(): void
    {
        [$one, $two] = $this->seedProtectedAdmins();

        $this->actingAs($one)
            ->delete('/admin/ppid/admins/'.$two->id)
            ->assertForbidden();
    }

    #[Test]
    public function bulk_delete_skips_protected_admins(): void
    {
        [$one, $two] = $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();

        $this->actingAs($one)
            ->post(route('admin.ppid.admins.bulk-destroy'), [
                'ids' => [$two->id, $extra->id],
            ])
            ->assertRedirect(route('admin.ppid.admins.index'));

        $this->assertDatabaseHas('users', ['email' => 'core2@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'extra-ppid@example.test']);
    }

    #[Test]
    public function bulk_delete_only_protected_returns_403(): void
    {
        [$one, $two] = $this->seedProtectedAdmins();

        $this->actingAs($one)
            ->post(route('admin.ppid.admins.bulk-destroy'), [
                'ids' => [$two->id],
            ])
            ->assertForbidden();
    }

    #[Test]
    public function protected_role_cannot_be_revoked(): void
    {
        [$one, $two] = $this->seedProtectedAdmins();
        $super = User::factory()->superAdmin()->create();

        $this->actingAs($one)
            ->put(route('admin.ppid.admins.update', $two), [
                'name' => $two->name,
                'username' => $two->username,
                'email' => $two->email,
                'role' => UserRole::Editor->value,
            ])
            ->assertForbidden();

        $this->actingAs($super)
            ->put(route('admin.users.update', $two), [
                'name' => $two->name,
                'email' => $two->email,
                'username' => $two->username,
                'role' => UserRole::Editor->value,
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->assertSame(UserRole::AdminPpid, $two->fresh()->role);
        $this->assertTrue($two->fresh()->is_active);
    }

    #[Test]
    public function additional_admin_cannot_disable_protected_admin(): void
    {
        [$one] = $this->seedProtectedAdmins();
        $extra = $this->extraAdmin();

        $this->actingAs($extra)
            ->patch(route('admin.ppid.admins.disable', $one))
            ->assertForbidden();

        $this->assertTrue($one->fresh()->is_active);
    }

    #[Test]
    public function verify_admin_command_does_not_print_password(): void
    {
        $this->seedProtectedAdmins();

        $this->artisan('ppid:verify-admin')
            ->expectsOutputToContain('Admin 1 account')
            ->expectsOutputToContain('Protection rules')
            ->doesntExpectOutputToContain('CorePass1!Test')
            ->doesntExpectOutputToContain('CorePass2!Test')
            ->assertSuccessful();
    }
}
