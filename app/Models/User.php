<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Services\PpidAdminService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'username', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function hasRole(UserRole|string ...$roles): bool
    {
        $values = array_map(
            fn (UserRole|string $role) => $role instanceof UserRole ? $role->value : $role,
            $roles,
        );

        return in_array($this->role?->value, $values, true);
    }

    public function canAccessAdmin(): bool
    {
        return $this->isActive() && $this->role?->can('access-admin') === true;
    }

    public function canDo(string $ability): bool
    {
        return $this->isActive() && $this->role?->can($ability) === true;
    }

    public function isProtectedPpidAdmin(): bool
    {
        return app(PpidAdminService::class)->isProtectedAdmin($this);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $letters = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'A';
    }
}
