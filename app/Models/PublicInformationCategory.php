<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicInformationCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'excerpt', 'description', 'icon', 'is_active', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PublicDocument::class);
    }
}
