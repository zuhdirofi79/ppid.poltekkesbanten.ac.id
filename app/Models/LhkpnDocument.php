<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LhkpnDocument extends Model
{
    public const SOURCE_UPLOADED = 'uploaded_file';

    public const SOURCE_PUBLIC_ASSET = 'public_asset';

    public const SOURCE_EXTERNAL = 'external_url';

    public const GROUP_PIMPINAN = 'pimpinan';

    public const GROUP_TERKAIT = 'terkait';

    public const PAGE_SLUG = 'lhkpn-pimpinan-tertinggi';

    protected $fillable = [
        'uuid',
        'position_key',
        'person_name',
        'position',
        'group_key',
        'report_year',
        'document_label',
        'notes',
        'source_type',
        'media_asset_id',
        'file_path',
        'public_asset_path',
        'external_url',
        'is_published',
        'sort_order',
        'created_by',
        'updated_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'report_year' => 'integer',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LhkpnDocument $document): void {
            $document->uuid ??= (string) Str::uuid();
        });
    }

    public function mediaAsset(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function isAvailable(): bool
    {
        return $this->hasUploadedFile()
            || $this->hasPublicAsset()
            || $this->hasSafeExternalUrl();
    }

    public function hasUploadedFile(): bool
    {
        if ($this->mediaAssetIsReadable()) {
            return true;
        }

        return filled($this->file_path) && Storage::disk('public')->exists($this->file_path);
    }

    public function hasPublicAsset(): bool
    {
        $path = trim((string) $this->public_asset_path);

        return $path !== ''
            && ! str_contains($path, '..')
            && ! str_starts_with($path, '/')
            && ! str_contains($path, '\\')
            && str_ends_with(strtolower($path), '.pdf');
    }

    public function hasSafeExternalUrl(): bool
    {
        return self::isSafeHttpUrl($this->external_url);
    }

    public static function isSafeHttpUrl(?string $url): bool
    {
        $url = trim((string) $url);
        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    public function documentUrl(): ?string
    {
        $asset = $this->readableMediaAsset();
        if ($asset) {
            return $asset->publicUrl();
        }

        if (filled($this->file_path) && Storage::disk('public')->exists($this->file_path)) {
            return asset('storage/'.$this->file_path);
        }

        if ($this->hasPublicAsset()) {
            return asset(self::encodePublicPath((string) $this->public_asset_path));
        }

        if ($this->hasSafeExternalUrl()) {
            return $this->external_url;
        }

        return null;
    }

    public static function encodePublicPath(string $path): string
    {
        return implode('/', array_map(
            rawurlencode(...),
            explode('/', str_replace('\\', '/', $path)),
        ));
    }

    public function statusLabel(): string
    {
        return $this->isAvailable() ? 'Tersedia' : 'Dokumen belum tersedia';
    }

    public function availabilitySentence(): string
    {
        if (! $this->isAvailable()) {
            return 'Dokumen LHKPN belum tersedia pada portal PPID.';
        }

        if ($this->report_year) {
            return 'Periode '.$this->report_year;
        }

        return 'Dokumen LHKPN tersedia';
    }

    public function typeLabel(): string
    {
        $label = trim((string) $this->document_label);

        return $label !== '' ? $label : 'PDF / Dokumen LHKPN';
    }

    public function downloadFilename(): string
    {
        $slug = Str::slug($this->person_name) ?: 'dokumen';

        return 'LHKPN-'.$slug.'.pdf';
    }

    public function anchor(): string
    {
        return 'lhkpn-'.$this->position_key;
    }

    public function requiresUpload(): bool
    {
        return $this->is_published && ! $this->isAvailable();
    }

    private function mediaAssetIsReadable(): bool
    {
        return $this->readableMediaAsset() !== null;
    }

    private function readableMediaAsset(): ?MediaAsset
    {
        if (! $this->media_asset_id) {
            return null;
        }

        $asset = $this->relationLoaded('mediaAsset') ? $this->mediaAsset : $this->mediaAsset()->first();

        if (
            ! $asset
            || $asset->visibility !== 'public'
            || $asset->disk !== 'public'
            || $asset->extension !== 'pdf'
            || ! Storage::disk('public')->exists($asset->path)
        ) {
            return null;
        }

        return $asset;
    }
};
