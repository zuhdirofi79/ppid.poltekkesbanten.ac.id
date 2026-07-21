<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadLog extends Model
{
    protected $fillable = [
        'public_document_id', 'user_id', 'ip_address', 'user_agent',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(PublicDocument::class, 'public_document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
