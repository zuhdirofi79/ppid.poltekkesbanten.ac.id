<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformationServiceSummary extends Model
{
    protected $fillable = [
        'year', 'total_received', 'fulfilled_full', 'fulfilled_partial', 'rejected',
        'average_response_days', 'response_time_description', 'rejection_reasons', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'total_received' => 'integer',
            'fulfilled_full' => 'integer',
            'fulfilled_partial' => 'integer',
            'rejected' => 'integer',
            'average_response_days' => 'decimal:2',
        ];
    }
}
