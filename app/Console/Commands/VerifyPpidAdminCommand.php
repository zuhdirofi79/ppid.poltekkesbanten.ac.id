<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Services\PpidAdminService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class VerifyPpidAdminCommand extends Command
{
    protected $signature = 'ppid:verify-admin';

    protected $description = 'Verify protected Admin PPID accounts and protection rules without printing the password';

    public function handle(PpidAdminService $admins, UserPolicy $policy): int
    {
        $this->info('Protected PPID Admins');
        $this->line('---------------------');

        $failures = 0;
        $protectedUsers = [];

        foreach ($admins->slots() as $slot) {
            $n = $slot['index'];

            if ($slot['partial']) {
                $this->line(sprintf('Admin %d config ......... FAIL', $n));
                $failures++;

                continue;
            }

            if (! $slot['complete']) {
                $this->line(sprintf('Admin %d account ........ SKIP', $n));
                $this->line(sprintf('Admin %d role ........... SKIP', $n));

                continue;
            }

            $user = $admins->findBySlot($slot);
            $accountOk = $user !== null;
            $this->line(sprintf('Admin %d account ........ %s', $n, $accountOk ? 'PASS' : 'FAIL'));

            if (! $accountOk) {
                $failures++;
                $this->line(sprintf('Admin %d role ........... FAIL', $n));
                $failures++;

                continue;
            }

            $protectedUsers[] = $user;

            $roleOk = $user->role === UserRole::AdminPpid && $user->is_active;
            $this->line(sprintf('Admin %d role ........... %s', $n, $roleOk ? 'PASS' : 'FAIL'));
            if (! $roleOk) {
                $failures++;
            }

            $identityOk = strcasecmp((string) $user->email, $slot['email']) === 0
                && (string) $user->username === $slot['username'];
            if (! $identityOk) {
                $this->line(sprintf('Admin %d identity ....... FAIL', $n));
                $failures++;
            }

            $passwordOk = Hash::check($slot['password'], $user->password);
            $this->line(sprintf('Admin %d password ....... %s', $n, $passwordOk ? 'VALID' : 'FAIL'));
            if (! $passwordOk) {
                $failures++;
            }
        }

        $protectionOk = $this->protectionHolds($policy, $admins, $protectedUsers);
        $this->line('Protection rules ....... '.($protectionOk ? 'PASS' : 'FAIL'));
        if (! $protectionOk) {
            $failures++;
        }

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @param  list<User>  $protectedUsers
     */
    private function protectionHolds(UserPolicy $policy, PpidAdminService $admins, array $protectedUsers): bool
    {
        if ($protectedUsers === []) {
            return true;
        }

        foreach ($protectedUsers as $actor) {
            if (! $admins->isProtectedAdmin($actor)) {
                return false;
            }

            if (! $admins->canManagePpidAdmins($actor)) {
                return false;
            }

            foreach ($protectedUsers as $target) {
                if ($policy->deleteAdmin($actor, $target) !== false) {
                    return false;
                }

                if ($policy->disableAdmin($actor, $target) !== false) {
                    return false;
                }

                if ($policy->removeRole($actor, $target) !== false) {
                    return false;
                }

                if ($policy->updateAdmin($actor, $target) !== false) {
                    return false;
                }
            }
        }

        return true;
    }
}
