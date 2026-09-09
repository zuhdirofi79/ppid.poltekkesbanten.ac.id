<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class PpidAdminService
{
    public const USERNAME_PATTERN = '/^[A-Za-z0-9._-]{3,64}$/';

    /**
     * @return list<array{
     *     index: int,
     *     email: string,
     *     username: string,
     *     name: string,
     *     password: string,
     *     complete: bool,
     *     partial: bool
     * }>
     */
    public function slots(): array
    {
        $raw = config('ppid.protected_admins', []);
        if (! is_array($raw) || $raw === []) {
            $raw = [config('ppid.admin', [])];
        }

        $slots = [];

        foreach (array_values($raw) as $i => $row) {
            $row = is_array($row) ? $row : [];
            $email = strtolower(trim((string) ($row['email'] ?? '')));
            $username = trim((string) ($row['username'] ?? ''));
            $password = (string) ($row['password'] ?? '');
            $name = trim((string) ($row['name'] ?? ''));

            if ($i === 0) {
                $fallback = config('ppid.admin', []);
                if ($email === '') {
                    $email = strtolower(trim((string) ($fallback['email'] ?? '')));
                }
                if ($username === '') {
                    $username = trim((string) ($fallback['username'] ?? ''));
                }
                if ($password === '') {
                    $password = (string) ($fallback['password'] ?? '');
                }
                if ($name === '') {
                    $name = trim((string) ($fallback['name'] ?? ''));
                }
            }

            if ($name === '') {
                $name = $username !== '' ? $username : 'Admin PPID';
            }

            $complete = $email !== '' && $username !== '' && $password !== '';
            $partial = ! $complete && ($email !== '' || $username !== '' || $password !== '');

            $slots[] = [
                'index' => $i + 1,
                'email' => $email,
                'username' => $username,
                'name' => $name,
                'password' => $password,
                'complete' => $complete,
                'partial' => $partial,
            ];
        }

        return $slots;
    }

    /**
     * Identitas terlindungi: email atau username yang terisi di config.
     *
     * @return list<array{email: string, username: string}>
     */
    public function protectedIdentities(): array
    {
        $idents = [];

        foreach ($this->slots() as $slot) {
            if ($slot['email'] === '' && $slot['username'] === '') {
                continue;
            }

            $idents[] = [
                'email' => $slot['email'],
                'username' => $slot['username'],
            ];
        }

        return $idents;
    }

    public function isProtectedAdmin(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $this->isProtectedIdentity($user->email, $user->username);
    }

    public function isProtectedIdentity(?string $email, ?string $username): bool
    {
        $email = strtolower(trim((string) $email));
        $username = trim((string) $username);

        foreach ($this->protectedIdentities() as $ident) {
            if ($email !== '' && $ident['email'] !== '' && strcasecmp($email, $ident['email']) === 0) {
                return true;
            }

            if ($username !== '' && $ident['username'] !== '' && strcasecmp($username, $ident['username']) === 0) {
                return true;
            }
        }

        return false;
    }

    public function canManagePpidAdmins(?User $user): bool
    {
        return $user !== null
            && $user->isActive()
            && $user->hasRole(UserRole::AdminPpid)
            && $this->isProtectedAdmin($user);
    }

    /**
     * @param  array{
     *     index: int,
     *     email: string,
     *     username: string,
     *     name: string,
     *     password: string,
     *     complete: bool,
     *     partial: bool
     * }  $slot
     * @return array{status: string, user: ?User, detail: string}
     */
    public function ensureSlot(array $slot): array
    {
        if ($slot['partial']) {
            return [
                'status' => 'FAIL',
                'user' => null,
                'detail' => 'env tidak lengkap',
            ];
        }

        if (! $slot['complete']) {
            return [
                'status' => 'SKIP',
                'user' => null,
                'detail' => 'env kosong',
            ];
        }

        if (! filter_var($slot['email'], FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => 'FAIL',
                'user' => null,
                'detail' => 'email tidak valid',
            ];
        }

        if (! preg_match(self::USERNAME_PATTERN, $slot['username'])) {
            return [
                'status' => 'FAIL',
                'user' => null,
                'detail' => 'username tidak valid',
            ];
        }

        try {
            $user = $this->syncUser($slot);
        } catch (RuntimeException $e) {
            return [
                'status' => 'FAIL',
                'user' => null,
                'detail' => $e->getMessage(),
            ];
        }

        return [
            'status' => 'PASS',
            'user' => $user,
            'detail' => 'siap',
        ];
    }

    /**
     * @param  array{email: string, username: string, name: string, password: string}  $slot
     */
    public function syncUser(array $slot): User
    {
        $email = strtolower(trim($slot['email']));
        $username = trim($slot['username']);

        $byEmail = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        $byUsername = User::query()->where('username', $username)->first();

        if ($byEmail && $byUsername && $byEmail->id !== $byUsername->id) {
            throw new RuntimeException('email dan username dimiliki user berbeda — selesaikan konflik manual');
        }

        $user = $byEmail ?? $byUsername;

        $attributes = [
            'name' => $slot['name'],
            'email' => $email,
            'username' => $username,
            'role' => UserRole::AdminPpid,
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        if (! $user) {
            $attributes['password'] = Hash::make($slot['password']);

            return User::query()->create($attributes);
        }

        $user->fill($attributes);

        if (! Hash::check($slot['password'], $user->password)) {
            $user->password = Hash::make($slot['password']);
        }

        $user->save();

        return $user->fresh();
    }

    public function findBySlot(array $slot): ?User
    {
        if ($slot['email'] !== '') {
            $byEmail = User::query()->whereRaw('LOWER(email) = ?', [$slot['email']])->first();
            if ($byEmail) {
                return $byEmail;
            }
        }

        if ($slot['username'] !== '') {
            return User::query()->where('username', $slot['username'])->first();
        }

        return null;
    }
}
