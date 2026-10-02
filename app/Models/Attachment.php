<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'media_asset_id', 'attachable_type', 'attachable_id', 'display_title',
        'description', 'sort_order', 'visibility', 'created_by',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
