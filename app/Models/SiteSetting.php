<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'label'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('site_settings_map', 3600, function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }

    public static function forgetCache(): void
    {
        Cache::forget('site_settings_map');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCache());
        static::deleted(fn () => static::forgetCache());
    }
}
