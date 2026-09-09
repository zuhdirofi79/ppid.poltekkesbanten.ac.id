<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Services\PpidDocumentNotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InformasiBerkalaController extends Controller
{
    public function index(Request $request, PpidDocumentNotificationService $notifications): View
    {
        $category = PublicInformationCategory::query()
            ->where('slug', 'berkala')
            ->firstOrFail();

        $status = (string) $request->query('status', 'all');
        if (! in_array($status, ['all', 'available', 'missing'], true)) {
            $status = 'all';
        }

        $documents = PublicDocument::query()
            ->where('public_information_category_id', $category->id)
            ->catalogOrdered()
            ->get();

        $filtered = $documents->when($status === 'available', fn ($items) => $items->filter(fn (PublicDocument $doc) => $doc->isAvailable()))
            ->when($status === 'missing', fn ($items) => $items->filter(fn (PublicDocument $doc) => ! $doc->isAvailable()))
            ->values();

        $sections = $filtered
            ->groupBy(fn (PublicDocument $doc) => $doc->section_title ?: 'Lainnya')
            ->map(fn ($items, $title) => [
                'title' => $title,
                'documents' => $items,
            ])
            ->values();

        $missingCount = $documents->filter(fn (PublicDocument $doc) => ! $doc->isAvailable())->count();
        $availableCount = $documents->count() - $missingCount;

        return view('admin.ppid.berkala', [
            'category' => $category,
            'sections' => $sections,
            'documents' => $filtered,
            'status' => $status,
            'notificationMissing' => $notifications->missingCount(),
            'stats' => [
                'total' => $documents->count(),
                'available' => $availableCount,
                'missing' => $missingCount,
            ],
        ]);
    }
}
