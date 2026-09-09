<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PpidDocumentNotificationService;
use Illuminate\View\View;

class PpidDashboardController extends Controller
{
    public function __invoke(PpidDocumentNotificationService $notifications): View
    {
        $documents = $notifications->berkalaDocuments();
        $missing = $notifications->missingDocuments();
        $available = $documents->filter(fn ($doc) => $doc->isAvailable())->values();
        $stats = $notifications->stats();
        $stats['required'] = $stats['missing'];

        return view('admin.ppid.dashboard', [
            'stats' => $stats,
            'missing' => $missing,
            'available' => $available,
            'category' => null,
        ]);
    }
}
