<?php

namespace App\Models;

use App\Models\Concerns\HasAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasAuthors, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'attachment_path', 'status',
        'is_pinned', 'starts_at', 'ends_at', 'published_at', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }
}
