<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaFileController extends Controller
{
    public function show(string $uuid): StreamedResponse
    {
        $asset = MediaAsset::query()->where('uuid', $uuid)->first();

        if (
            ! $asset
            || $asset->visibility !== 'public'
            || $asset->disk !== 'public'
            || ! Storage::disk('public')->exists($asset->path)
        ) {
            abort(404);
        }

        $filename = str_replace(['"', "\r", "\n", '\\', '/'], '', $asset->original_name);
        if ($filename === '') {
            $filename = 'berkas.'.$asset->extension;
        }

        $disposition = $asset->isImage() || $asset->extension === 'pdf' ? 'inline' : 'attachment';

        return Storage::disk('public')->response($asset->path, $filename, [
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
