<?php

namespace App\Http\Controllers;

use App\Models\DocumentCategory;
use App\Models\DownloadLog;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = PublicDocument::query()
            ->published()
            ->with(['documentCategory', 'informationCategory'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($inner) => $inner->where('title', 'like', $term)->orWhere('description', 'like', $term));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('document_category_id', $request->integer('category')))
            ->when($request->filled('info'), fn ($q) => $q->where('public_information_category_id', $request->integer('info')))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->integer('year')))
            ->when(
                $request->string('sort')->toString() === 'oldest',
                fn ($q) => $q->orderBy('published_at')->orderBy('id'),
                fn ($q) => $q->latest('published_at')->latest('id'),
            )
            ->paginate(12)
            ->withQueryString();

        $years = PublicDocument::query()->published()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('pages.documents.index', [
            'documents' => $documents,
            'documentCategories' => DocumentCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'informationCategories' => PublicInformationCategory::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'years' => $years,
        ]);
    }

    public function show(string $slug): View
    {
        $document = PublicDocument::query()
            ->published()
            ->with(['documentCategory', 'informationCategory'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.documents.show', compact('document'));
    }

    public function download(Request $request, string $slug): StreamedResponse|RedirectResponse
    {
        $document = PublicDocument::query()
            ->with('informationCategory')
            ->where('slug', $slug)
            ->firstOrFail();

        if (! $document->isPubliclyDownloadable()) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        if ($document->is_external && filled($document->external_url)) {
            $this->logDownload($request, $document);
            $document->increment('download_count');

            return redirect()->away($document->external_url);
        }

        if (! $document->file_path || ! Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        // Cegah path traversal: hanya path relatif di disk public
        $path = $document->file_path;
        if (str_contains($path, '..') || str_starts_with($path, '/') || str_contains($path, '\\..')) {
            abort(403);
        }

        $this->logDownload($request, $document);
        $document->increment('download_count');

        $downloadName = $document->file_name ?: basename($path);

        return Storage::disk('public')->download($path, $downloadName);
    }

    private function logDownload(Request $request, PublicDocument $document): void
    {
        DownloadLog::query()->create([
            'public_document_id' => $document->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);
    }
}
