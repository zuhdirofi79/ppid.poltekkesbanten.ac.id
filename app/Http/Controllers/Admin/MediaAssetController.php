<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMediaAssetRequest;
use App\Models\ActivityLog;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Services\MediaLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaAssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = MediaAsset::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('title', 'like', $term)
                        ->orWhere('original_name', 'like', $term);
                });
            })
            ->when($request->filled('type'), fn ($query) => $query->where('extension', $request->string('type')))
            ->when($request->filled('year'), fn ($query) => $query->whereYear('created_at', (int) $request->input('year')))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        $pages = Page::query()->orderBy('title')->get(['id', 'title']);

        return view('admin.media.index', compact('assets', 'pages'));
    }

    public function picker(): JsonResponse
    {
        $items = MediaAsset::query()
            ->where('visibility', 'public')
            ->latest()
            ->limit(80)
            ->get()
            ->map(fn (MediaAsset $asset) => [
                'title' => $asset->title ?: $asset->original_name,
                'url' => $asset->publicUrl(),
                'extension' => $asset->extension,
                'image' => $asset->isImage(),
            ]);

        return response()->json($items);
    }

    public function store(StoreMediaAssetRequest $request, MediaLibrary $library): RedirectResponse
    {
        $asset = $library->store($request->file('file'), $request->validated(), $request->user()->id);
        ActivityLog::record('media_uploaded', $asset, 'Mengunggah media #'.$asset->id, [
            'visibility' => $asset->visibility,
            'mime_type' => $asset->mime_type,
        ]);

        return redirect()->route('admin.media.index')->with('status', 'Berkas masuk ke pustaka media.');
    }

    public function replace(StoreMediaAssetRequest $request, MediaAsset $mediaAsset, MediaLibrary $library): RedirectResponse
    {
        $library->replace($mediaAsset, $request->file('file'));
        ActivityLog::record('media_replaced', $mediaAsset, 'Mengganti berkas media #'.$mediaAsset->id);

        return back()->with('status', 'Berkas media diganti.');
    }

    public function update(Request $request, MediaAsset $mediaAsset): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,internal'],
        ]);

        $mediaAsset->update($data);

        return back()->with('status', 'Metadata media disimpan.');
    }

    public function destroy(MediaAsset $mediaAsset): RedirectResponse
    {
        $used = $mediaAsset->attachments()->count()
            + $mediaAsset->evidences()->count()
            + \App\Models\LeadershipProfile::query()->where('photo_media_asset_id', $mediaAsset->id)->count();
        if ($used > 0) {
            return back()->withErrors([
                'file' => 'Berkas masih dipakai oleh '.$used.' halaman atau indikator. Lepas relasinya dahulu.',
            ]);
        }

        Storage::disk($mediaAsset->disk)->delete($mediaAsset->path);
        ActivityLog::record('media_deleted', $mediaAsset, 'Menghapus media #'.$mediaAsset->id);
        $mediaAsset->delete();

        return redirect()->route('admin.media.index')->with('status', 'Berkas dihapus.');
    }

    public function download(MediaAsset $mediaAsset): StreamedResponse
    {
        abort_unless(Storage::disk($mediaAsset->disk)->exists($mediaAsset->path), 404);

        return $this->stream($mediaAsset);
    }

    private function stream(MediaAsset $mediaAsset): StreamedResponse
    {
        $filename = str_replace(['"', "\r", "\n", '\\', '/'], '', $mediaAsset->original_name);
        if ($filename === '') {
            $filename = 'berkas.'.$mediaAsset->extension;
        }

        $disposition = $mediaAsset->isImage() || $mediaAsset->extension === 'pdf' ? 'inline' : 'attachment';

        return Storage::disk($mediaAsset->disk)->response($mediaAsset->path, $filename, [
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
        ]);
    }
}
