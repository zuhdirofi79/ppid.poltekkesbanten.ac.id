<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class InformationServiceSummary extends Model
{
    protected $fillable = [
        'year', 'total_received', 'provided_total', 'fulfilled_full', 'fulfilled_partial', 'rejected',
        'in_progress', 'average_response_days', 'response_time_description', 'rejection_reasons',
        'rejection_reason_breakdown', 'period_breakdown', 'information_type_breakdown',
        'source_document_path', 'analysis_paragraphs', 'improvement_points', 'notes',
        'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'total_received' => 'integer',
            'provided_total' => 'integer',
            'fulfilled_full' => 'integer',
            'fulfilled_partial' => 'integer',
            'rejected' => 'integer',
            'in_progress' => 'integer',
            'average_response_days' => 'decimal:2',
            'rejection_reason_breakdown' => 'array',
            'period_breakdown' => 'array',
            'information_type_breakdown' => 'array',
            'analysis_paragraphs' => 'array',
            'improvement_points' => 'array',
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

    /**
     * @return list<array{label: string, received: ?int, provided: ?int, rejected: ?int, in_progress: ?int}>
     */
    public function periodRows(): array
    {
        return $this->labeledRows('period_breakdown', ['received', 'provided', 'rejected', 'in_progress']);
    }

    /**
     * @return list<array{label: string, count: ?int}>
     */
    public function informationTypes(): array
    {
        if (! is_array($this->information_type_breakdown)) {
            return [];
        }

        $rows = [];
        foreach ($this->information_type_breakdown as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $rows[] = [
                'label' => $label,
                'count' => $this->nullableInt($row['count'] ?? null),
            ];
        }

        return $rows;
    }

    /**
     * @return list<string>
     */
    public function analysisParagraphs(): array
    {
        return $this->stringList($this->analysis_paragraphs);
    }

    /**
     * @return list<string>
     */
    public function improvementPoints(): array
    {
        return $this->stringList($this->improvement_points);
    }

    public function fulfillmentRate(): ?float
    {
        if ($this->total_received === null || (int) $this->total_received === 0 || $this->provided_total === null) {
            return null;
        }

        return round(((int) $this->provided_total / (int) $this->total_received) * 100, 1);
    }

    public function unfulfilledRate(): ?float
    {
        if ($this->total_received === null || (int) $this->total_received === 0 || $this->rejected === null) {
            return null;
        }

        return round(((int) $this->rejected / (int) $this->total_received) * 100, 1);
    }

    public function formatRate(?float $rate): string
    {
        if ($rate === null) {
            return 'Data belum tersedia';
        }

        return number_format($rate, 1, ',', '.').'%';
    }

    public function fulfillmentSplitText(): ?string
    {
        if ($this->fulfilled_full !== null || $this->fulfilled_partial !== null) {
            return null;
        }

        return 'Belum tersedia secara terpisah dalam Laporan Layanan Informasi Publik Tahun '.$this->year.'.';
    }

    public function responseTimeNarrative(): string
    {
        $description = trim((string) $this->response_time_description);
        if ($description !== '') {
            return $description;
        }

        if ($this->average_response_days !== null) {
            return '';
        }

        return 'Data waktu penyelesaian individual maupun rata-rata waktu pemenuhan permohonan belum tersedia secara kuantitatif dalam Laporan Layanan Informasi Publik Tahun '.$this->year.'.';
    }

    public function everyRequestWasFollowedUp(): bool
    {
        if ($this->total_received === null || $this->provided_total === null || $this->rejected === null || $this->in_progress === null) {
            return false;
        }

        return (int) $this->in_progress === 0
            && ((int) $this->provided_total + (int) $this->rejected) === (int) $this->total_received;
    }

    public function sourceDocumentUrl(): ?string
    {
        $path = trim(str_replace('\\', '/', (string) $this->source_document_path), '/');
        if ($path === '') {
            return null;
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $path)));

        return asset($encoded);
    }

    public function displayCount(mixed $value): string
    {
        if ($value === null || $value === '') {
            return 'Data belum tersedia';
        }

        return (string) (int) $value;
    }

    /**
     * @param  list<string>  $numericKeys
     * @return list<array<string, mixed>>
     */
    private function labeledRows(string $attribute, array $numericKeys): array
    {
        $source = $this->getAttribute($attribute);
        if (! is_array($source)) {
            return [];
        }

        $rows = [];
        foreach ($source as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $clean = ['label' => $label];
            foreach ($numericKeys as $key) {
                $clean[$key] = $this->nullableInt($row[$key] ?? null);
            }
            $rows[] = $clean;
        }

        return $rows;
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn ($line) => trim((string) $line),
            $value,
        ), static fn (string $line) => $line !== ''));
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
