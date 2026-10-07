<?php

namespace App\Http\Controllers;

use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Support\DokumenPengadaanBarangJasa;
use App\Support\InformasiDikecualikan;
use App\Support\InventarisBarangMilikNegara;
use App\Support\StatistikKepegawaian;
use Illuminate\Http\Request;
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

    public function category(Request $request, string $slug): View
    {
        $category = PublicInformationCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($slug === 'berkala' || $slug === 'serta-merta') {
            $documents = $slug === 'berkala'
                ? PublicDocument::query()->inBerkalaCatalog()->catalogOrdered()->get()
                : PublicDocument::query()
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

        $excludedInformation = $slug === InformasiDikecualikan::SLUG
            ? InformasiDikecualikan::presentation($request->query('tahun'))
            : null;

        $procurementAccess = $slug === 'setiap-saat'
            ? DokumenPengadaanBarangJasa::presentation()
            : null;

        $bmnInventory = $slug === 'setiap-saat'
            ? InventarisBarangMilikNegara::presentation()
            : null;

        $staffingChartGroups = $slug === 'setiap-saat'
            ? StatistikKepegawaian::groupsFor($category)
            : collect();

        $documents = PublicDocument::query()
            ->published()
            ->where('public_information_category_id', $category->id)
            ->when($slug === InformasiDikecualikan::SLUG, fn ($query) => $query->whereKey([]))
            ->when($slug === 'setiap-saat', function ($query) {
                $query->where(function ($inner) {
                    $inner->whereNull('section_title')
                        ->orWhereNotIn('section_title', [
                            StatistikKepegawaian::SECTION,
                            DokumenPengadaanBarangJasa::SECTION,
                        ]);
                });
            })
            ->latest('published_at')
            ->paginate(12);

        $pageTitle = $slug === InformasiDikecualikan::SLUG ? InformasiDikecualikan::TITLE : $category->name;
        $pageExcerpt = $slug === InformasiDikecualikan::SLUG ? InformasiDikecualikan::EXCERPT : $category->excerpt;

        return view('pages.information.category', compact(
            'category',
            'documents',
            'staffingChartGroups',
            'procurementAccess',
            'bmnInventory',
            'excludedInformation',
            'pageTitle',
            'pageExcerpt',
        ));
    }
}
