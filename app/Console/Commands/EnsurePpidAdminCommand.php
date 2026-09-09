<?php

namespace App\Console\Commands;

use App\Services\PpidAdminService;
use Illuminate\Console\Command;

class EnsurePpidAdminCommand extends Command
{
    protected $signature = 'ppid:ensure-admin';

    protected $description = 'Ensure the three protected Admin PPID accounts exist from environment (never prints password)';

    public function handle(PpidAdminService $admins): int
    {
        $slots = $admins->slots();
        $failed = 0;
        $ensured = 0;

        foreach ($slots as $slot) {
            $label = 'Protected Admin #'.$slot['index'];
            $result = $admins->ensureSlot($slot);

            $this->line(sprintf('%-24s %s', $label, $result['status']));

            if ($result['status'] === 'FAIL') {
                $this->error('  '.$result['detail']);
                $failed++;
            }

            if ($result['status'] === 'PASS') {
                $ensured++;
            }
        }

        if ($ensured === 0 && $failed === 0) {
            $this->error('FAIL: PPID_ADMIN_EMAIL / PPID_ADMIN_USERNAME / PPID_ADMIN_PASSWORD belum diisi di .env');

            return self::FAILURE;
        }

        if ($failed > 0) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
