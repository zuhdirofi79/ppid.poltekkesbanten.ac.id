<?php

namespace App\Http\Controllers;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\View\View;

class InformationController extends Controller
{
    public function index(): View
    {
        $categories = PublicInformationCategory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('pages.information.index', compact('categories'));
    }

    public function category(string $slug): View
    {
        $category = PublicInformationCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($slug === 'berkala') {
            $documents = PublicDocument::query()
                ->published()
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

            return view('pages.information.berkala', compact('category', 'sections', 'documents'));
        }

        $documents = PublicDocument::query()
            ->published()
            ->where('public_information_category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        return view('pages.information.category', compact('category', 'documents'));
    }
}
