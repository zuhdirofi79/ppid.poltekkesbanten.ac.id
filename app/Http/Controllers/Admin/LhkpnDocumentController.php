<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LhkpnDocument;
use App\Models\MediaAsset;
use App\Services\MediaLibrary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LhkpnDocumentController extends Controller
{
    public function index(): View
    {
        $documents = LhkpnDocument::query()->with('mediaAsset')->ordered()->get();

        return view('admin.lhkpn.index', compact('documents'));
    }

    public function create(): View
    {
        $document = new LhkpnDocument([
            'group_key' => LhkpnDocument::GROUP_PIMPINAN,
            'is_published' => false,
            'sort_order' => ((int) LhkpnDocument::query()->max('sort_order')) + 10,
        ]);

        return view('admin.lhkpn.create', compact('document'));
    }

    public function store(Request $request, MediaLibrary $library): RedirectResponse
    {
        $data = $this->validated($request, new LhkpnDocument, true);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        $document = LhkpnDocument::query()->create($data);

        if ($request->hasFile('file')) {
            $this->storePdf($request->file('file'), $document, $library, $request->user()->id);
        }

        ActivityLog::record('created', $document, 'Menambahkan dokumen LHKPN: '.$document->person_name);

        return redirect()
            ->route('admin.lhkpn.index')
            ->with('status', 'Data LHKPN ditambahkan.');
    }

    public function edit(LhkpnDocument $lhkpnDocument): View
    {
        $lhkpnDocument->load('mediaAsset');

        return view('admin.lhkpn.edit', ['document' => $lhkpnDocument]);
    }

    public function update(Request $request, LhkpnDocument $lhkpnDocument, MediaLibrary $library): RedirectResponse
    {
        $data = $this->validated($request, $lhkpnDocument);
        $data['updated_by'] = $request->user()->id;

        if ($data['is_published'] && ! $lhkpnDocument->published_at) {
            $data['published_at'] = now();
        }

        $lhkpnDocument->update($data);

        if ($request->hasFile('file')) {
            $this->storePdf($request->file('file'), $lhkpnDocument, $library, $request->user()?->id);
        }

        ActivityLog::record('updated', $lhkpnDocument, 'Memperbarui dokumen LHKPN: '.$lhkpnDocument->person_name);

        return redirect()
            ->route('admin.lhkpn.index')
            ->with('status', 'Data LHKPN disimpan.');
    }

    public function upload(Request $request, LhkpnDocument $lhkpnDocument, MediaLibrary $library): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:'.$this->maxKilobytes()],
        ]);

        $this->storePdf($request->file('file'), $lhkpnDocument, $library, $request->user()?->id);
        $lhkpnDocument->forceFill(['updated_by' => $request->user()?->id])->save();

        ActivityLog::record('uploaded', $lhkpnDocument, 'Mengunggah dokumen LHKPN: '.$lhkpnDocument->person_name);

        return back()->with('status', 'Dokumen LHKPN berhasil diunggah dan telah tersedia pada portal PPID.');
    }

    public function removeFile(LhkpnDocument $lhkpnDocument): RedirectResponse
    {
        $asset = $lhkpnDocument->mediaAsset;
        $path = $lhkpnDocument->file_path;

        $lhkpnDocument->update([
            'media_asset_id' => null,
            'file_path' => null,
            'source_type' => $lhkpnDocument->hasPublicAsset()
                ? LhkpnDocument::SOURCE_PUBLIC_ASSET
                : ($lhkpnDocument->hasSafeExternalUrl() ? LhkpnDocument::SOURCE_EXTERNAL : null),
            'updated_by' => auth()->id(),
        ]);

        if ($asset) {
            if ($asset->path && Storage::disk($asset->disk)->exists($asset->path)) {
                Storage::disk($asset->disk)->delete($asset->path);
            }
            $asset->delete();
        } elseif ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        ActivityLog::record('removed_file', $lhkpnDocument, 'Menghapus berkas unggahan LHKPN: '.$lhkpnDocument->person_name);

        return back()->with('status', 'Berkas unggahan dihapus. Path dokumen bawaan tetap dipertahankan bila ada.');
    }

    public function publish(LhkpnDocument $lhkpnDocument): RedirectResponse
    {
        $lhkpnDocument->is_published = ! $lhkpnDocument->is_published;
        if ($lhkpnDocument->is_published) {
            $lhkpnDocument->published_at ??= now();
        }
        $lhkpnDocument->updated_by = auth()->id();
        $lhkpnDocument->save();

        return back()->with('status', $lhkpnDocument->is_published
            ? 'Dokumen LHKPN ditayangkan.'
            : 'Dokumen LHKPN disembunyikan dari halaman publik.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, LhkpnDocument $document, bool $creating = false): array
    {
        $rules = [
            'person_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'group_key' => ['required', Rule::in([LhkpnDocument::GROUP_PIMPINAN, LhkpnDocument::GROUP_TERKAIT])],
            'report_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'document_label' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'public_asset_path' => ['nullable', 'string', 'max:255'],
            'external_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'file' => ['nullable', 'file', 'max:'.$this->maxKilobytes()],
        ];

        if ($creating) {
            $rules['position_key'] = ['required', 'string', 'max:64', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('lhkpn_documents', 'position_key')];
        }

        $data = $request->validate($rules);

        foreach (['person_name', 'position', 'document_label', 'notes'] as $field) {
            $value = trim(strip_tags((string) ($data[$field] ?? '')));
            $data[$field] = $value === '' ? null : $value;
        }

        if ($data['person_name'] === null || $data['position'] === null) {
            throw ValidationException::withMessages([
                'person_name' => 'Nama dan jabatan wajib diisi.',
            ]);
        }

        $data['report_year'] = filled($data['report_year'] ?? null) ? (int) $data['report_year'] : null;
        $data['sort_order'] = (int) ($data['sort_order'] ?? $document->sort_order);
        $data['is_published'] = $request->boolean('is_published');
        unset($data['file']);

        if ($creating) {
            $data['position_key'] = strtolower(trim((string) $data['position_key']));
        } else {
            unset($data['position_key']);
        }

        $publicPath = $request->exists('public_asset_path')
            ? $this->normalizedPublicPath($request->input('public_asset_path'))
            : $document->public_asset_path;
        if ($request->exists('public_asset_path')) {
            $data['public_asset_path'] = $publicPath;
        }

        $externalUrl = $request->exists('external_url')
            ? $this->normalizedExternalUrl($request->input('external_url'))
            : $document->external_url;
        if ($request->exists('external_url')) {
            $data['external_url'] = $externalUrl;
        }

        $keepsUpload = $request->hasFile('file')
            || ($document->exists && (filled($document->media_asset_id) || filled($document->file_path)));

        if ($keepsUpload) {
            $data['source_type'] = LhkpnDocument::SOURCE_UPLOADED;
        } elseif (filled($publicPath)) {
            $data['source_type'] = LhkpnDocument::SOURCE_PUBLIC_ASSET;
        } elseif (filled($externalUrl)) {
            $data['source_type'] = LhkpnDocument::SOURCE_EXTERNAL;
        } else {
            $data['source_type'] = null;
        }

        if ($request->hasFile('file')) {
            $this->assertPdf($request->file('file'));
        }

        return $data;
    }

    private function normalizedPublicPath(mixed $path): ?string
    {
        $path = trim(str_replace('\\', '/', (string) $path));
        if ($path === '') {
            return null;
        }

        if (
            str_contains($path, '..')
            || str_starts_with($path, '/')
            || str_contains($path, "\0")
            || ! str_ends_with(strtolower($path), '.pdf')
        ) {
            throw ValidationException::withMessages([
                'public_asset_path' => 'Path dokumen publik harus berupa berkas PDF di dalam folder situs, tanpa tanda .. atau garis miring di awal.',
            ]);
        }

        return $path;
    }

    private function normalizedExternalUrl(mixed $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (! LhkpnDocument::isSafeHttpUrl($url)) {
            throw ValidationException::withMessages([
                'external_url' => 'URL harus berupa tautan HTTP atau HTTPS yang valid.',
            ]);
        }

        return $url;
    }

    private function storePdf(UploadedFile $file, LhkpnDocument $document, MediaLibrary $library, ?int $userId): void
    {
        $this->assertPdf($file);

        $asset = $this->resolveAsset($file, $document, $library, $userId);
        $asset->forceFill([
            'original_name' => $document->downloadFilename(),
            'title' => 'Dokumen LHKPN '.$document->person_name,
            'visibility' => 'public',
        ])->save();

        $document->forceFill([
            'media_asset_id' => $asset->id,
            'file_path' => $asset->path,
            'source_type' => LhkpnDocument::SOURCE_UPLOADED,
        ])->save();
    }

    private function resolveAsset(UploadedFile $file, LhkpnDocument $document, MediaLibrary $library, ?int $userId): MediaAsset
    {
        if ($document->media_asset_id && $document->mediaAsset) {
            return $library->replace($document->mediaAsset, $file);
        }

        try {
            return $library->store($file, [
                'title' => 'Dokumen LHKPN '.$document->person_name,
                'alt_text' => $document->person_name,
                'visibility' => 'public',
            ], $userId);
        } catch (ValidationException $exception) {
            $checksum = hash_file('sha256', (string) $file->getRealPath());
            $existing = MediaAsset::query()
                ->where('checksum', $checksum)
                ->where('extension', 'pdf')
                ->where('visibility', 'public')
                ->first();

            if ($existing) {
                return $existing;
            }

            throw $exception;
        }
    }

    private function assertPdf(UploadedFile $file): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mime = strtolower((string) $file->getMimeType());

        if ($extension !== 'pdf' || $mime !== 'application/pdf') {
            throw ValidationException::withMessages([
                'file' => 'Hanya berkas PDF yang diizinkan.',
            ]);
        }

        $head = (string) file_get_contents($file->getRealPath(), false, null, 0, 1024);
        $lower = strtolower($head);

        if (
            ! str_starts_with($head, '%PDF')
            || str_contains($lower, '<?php')
            || str_contains($lower, '<script')
            || str_contains($lower, '<html')
            || str_contains($lower, '<svg')
        ) {
            throw ValidationException::withMessages([
                'file' => 'Isi berkas PDF tidak valid.',
            ]);
        }
    }

    private function maxKilobytes(): int
    {
        return max(1, (int) config('ppid.max_upload_mb', 20)) * 1024;
    }
};
