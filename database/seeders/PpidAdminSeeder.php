<?php

namespace Database\Seeders;

use App\Services\PpidAdminService;
use Illuminate\Database\Seeder;
use RuntimeException;

class PpidAdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = app(PpidAdminService::class);
        $ensured = 0;

        foreach ($admins->slots() as $slot) {
            if ($slot['partial']) {
                throw new RuntimeException(
                    'Protected Admin #'.$slot['index'].': env tidak lengkap. Isi email, username, dan password.'
                );
            }

            if (! $slot['complete']) {
                continue;
            }

            $result = $admins->ensureSlot($slot);

            if ($result['status'] === 'FAIL') {
                throw new RuntimeException(
                    'Protected Admin #'.$slot['index'].': '.$result['detail']
                );
            }

            $ensured++;
        }

        if ($ensured === 0) {
            if ($this->command) {
                $this->command->warn('PpidAdminSeeder dilewati: set PPID_ADMIN_EMAIL, PPID_ADMIN_USERNAME, dan PPID_ADMIN_PASSWORD di .env.');
            }

            return;
        }

        if ($this->command) {
            $this->command->info($ensured.' Admin PPID terlindungi siap (password tidak ditampilkan).');
        }
    }
}
