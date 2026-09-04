<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_super_admin_can_login_and_access_dashboard(): void
    {
        $user = User::factory()->superAdmin()->create([
            'email' => 'admin@example.com',
        ]);

        $this->post('/admin/login', [
            'login' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Dashboard', false);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->superAdmin()->inactive()->create([
            'email' => 'inactive@example.com',
        ]);

        $this->post('/admin/login', [
            'login' => 'inactive@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_editor_cannot_manage_users_via_gate(): void
    {
        $editor = User::factory()->editor()->create();

        $this->assertFalse($editor->can('manage-users'));
        $this->assertTrue($editor->can('manage-posts'));
        $this->assertFalse($editor->can('manage-documents'));
    }

    public function test_operator_can_manage_documents_but_not_posts(): void
    {
        $operator = User::factory()->operator()->create();

        $this->assertTrue($operator->can('manage-documents'));
        $this->assertFalse($operator->can('manage-posts'));
    }

    public function test_role_middleware_blocks_missing_ability(): void
    {
        $operator = User::factory()->operator()->create();

        $this->actingAs($operator)
            ->get('/admin')
            ->assertOk();

        // Direct middleware check with ability
        $this->actingAs($operator);

        $middleware = new \App\Http\Middleware\EnsureUserCanAccessAdmin;
        $request = request();

        try {
            $middleware->handle($request, fn () => response('ok'), 'manage-users');
            $this->fail('Expected 403 for manage-users');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_super_admin_role_enum_values(): void
    {
        $this->assertContains(UserRole::SuperAdmin->value, UserRole::values());
        $this->assertTrue(UserRole::SuperAdmin->can('manage-users'));
    }
}
