<?php

namespace App\Models;

use App\Models\Concerns\HasAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicDocument extends Model
{
    use HasAuthors, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'document_category_id', 'public_information_category_id',
        'description', 'year', 'file_path', 'external_url', 'file_name', 'file_type',
        'file_size', 'download_count', 'is_external', 'status', 'published_at',
        'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'file_size' => 'integer',
            'download_count' => 'integer',
            'is_external' => 'boolean',
            'published_at' => 'datetime',
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
}
