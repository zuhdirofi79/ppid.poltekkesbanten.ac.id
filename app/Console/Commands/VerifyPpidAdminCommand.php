<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyPpidAdminCommand extends Command
{
    protected $signature = 'ppid:verify-admin';

    protected $description = 'Verify Admin PPID account without printing the password';

    public function handle(): int
    {
        $email = trim((string) config('ppid.admin.email'));
        $username = trim((string) config('ppid.admin.username'));
        $password = (string) config('ppid.admin.password');

        $this->info('Admin PPID Verification');
        $this->line('-----------------------');

        $failures = 0;

        if ($email === '' || $username === '' || $password === '') {
            $this->error('Config ................ FAIL (env incomplete)');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->orWhere('username', $username)->first();

        $accountOk = $user !== null;
        $this->line('Account exists ........ '.($accountOk ? 'PASS' : 'FAIL'));
        if (! $accountOk) {
            return self::FAILURE;
        }

        $emailOk = $user->email === $email;
        $this->line('Email ................ '.($emailOk ? 'PASS' : 'FAIL'));
        if (! $emailOk) {
            $failures++;
        }

        $usernameOk = $user->username === $username;
        $this->line('Username .............. '.($usernameOk ? 'PASS' : 'FAIL'));
        if (! $usernameOk) {
            $failures++;
        }

        $roleOk = $user->role === UserRole::AdminPpid;
        $this->line('Role .................. '.($roleOk ? 'PASS' : 'FAIL'));
        if (! $roleOk) {
            $failures++;
        }

        $passwordOk = Hash::check($password, $user->password);
        $this->line('Password hash ......... '.($passwordOk ? 'VALID' : 'FAIL'));
        if (! $passwordOk) {
            $failures++;
        }

        $activeOk = $user->is_active;
        $this->line('Active ................ '.($activeOk ? 'PASS' : 'FAIL'));
        if (! $activeOk) {
            $failures++;
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }
}
