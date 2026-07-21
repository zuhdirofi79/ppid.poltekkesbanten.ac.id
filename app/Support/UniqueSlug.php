<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlug
{
    public static function make(string $value, string $modelClass, ?int $ignoreId = null, string $column = 'slug'): string
    {
        /** @var class-string<Model> $modelClass */
        $base = Str::slug(Str::limit($value, 180, ''));
        $base = $base !== '' ? $base : 'item';
        $slug = $base;
        $i = 2;

        while (
            $modelClass::query()
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->where($column, $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
