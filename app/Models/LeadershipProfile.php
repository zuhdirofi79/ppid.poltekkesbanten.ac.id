<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LeadershipProfile extends Model
{
    public const DEFAULT_PHOTOS = [
        'director' => 'images/ppid/direktur1.png',
        'vice-director-1' => 'images/ppid/wakildirektur1.png',
        'vice-director-2' => 'images/ppid/wakildirektur2-fotosementara.png',
        'vice-director-3' => 'images/ppid/wakildirektur3.png',
    ];

    public const LEGACY_PHOTOS = [
        'director' => ['images/pimpinan/direktur-dahrizal.png'],
        'vice-director-1' => ['images/pimpinan/wakil-direktur-1-kusniawati.png'],
        'vice-director-2' => ['images/pimpinan/wakil-direktur-2-sendy-cokro-wibowo.png'],
    ];

    protected $fillable = [
        'uuid', 'position_key', 'name', 'credentials', 'position', 'short_summary',
        'detail_body', 'education', 'work_history', 'photo_media_asset_id', 'photo_path',
        'sort_order', 'is_published', 'is_featured', 'published_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (LeadershipProfile $profile): void {
            $profile->uuid ??= (string) Str::uuid();
        });
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'photo_media_asset_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function displayName(): string
    {
        $name = trim($this->name);
        $credentials = trim((string) $this->credentials);

        if ($credentials === '' || str_contains($name, $credentials)) {
            return $name;
        }

        return $name.', '.$credentials;
    }

    public function photoUrl(): ?string
    {
        $photo = $this->relationLoaded('photo') ? $this->photo : $this->photo()->first();

        if ($photo && $photo->visibility === 'public' && $photo->disk === 'public' && $photo->publicUrl()) {
            return $photo->publicUrl();
        }

        $path = $this->photo_path ?: (self::DEFAULT_PHOTOS[$this->position_key] ?? null);
        if (! is_string($path) || $path === '') {
            return null;
        }

        return asset($path);
    }

    public function photoAlt(): string
    {
        return match ($this->position_key) {
            'director' => 'Dr. Dahrizal, Direktur Poltekkes Kemenkes Banten',
            'vice-director-1' => 'Kusniawati, Wakil Direktur I Poltekkes Kemenkes Banten',
            'vice-director-2' => 'Sendy Cokro Wibowo, Wakil Direktur II Poltekkes Kemenkes Banten',
            'vice-director-3' => 'Asmawati, Wakil Direktur III Poltekkes Kemenkes Banten',
            default => trim($this->displayName().', '.$this->position, ' ,'),
        };
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->displayName())) ?: [];
        $letters = '';

        foreach ($parts as $part) {
            if (in_array(strtolower($part), ['dr.', 'dr', 's.kp.', 's.kep.', 'ners.', 'm.kep.', 'm.kep', 's.e.', 'mph'], true)) {
                continue;
            }
            $letters .= mb_strtoupper(mb_substr($part, 0, 1));
            if (mb_strlen($letters) >= 2) {
                break;
            }
        }

        return $letters !== '' ? $letters : 'P';
    }

    /** @return list<string> */
    public function educationItems(): array
    {
        return $this->lines($this->education);
    }

    /** @return list<string> */
    public function workItems(): array
    {
        return $this->lines($this->work_history);
    }

    /** @return list<string> */
    public function detailParagraphs(): array
    {
        $text = trim((string) $this->detail_body);
        if ($text === '') {
            return [];
        }

        $parts = preg_split("/\r\n\r\n|\n\n/", $text) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    /** @return list<string> */
    private function lines(?string $value): array
    {
        $lines = preg_split("/\r\n|\n|\r/", trim((string) $value)) ?: [];

        return array_values(array_filter(array_map(function (string $line) {
            return trim(preg_replace('/^[-*]\s*/', '', trim($line)) ?? '');
        }, $lines)));
    }
}
