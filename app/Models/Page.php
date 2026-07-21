<?php

namespace App\Models;

use App\Models\Concerns\HasAuthors;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasAuthors, SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'meta_title', 'meta_description', 'body',
        'image_path', 'status', 'published_at', 'sort_order', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }
}
