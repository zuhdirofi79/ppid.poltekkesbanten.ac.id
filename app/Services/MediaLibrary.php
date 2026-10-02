<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\MediaAsset;
use App\Models\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MediaLibrary
{
    /**
     * @param  array{title?: ?string, caption?: ?string, alt_text?: ?string, visibility: string, page_id?: ?int}  $meta
     */
    public function store(UploadedFile $file, array $meta, ?int $userId): MediaAsset
    {
        $checksum = hash_file('sha256', (string) $file->getRealPath());
        $existing = MediaAsset::query()->where('checksum', $checksum)->first();
        if ($existing) {
            throw ValidationException::withMessages([
                'file' => 'Berkas yang sama sudah ada di pustaka media.',
            ]);
        }

        $stored = $this->storeUploadedFile($file, $meta['visibility']);

        try {
            return DB::transaction(function () use ($stored, $meta, $userId, $checksum) {
                $asset = MediaAsset::query()->create([
                    'uuid' => (string) Str::uuid(),
                    'original_name' => $stored['original_name'],
                    'stored_name' => $stored['stored_name'],
                    'disk' => $stored['disk'],
                    'path' => $stored['path'],
                    'mime_type' => $stored['mime_type'],
                    'extension' => $stored['extension'],
                    'size' => $stored['size'],
                    'checksum' => $checksum,
                    'title' => $meta['title'] ?? null,
                    'caption' => $meta['caption'] ?? null,
                    'alt_text' => $meta['alt_text'] ?? null,
                    'uploaded_by' => $userId,
                    'visibility' => $meta['visibility'],
                ]);

                $pageId = $meta['page_id'] ?? null;
                if ($pageId) {
                    $page = Page::query()->find($pageId);
                    if ($page) {
                        Attachment::query()->create([
                            'media_asset_id' => $asset->id,
                            'attachable_type' => $page->getMorphClass(),
                            'attachable_id' => $page->id,
                            'display_title' => $asset->title ?: $asset->original_name,
                            'sort_order' => (int) $page->attachments()->count(),
                            'visibility' => $asset->visibility,
                            'created_by' => $userId,
                        ]);
                    }
                }

                return $asset;
            });
        } catch (\Throwable $e) {
            Storage::disk($stored['disk'])->delete($stored['path']);
            throw $e;
        }
    }

    public function replace(MediaAsset $asset, UploadedFile $file): MediaAsset
    {
        $oldDisk = $asset->disk;
        $oldPath = $asset->path;
        $stored = $this->storeUploadedFile($file, $asset->visibility);

        try {
            $this->persist($asset, [
                'original_name' => $stored['original_name'],
                'stored_name' => $stored['stored_name'],
                'disk' => $stored['disk'],
                'path' => $stored['path'],
                'mime_type' => $stored['mime_type'],
                'extension' => $stored['extension'],
                'size' => $stored['size'],
                'checksum' => hash_file('sha256', (string) $file->getRealPath()),
            ]);
        } catch (\Throwable $e) {
            Storage::disk($stored['disk'])->delete($stored['path']);
            throw $e;
        }

        if ($oldPath && ($oldPath !== $asset->path || $oldDisk !== $asset->disk)) {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $asset->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function persist(MediaAsset $asset, array $attributes): void
    {
        DB::transaction(function () use ($asset, $attributes) {
            $asset->update($attributes);
        });
    }

    /**
     * @return array{disk: string, path: string, stored_name: string, original_name: string, mime_type: string, extension: string, size: int}
     */
    protected function storeUploadedFile(UploadedFile $file, string $visibility): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $disk = $visibility === 'public' ? 'public' : 'local';
        $storedName = Str::uuid()->toString().'.'.$extension;
        $directory = 'ppid/media/'.now()->format('Y/m');
        $path = $file->storeAs($directory, $storedName, $disk);

        if (! is_string($path) || $path === '') {
            throw ValidationException::withMessages([
                'file' => 'Berkas gagal disimpan.',
            ]);
        }

        $original = basename(str_replace('\\', '/', $file->getClientOriginalName()));

        return [
            'disk' => $disk,
            'path' => $path,
            'stored_name' => $storedName,
            'original_name' => $original !== '' ? $original : $storedName,
            'mime_type' => (string) $file->getMimeType(),
            'extension' => $extension,
            'size' => (int) $file->getSize(),
        ];
    }
}
