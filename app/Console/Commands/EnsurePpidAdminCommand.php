<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsurePpidAdminCommand extends Command
{
    protected $signature = 'ppid:ensure-admin';

    protected $description = 'Ensure Admin PPID account exists with email/username/role/password from environment (never prints password)';

    public function handle(): int
    {
        $email = trim((string) config('ppid.admin.email'));
        $username = trim((string) config('ppid.admin.username'));
        $password = (string) config('ppid.admin.password');
        $name = trim((string) config('ppid.admin.name')) ?: 'Admin PPID';

        if ($email === '' || $username === '' || $password === '') {
            $this->error('FAIL: PPID_ADMIN_EMAIL / PPID_ADMIN_USERNAME / PPID_ADMIN_PASSWORD belum diisi di .env');

            return self::FAILURE;
        }

        $byEmail = User::query()->where('email', $email)->first();
        $byUsername = User::query()->where('username', $username)->first();

        if ($byEmail && $byUsername && $byEmail->id !== $byUsername->id) {
            $this->error('FAIL: email dan username dimiliki user berbeda — selesaikan konflik manual');

            return self::FAILURE;
        }

        $user = $byEmail ?? $byUsername;
        $accountStatus = 'PASS';
        $emailStatus = 'PASS';
        $usernameStatus = 'PASS';
        $roleStatus = 'PASS';
        $passwordStatus = 'PASS';

        if (! $user) {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => UserRole::AdminPpid,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $accountStatus = 'UPDATED';
            $passwordStatus = 'UPDATED';
        } else {
            $dirty = false;

            if ($user->email !== $email) {
                $user->email = $email;
                $emailStatus = 'UPDATED';
                $dirty = true;
            }

            if ($user->username !== $username) {
                $user->username = $username;
                $usernameStatus = 'UPDATED';
                $dirty = true;
            }

            if ($user->role !== UserRole::AdminPpid) {
                $user->role = UserRole::AdminPpid;
                $roleStatus = 'UPDATED';
                $dirty = true;
            }

            if (! $user->is_active) {
                $user->is_active = true;
                $dirty = true;
            }

            if (! Hash::check($password, $user->password)) {
                $user->password = Hash::make($password);
                $passwordStatus = 'UPDATED';
                $dirty = true;
            }

            if ($dirty) {
                $user->save();
                $accountStatus = 'UPDATED';
            }
        }

        $this->line('Admin PPID account ........ '.$accountStatus);
        $this->line('Email ..................... '.$emailStatus);
        $this->line('Username .................. '.$usernameStatus);
        $this->line('Role ...................... '.$roleStatus);
        $this->line('Password .................. '.$passwordStatus);

        return self::SUCCESS;
    }
}
