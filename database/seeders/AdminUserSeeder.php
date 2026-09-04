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
        $email = trim((string) env('SUPER_ADMIN_EMAIL', ''));
        $password = (string) env('SUPER_ADMIN_PASSWORD', '');
        $ppidEmail = trim((string) config('ppid.admin.email'));

        // Jangan bentrok dengan akun Admin PPID yang dikelola PpidAdminSeeder / ppid:ensure-admin.
        if ($email !== '' && $password !== '' && strcasecmp($email, $ppidEmail) !== 0) {
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
        }

        User::query()
            ->where('email', 'admin@ppid.local')
            ->update(['is_active' => false]);

        if (! app()->environment('production')) {
            User::query()->updateOrCreate(
                ['email' => 'editor@ppid.local'],
                [
                    'name' => 'Editor CONTOH',
                    'username' => 'editor_demo',
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
                    'username' => 'operator_demo',
                    'password' => Hash::make('password'),
                    'role' => UserRole::Operator,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
