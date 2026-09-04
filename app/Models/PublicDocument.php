<?php

namespace App\Models;

use App\Models\Concerns\HasAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicDocument extends Model
{
    use HasAuthors, SoftDeletes;

    public const SOURCE_UPLOADED = 'uploaded_file';

    public const SOURCE_EXTERNAL = 'external_url';

    public const SOURCE_INTERNAL = 'internal_page';

    protected $fillable = [
        'title', 'slug', 'document_category_id', 'public_information_category_id',
        'section_title', 'description', 'year', 'source_type',
        'file_path', 'external_url', 'internal_url', 'file_name', 'file_type',
        'file_size', 'download_count', 'is_external', 'status', 'sort_order',
        'published_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'file_size' => 'integer',
            'download_count' => 'integer',
            'is_external' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function documentCategory(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function informationCategory(): BelongsTo
    {
        return $this->belongsTo(PublicInformationCategory::class, 'public_information_category_id');
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeCatalogOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function isAvailable(): bool
    {
        return $this->hasUploadedFile()
            || $this->hasSafeExternalUrl()
            || $this->hasInternalUrl();
    }

    public function availabilityStatus(): string
    {
        return $this->isAvailable() ? 'available' : 'missing';
    }

    public function hasUploadedFile(): bool
    {
        return filled($this->file_path);
    }

    public function hasSafeExternalUrl(): bool
    {
        return self::isSafeHttpUrl($this->external_url);
    }

    public function hasInternalUrl(): bool
    {
        $url = trim((string) $this->internal_url);

        return $url !== '' && str_starts_with($url, '/') && ! str_starts_with($url, '//');
    }

    public static function isSafeHttpUrl(?string $url): bool
    {
        $url = trim((string) $url);
        if ($url === '') {
            return false;
        }

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    /**
     * Public href for catalog links. Null when missing (no fake anchors).
     */
    public function publicHref(): ?string
    {
        if ($this->hasInternalUrl()) {
            return url($this->internal_url);
        }

        if ($this->hasSafeExternalUrl()) {
            return $this->external_url;
        }

        if ($this->hasUploadedFile() && Storage::disk('public')->exists($this->file_path)) {
            return route('documents.download', $this->slug);
        }

        return null;
    }

    public function opensInNewTab(): bool
    {
        return $this->hasSafeExternalUrl() && ! $this->hasInternalUrl();
    }

    public function resolveSourceType(): string
    {
        if (filled($this->source_type)) {
            return (string) $this->source_type;
        }

        if ($this->hasInternalUrl()) {
            return self::SOURCE_INTERNAL;
        }

        if ($this->hasSafeExternalUrl() && ! $this->hasUploadedFile()) {
            return self::SOURCE_EXTERNAL;
        }

        if ($this->hasUploadedFile()) {
            return self::SOURCE_UPLOADED;
        }

        return self::SOURCE_UPLOADED;
    }

    public function syncSourceFlags(): void
    {
        $type = $this->resolveSourceType();
        $this->source_type = $type;
        $this->is_external = $type === self::SOURCE_EXTERNAL;
    }

    public static function makeCatalogSlug(string $title): string
    {
        return Str::slug($title);
    }
}
