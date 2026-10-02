<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaqEvidence extends Model
{
    protected $table = 'saq_evidences';

    protected $fillable = [
        'saq_item_id', 'evidence_type', 'media_asset_id', 'page_id',
        'public_document_id', 'external_url', 'title', 'description',
        'visibility', 'sort_order', 'created_by',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(SaqItem::class, 'saq_item_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'media_asset_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(PublicDocument::class, 'public_document_id');
    }
}
