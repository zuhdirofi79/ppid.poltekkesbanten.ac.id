<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentCategory;
use App\Models\Page;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = PublicDocument::query()
            ->with(['documentCategory', 'informationCategory'])
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->integer('year')))
            ->when($request->filled('missing'), function ($q) {
                $q->where(function ($inner) {
                    $inner->where(function ($empty) {
                        $empty->whereNull('file_path')->orWhere('file_path', '');
                    })->where(function ($empty) {
                        $empty->whereNull('external_url')->orWhere('external_url', '');
                    })->where(function ($empty) {
                        $empty->whereNull('internal_url')->orWhere('internal_url', '');
                    });
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $missingCount = PublicDocument::query()
            ->whereHas('informationCategory', fn ($q) => $q->where('slug', 'berkala'))
            ->get()
            ->filter(fn (PublicDocument $doc) => ! $doc->isAvailable())
            ->count();

        return view('admin.documents.index', compact('documents', 'missingCount'));
    }

    public function create(): View
    {
        return view('admin.documents.create', [
            'document' => new PublicDocument([
                'status' => 'draft',
                'is_external' => false,
                'source_type' => PublicDocument::SOURCE_UPLOADED,
            ]),
            'documentCategories' => DocumentCategory::query()->orderBy('name')->get(),
            'informationCategories' => PublicInformationCategory::query()->orderBy('sort_order')->get(),
            'pages' => Page::query()->published()->orderBy('title')->get(['id', 'title', 'slug']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], PublicDocument::class);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data = $this->handleUpload($request, $data);

        $document = PublicDocument::query()->create($data);
        $document->syncSourceFlags();
        $document->save();

        $action = $document->isAvailable() ? 'document_uploaded' : 'created';
        ActivityLog::record($action, $document, 'Membuat dokumen: '.$document->title);

        $message = $document->isAvailable()
            ? 'Dokumen berhasil dilengkapi dan telah tersedia pada portal PPID.'
            : 'Dokumen berhasil dibuat.';

        return $this->redirectAfterSave($document, $message);
    }

    public function edit(PublicDocument $document): View
    {
        return view('admin.documents.edit', [
            'document' => $document,
            'documentCategories' => DocumentCategory::query()->orderBy('name')->get(),
            'informationCategories' => PublicInformationCategory::query()->orderBy('sort_order')->get(),
            'pages' => Page::query()->published()->orderBy('title')->get(['id', 'title', 'slug']),
        ]);
    }

    public function update(Request $request, PublicDocument $document): RedirectResponse
    {
        $data = $this->validated($request, $document);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], PublicDocument::class, $document->id);
        $data['updated_by'] = $request->user()->id;
        $data = $this->handleUpload($request, $data, $document);

        $wasAvailable = $document->isAvailable();
        $hadFile = filled($document->file_path);

        $document->fill($data);
        $document->syncSourceFlags();
        $document->save();

        $action = 'updated';
        if ($request->hasFile('file')) {
            $action = $hadFile ? 'document_replaced' : 'document_uploaded';
        } elseif ($document->hasSafeExternalUrl() || $document->hasInternalUrl()) {
            $action = 'document_url_updated';
        }

        ActivityLog::record($action, $document, 'Memperbarui dokumen: '.$document->title);

        $message = $document->isAvailable() && (! $wasAvailable || $request->hasFile('file') || $action === 'document_url_updated')
            ? 'Dokumen berhasil dilengkapi dan telah tersedia pada portal PPID.'
            : 'Dokumen berhasil diperbarui.';

        return $this->redirectAfterSave($document, $message);
    }

    public function destroy(PublicDocument $document): RedirectResponse
    {
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        ActivityLog::record('deleted', $document, 'Menghapus dokumen: '.$document->title);
        $document->delete();

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil dihapus.');
    }

    public function removeFile(PublicDocument $document): RedirectResponse
    {
        $oldPath = $document->file_path;

        DB::transaction(function () use ($document) {
            $document->file_path = null;
            $document->file_name = null;
            $document->file_type = null;
            $document->file_size = null;

            if (! $document->hasSafeExternalUrl() && ! $document->hasInternalUrl()) {
                $document->source_type = PublicDocument::SOURCE_UPLOADED;
                $document->is_external = false;
            }

            $document->save();
        });

        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            $stillUsed = PublicDocument::query()
                ->where('file_path', $oldPath)
                ->where('id', '!=', $document->id)
                ->exists();

            if (! $stillUsed) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        ActivityLog::record('removed_file', $document, 'Menghapus file dokumen: '.$document->title);

        return back()->with('status', 'File dihapus. Dokumen ditandai belum tersedia bila tidak ada URL/halaman internal.');
    }

    private function redirectAfterSave(PublicDocument $document, string $message): RedirectResponse
    {
        if ($document->informationCategory?->slug === 'berkala'
            || optional(PublicInformationCategory::query()->find($document->public_information_category_id))->slug === 'berkala') {
            return redirect()
                ->route('admin.informasi-publik.berkala')
                ->with('status', $message);
        }

        return redirect()->route('admin.documents.index')->with('status', $message);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?PublicDocument $document = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'document_category_id' => ['nullable', 'exists:document_categories,id'],
            'public_information_category_id' => ['nullable', 'exists:public_information_categories,id'],
            'section_title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'source_type' => ['required', Rule::in([
                PublicDocument::SOURCE_UPLOADED,
                PublicDocument::SOURCE_EXTERNAL,
                PublicDocument::SOURCE_INTERNAL,
            ])],
            'external_url' => ['nullable', 'string', 'max:500'],
            'internal_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'file' => [
                'nullable',
                'file',
                'max:20480',
                'mimes:pdf',
                'mimetypes:application/pdf',
            ],
        ]);

        $source = $data['source_type'];

        if ($source === PublicDocument::SOURCE_EXTERNAL) {
            $url = trim((string) ($data['external_url'] ?? ''));
            if ($url === '' || ! PublicDocument::isSafeHttpUrl($url)) {
                throw ValidationException::withMessages([
                    'external_url' => 'URL eksternal wajib http/https yang valid.',
                ]);
            }
            $data['external_url'] = $url;
            $data['internal_url'] = null;
            $data['is_external'] = true;
        } elseif ($source === PublicDocument::SOURCE_INTERNAL) {
            $path = trim((string) ($data['internal_url'] ?? ''));
            if ($path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//')) {
                throw ValidationException::withMessages([
                    'internal_url' => 'Pilih halaman internal yang valid.',
                ]);
            }
            $slug = trim($path, '/');
            if (str_starts_with($slug, 'halaman/')) {
                $pageSlug = substr($slug, strlen('halaman/'));
                $exists = Page::query()->published()->where('slug', $pageSlug)->exists();
                if (! $exists) {
                    throw ValidationException::withMessages([
                        'internal_url' => 'Halaman internal tidak ditemukan atau belum dipublikasikan.',
                    ]);
                }
            }
            $data['internal_url'] = $path;
            $data['external_url'] = null;
            $data['is_external'] = false;
        } else {
            $data['external_url'] = null;
            $data['internal_url'] = null;
            $data['is_external'] = false;

            $hasFile = $request->hasFile('file') || filled($document?->file_path);
            if (! $hasFile && ($data['status'] ?? '') === 'published') {
                // Missing published docs (katalog) diperbolehkan tanpa file.
            }
        }

        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? $document?->sort_order ?? 0);

        unset($data['file']);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function handleUpload(Request $request, array $data, ?PublicDocument $document = null): array
    {
        if (! $request->hasFile('file')) {
            return $data;
        }

        if (($data['source_type'] ?? '') !== PublicDocument::SOURCE_UPLOADED) {
            return $data;
        }

        $file = $request->file('file');
        $mime = (string) $file->getMimeType();
        if ($mime !== 'application/pdf') {
            throw ValidationException::withMessages([
                'file' => 'Hanya berkas PDF yang diizinkan.',
            ]);
        }

        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'dokumen';
        $safeName = now()->format('Y-m-d').'-'.$base.'-'.Str::lower(Str::random(8)).'.pdf';
        $path = $file->storeAs('ppid/documents/'.date('Y/m'), $safeName, 'public');

        $oldPath = $document?->file_path;

        $data['file_path'] = $path;
        $data['file_name'] = $safeName;
        $data['file_type'] = 'pdf';
        $data['file_size'] = $file->getSize();
        $data['is_external'] = false;
        $data['external_url'] = null;
        $data['internal_url'] = null;
        $data['source_type'] = PublicDocument::SOURCE_UPLOADED;

        if ($oldPath && $oldPath !== $path && Storage::disk('public')->exists($oldPath)) {
            $stillUsed = PublicDocument::query()
                ->where('file_path', $oldPath)
                ->when($document, fn ($q) => $q->where('id', '!=', $document->id))
                ->exists();
            if (! $stillUsed) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        return $data;
    }
}
