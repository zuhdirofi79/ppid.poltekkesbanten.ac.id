<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InformationServiceSummary extends Model
{
    protected $fillable = [
        'year', 'total_received', 'fulfilled_full', 'fulfilled_partial', 'rejected',
        'average_response_days', 'response_time_description', 'rejection_reasons',
        'rejection_reason_breakdown', 'notes', 'is_published', 'published_at',
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
            'rejection_reason_breakdown' => 'array',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        if (! Schema::hasColumn($query->getModel()->getTable(), 'is_published')) {
            return $query;
        }

        return $query->where('is_published', true);
    }

    public function countText(string $attribute): string
    {
        $value = $this->getAttribute($attribute);

        if ($value === null) {
            return 'Data belum tersedia';
        }

        return (string) (int) $value;
    }

    public function countCaption(string $attribute): ?string
    {
        $value = $this->getAttribute($attribute);

        if ($value === null) {
            return null;
        }

        return ((int) $value) === 0 ? '0 permohonan' : null;
    }

    public function responseDaysText(): string
    {
        if ($this->average_response_days === null) {
            return 'Data belum tersedia';
        }

        return number_format((float) $this->average_response_days, 2, ',', '.');
    }

    public function responseDaysCaption(): ?string
    {
        if ($this->average_response_days === null) {
            return null;
        }

        return ((float) $this->average_response_days) === 0.0 ? '0 hari kerja' : 'hari kerja';
    }

    /**
     * @return list<string>
     */
    public function rejectionReasonLines(): array
    {
        $text = trim((string) $this->rejection_reasons);
        if ($text === '') {
            return [];
        }

        $lines = preg_split("/\r\n|\n|\r/", $text) ?: [];

        return array_values(array_filter(array_map(
            static fn ($line) => trim((string) $line),
            $lines,
        ), static fn (string $line) => $line !== ''));
    }

    /**
     * @return list<array{reason: string, count: int}>
     */
    public function rejectionBreakdown(): array
    {
        if (! is_array($this->rejection_reason_breakdown)) {
            return [];
        }

        $rows = [];
        foreach ($this->rejection_reason_breakdown as $row) {
            if (! is_array($row)) {
                continue;
            }

            $reason = trim((string) ($row['reason'] ?? ''));
            if ($reason === '' || ! array_key_exists('count', $row) || $row['count'] === null || $row['count'] === '') {
                continue;
            }

            $rows[] = [
                'reason' => $reason,
                'count' => (int) $row['count'],
            ];
        }

        return $rows;
    }
}
