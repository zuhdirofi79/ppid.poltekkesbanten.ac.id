<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\View\View;

class PpidDashboardController extends Controller
{
    public function __invoke(): View
    {
        $category = PublicInformationCategory::query()->where('slug', 'berkala')->first();

        $documents = $category
            ? PublicDocument::query()
                ->where('public_information_category_id', $category->id)
                ->catalogOrdered()
                ->get()
            : collect();

        $missing = $documents->filter(fn (PublicDocument $doc) => ! $doc->isAvailable())->values();
        $available = $documents->filter(fn (PublicDocument $doc) => $doc->isAvailable())->values();

        $stats = [
            'total' => $documents->count(),
            'available' => $available->count(),
            'missing' => $missing->count(),
            'required' => $missing->count(),
        ];

        return view('admin.ppid.dashboard', compact('stats', 'missing', 'available', 'category'));
    }
}
