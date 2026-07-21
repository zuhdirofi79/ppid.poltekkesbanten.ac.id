<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorStatistic extends Model
{
    protected $fillable = ['date', 'visits', 'unique_visits'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'visits' => 'integer',
            'unique_visits' => 'integer',
        ];
    }
}
