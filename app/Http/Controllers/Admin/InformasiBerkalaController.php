<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\View\View;

class InformasiBerkalaController extends Controller
{
    public function index(): View
    {
        $category = PublicInformationCategory::query()
            ->where('slug', 'berkala')
            ->firstOrFail();

        $documents = PublicDocument::query()
            ->where('public_information_category_id', $category->id)
            ->catalogOrdered()
            ->get();

        $sections = $documents
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
            'documents' => $documents,
            'stats' => [
                'total' => $documents->count(),
                'available' => $availableCount,
                'missing' => $missingCount,
            ],
        ]);
    }
}
