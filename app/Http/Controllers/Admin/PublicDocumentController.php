<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocumentCategory;
use App\Models\PublicDocument;
use App\Models\PublicInformationCategory;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $documents = PublicDocument::query()
            ->with(['documentCategory', 'informationCategory'])
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('year'), fn ($q) => $q->where('year', $request->integer('year')))
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    public function create(): View
    {
        return view('admin.documents.create', [
            'document' => new PublicDocument(['status' => 'draft', 'is_external' => false]),
            'documentCategories' => DocumentCategory::query()->orderBy('name')->get(),
            'informationCategories' => PublicInformationCategory::query()->orderBy('sort_order')->get(),
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
        ActivityLog::record('created', $document, 'Membuat dokumen: '.$document->title);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil dibuat.');
    }

    public function edit(PublicDocument $document): View
    {
        return view('admin.documents.edit', [
            'document' => $document,
            'documentCategories' => DocumentCategory::query()->orderBy('name')->get(),
            'informationCategories' => PublicInformationCategory::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, PublicDocument $document): RedirectResponse
    {
        $data = $this->validated($request, $document);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], PublicDocument::class, $document->id);
        $data['updated_by'] = $request->user()->id;
        $data = $this->handleUpload($request, $data, $document);

        $document->update($data);
        ActivityLog::record('updated', $document, 'Memperbarui dokumen: '.$document->title);

        return redirect()->route('admin.documents.index')->with('status', 'Dokumen berhasil diperbarui.');
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

    /** @return array<string, mixed> */
    private function validated(Request $request, ?PublicDocument $document = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'document_category_id' => ['nullable', 'exists:document_categories,id'],
            'public_information_category_id' => ['nullable', 'exists:public_information_categories,id'],
            'description' => ['nullable', 'string'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'external_url' => ['nullable', 'url', 'max:500'],
            'is_external' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'file' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,zip',
            ],
        ]);

        $data['is_external'] = $request->boolean('is_external') || filled($data['external_url'] ?? null);

        if ($data['is_external'] && empty($data['external_url']) && ! $request->hasFile('file') && ! $document?->file_path) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'external_url' => 'Isi tautan eksternal atau unggah berkas.',
            ]);
        }

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

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

        $file = $request->file('file');
        $original = $file->getClientOriginalName();
        $safeName = preg_replace('/[^A-Za-z0-9._-]/', '_', $original) ?: 'document.bin';
        $path = $file->storeAs('documents/'.date('Y/m'), $safeName, 'public');

        if ($document?->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $data['file_path'] = $path;
        $data['file_name'] = $safeName;
        $data['file_type'] = $file->getClientOriginalExtension();
        $data['file_size'] = $file->getSize();
        $data['is_external'] = false;

        return $data;
    }
}
