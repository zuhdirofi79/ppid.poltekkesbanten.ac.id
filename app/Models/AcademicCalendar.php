<?php

namespace App\Models;

use App\Models\Concerns\HasAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AcademicCalendar extends Model
{
    use HasAuthors;

    public const SOURCE_UPLOADED = 'uploaded_file';

    public const SOURCE_EXTERNAL = 'external_url';

    public const SOURCE_PUBLIC_ASSET = 'public_asset';

    /** @var array<string, string> Allowed MIME types and their extensions */
    public const ALLOWED_MIMES = [
        'application/pdf' => 'pdf',
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
    ];

    /** @var array<string> Allowed file extensions */
    public const ALLOWED_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg'];

    protected $fillable = [
        'uuid',
        'year',
        'title',
        'description',
        'source_type',
        'media_asset_id',
        'file_path',
        'public_asset_path',
        'external_url',
        'mime_type',
        'extension',
        'is_published',
        'sort_order',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AcademicCalendar $calendar) {
            if (empty($calendar->uuid)) {
                $calendar->uuid = (string) Str::uuid();
            }
            if (empty($calendar->sort_order)) {
                $calendar->sort_order = $calendar->year;
            }
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

    public function scopeYearOrdered(Builder $query, string $direction = 'desc'): Builder
    {
        return $query->orderBy('year', $direction);
    }

    /**
     * Check if the calendar has an available document.
     */
    public function isAvailable(): bool
    {
        return $this->hasUploadedFile()
            || $this->hasPublicAsset()
            || $this->hasSafeExternalUrl();
    }

    /**
     * Check if there's an uploaded file in storage.
     */
    public function hasUploadedFile(): bool
    {
        if (filled($this->file_path)) {
            return Storage::disk('public')->exists($this->file_path);
        }

        return false;
    }

    /**
     * Check if there's a public asset (e.g., docs/file.pdf or docs/file.png).
     */
    public function hasPublicAsset(): bool
    {
        return filled($this->public_asset_path);
    }

    /**
     * Check if there's a valid external URL.
     */
    public function hasSafeExternalUrl(): bool
    {
        $url = trim((string) $this->external_url);
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
     * Get the public URL for the document.
     */
    public function getDocumentUrl(): ?string
    {
        // Priority 1: Public asset (already in public folder)
        if ($this->hasPublicAsset()) {
            return asset($this->public_asset_path);
        }

        // Priority 2: Uploaded file in storage
        if ($this->hasUploadedFile()) {
            return asset('storage/'.$this->file_path);
        }

        // Priority 3: External URL
        if ($this->hasSafeExternalUrl()) {
            return $this->external_url;
        }

        return null;
    }

    /**
     * Alias for backward compatibility.
     */
    public function getPdfUrl(): ?string
    {
        return $this->getDocumentUrl();
    }

    /**
     * Check if the document is a PDF.
     */
    public function isPdf(): bool
    {
        $ext = $this->getExtension();

        return $ext === 'pdf' || $this->mime_type === 'application/pdf';
    }

    /**
     * Check if the document is an image (PNG/JPG/JPEG).
     */
    public function isImage(): bool
    {
        $ext = $this->getExtension();

        return in_array($ext, ['png', 'jpg', 'jpeg'], true)
            || in_array($this->mime_type, ['image/png', 'image/jpeg'], true);
    }

    /**
     * Get the file extension.
     */
    public function getExtension(): ?string
    {
        // If extension is explicitly set, use it
        if (filled($this->extension)) {
            return strtolower($this->extension);
        }

        // Try to derive from public_asset_path
        if (filled($this->public_asset_path)) {
            return strtolower(pathinfo($this->public_asset_path, PATHINFO_EXTENSION));
        }

        // Try to derive from file_path
        if (filled($this->file_path)) {
            return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        }

        // Try to derive from external_url
        if (filled($this->external_url)) {
            $path = parse_url($this->external_url, PHP_URL_PATH);
            if ($path) {
                return strtolower(pathinfo($path, PATHINFO_EXTENSION));
            }
        }

        return null;
    }

    /**
     * Get the MIME type.
     */
    public function getMimeType(): ?string
    {
        if (filled($this->mime_type)) {
            return $this->mime_type;
        }

        $ext = $this->getExtension();

        return match ($ext) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            default => null,
        };
    }

    /**
     * Get human-readable format label.
     */
    public function getFormatLabel(): string
    {
        $ext = $this->getExtension();

        return match ($ext) {
            'pdf' => 'PDF',
            'png' => 'PNG',
            'jpg', 'jpeg' => 'JPG',
            default => 'Dokumen',
        };
    }

    /**
     * Get format description for card meta.
     */
    public function getFormatDescription(): string
    {
        $format = $this->getFormatLabel();

        return $format.' / Kalender Akademik';
    }

    /**
     * Get the download filename.
     */
    public function getDownloadFilename(): string
    {
        $ext = $this->getExtension() ?? 'pdf';

        return 'Kalender-Akademik-'.$this->year.'.'.$ext;
    }

    /**
     * Get the year label (e.g., "2026/2027").
     */
    public function getYearLabel(): string
    {
        $nextYear = $this->year + 1;

        return $this->year.'/'.$nextYear;
    }

    /**
     * Get availability status string.
     */
    public function availabilityStatus(): string
    {
        return $this->isAvailable() ? 'available' : 'missing';
    }

    /**
     * Resolve the source type based on available data.
     */
    public function resolveSourceType(): string
    {
        if (filled($this->source_type)) {
            return (string) $this->source_type;
        }

        if ($this->hasPublicAsset()) {
            return self::SOURCE_PUBLIC_ASSET;
        }

        if ($this->hasUploadedFile()) {
            return self::SOURCE_UPLOADED;
        }

        if ($this->hasSafeExternalUrl()) {
            return self::SOURCE_EXTERNAL;
        }

        return self::SOURCE_UPLOADED;
    }

    /**
     * Sync source flags based on current data.
     */
    public function syncSourceFlags(): void
    {
        $this->source_type = $this->resolveSourceType();
    }

    /**
     * Get alt text for image display.
     */
    public function getAltText(): string
    {
        return 'Kalender Akademik Poltekkes Kemenkes Banten Tahun Akademik '.$this->getYearLabel();
    }
}
