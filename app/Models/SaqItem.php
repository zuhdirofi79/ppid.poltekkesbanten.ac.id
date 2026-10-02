<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaqItem extends Model
{
    protected $fillable = [
        'code', 'year', 'section', 'subsection', 'question', 'evidence_requirement',
        'recommended_content_type', 'recommended_public_route', 'answer', 'status',
        'notes', 'requires_public', 'owner_id', 'reviewed_by', 'published_at',
        'completed_at', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'requires_public' => 'boolean',
            'published_at' => 'datetime',
            'completed_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SaqEvidence::class)->orderBy('sort_order')->orderBy('id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
