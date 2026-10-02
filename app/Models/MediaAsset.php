<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaAsset extends Model
{
    protected $fillable = [
        'uuid', 'original_name', 'stored_name', 'disk', 'path', 'mime_type',
        'extension', 'size', 'checksum', 'title', 'caption', 'alt_text',
        'uploaded_by', 'visibility',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(SaqEvidence::class);
    }

    public function isImage(): bool
    {
        return in_array($this->extension, ['png', 'jpg', 'jpeg'], true);
    }

    public function publicUrl(): ?string
    {
        if ($this->visibility !== 'public' || $this->disk !== 'public') {
            return null;
        }

        return route('media.show', $this->uuid);
    }
}
