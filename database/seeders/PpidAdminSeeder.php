<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class PpidAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = trim((string) config('ppid.admin.email'));
        $username = trim((string) config('ppid.admin.username'));
        $password = (string) config('ppid.admin.password');
        $name = trim((string) config('ppid.admin.name')) ?: 'Admin PPID';

        if ($email === '' || $username === '' || $password === '') {
            if ($this->command) {
                $this->command->warn('PpidAdminSeeder dilewati: set PPID_ADMIN_EMAIL, PPID_ADMIN_USERNAME, dan PPID_ADMIN_PASSWORD di .env.');
            }

            return;
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new RuntimeException('PPID_ADMIN_EMAIL tidak valid.');
        }

        if (! preg_match('/^[A-Za-z0-9._-]{3,64}$/', $username)) {
            throw new RuntimeException('PPID_ADMIN_USERNAME tidak valid.');
        }

        $byEmail = User::query()->where('email', $email)->first();
        $byUsername = User::query()->where('username', $username)->first();

        if ($byEmail && $byUsername && $byEmail->id !== $byUsername->id) {
            throw new RuntimeException(
                'Konflik akun Admin PPID: email dan username dimiliki user berbeda. Selesaikan secara manual.'
            );
        }

        $user = $byEmail ?? $byUsername;

        $attributes = [
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => UserRole::AdminPpid,
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        if ($user) {
            $user->fill($attributes);
            $user->save();
        } else {
            User::query()->create($attributes);
        }

        if ($this->command) {
            $this->command->info('Admin PPID siap (password tidak ditampilkan).');
        }
    }
}
