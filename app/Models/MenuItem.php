<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'type', 'route_name', 'route_params',
        'url', 'page_slug', 'target', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function resolvedUrl(): string
    {
        if ($this->type === 'url' && filled($this->url)) {
            return $this->url;
        }

        if ($this->type === 'page' && filled($this->page_slug)) {
            try {
                return route('pages.show', ['slug' => $this->page_slug]);
            } catch (\Throwable) {
                return '#';
            }
        }

        if ($this->type === 'route' && filled($this->route_name)) {
            try {
                return route($this->route_name, $this->route_params ?? []);
            } catch (\Throwable) {
                return '#';
            }
        }

        return '#';
    }
}
