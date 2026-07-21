<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'qowiyrofi@poltekkesbanten.ac.id');
        $password = env('SUPER_ADMIN_PASSWORD', 'qtester1234567890');

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin PPID',
                'password' => Hash::make($password),
                'role' => UserRole::SuperAdmin,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        // Nonaktifkan akun demo lama jika masih ada
        User::query()
            ->where('email', 'admin@ppid.local')
            ->where('email', '!=', $email)
            ->update(['is_active' => false]);

        User::query()->updateOrCreate(
            ['email' => 'editor@ppid.local'],
            [
                'name' => 'Editor CONTOH',
                'password' => Hash::make('password'),
                'role' => UserRole::Editor,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'operator@ppid.local'],
            [
                'name' => 'Operator CONTOH',
                'password' => Hash::make('password'),
                'role' => UserRole::Operator,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
