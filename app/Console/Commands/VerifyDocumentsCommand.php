<?php

namespace App\Console\Commands;

use App\Services\PpidDocumentNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class VerifyDocumentsCommand extends Command
{
    protected $signature = 'ppid:verify-documents';

    protected $description = 'Verify Informasi Berkala availability counts and admin upload routes';

    public function handle(PpidDocumentNotificationService $notifications): int
    {
        $stats = $notifications->stats();
        $count = $notifications->missingCount();
        $serviceOk = $count === $stats['missing'];
        $routesOk = Route::has('admin.documents.edit')
            && Route::has('admin.informasi-publik.berkala')
            && Route::has('admin.ppid.dashboard');

        $this->info('Informasi Berkala');
        $this->line('-----------------');
        $this->line(sprintf('Total .................... %d', $stats['total']));
        $this->line(sprintf('Tersedia ................. %d', $stats['available']));
        $this->line(sprintf('Belum tersedia ........... %d', $stats['missing']));
        $this->line(sprintf('Notification count ....... %d', $count));
        $this->line('Notification service ..... '.($serviceOk ? 'PASS' : 'FAIL'));
        $this->line('Admin upload routes ...... '.($routesOk ? 'PASS' : 'FAIL'));

        return $serviceOk && $routesOk ? self::SUCCESS : self::FAILURE;
    }
}
