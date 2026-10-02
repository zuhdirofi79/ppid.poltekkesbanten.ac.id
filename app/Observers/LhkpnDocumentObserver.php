<?php

namespace App\Observers;

use App\Models\LhkpnDocument;
use App\Services\PpidDocumentNotificationService;

class LhkpnDocumentObserver
{
    public function saved(LhkpnDocument $document): void
    {
        app(PpidDocumentNotificationService::class)->forgetCache();
    }

    public function deleted(LhkpnDocument $document): void
    {
        app(PpidDocumentNotificationService::class)->forgetCache();
    }
};
