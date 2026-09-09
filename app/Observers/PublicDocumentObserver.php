<?php

namespace App\Observers;

use App\Models\PublicDocument;
use App\Services\PpidDocumentNotificationService;

class PublicDocumentObserver
{
    public function saved(PublicDocument $document): void
    {
        app(PpidDocumentNotificationService::class)->forgetCache();
    }

    public function deleted(PublicDocument $document): void
    {
        app(PpidDocumentNotificationService::class)->forgetCache();
    }

    public function restored(PublicDocument $document): void
    {
        app(PpidDocumentNotificationService::class)->forgetCache();
    }
}
