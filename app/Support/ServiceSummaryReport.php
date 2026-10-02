<?php

namespace App\Support;

use App\Models\InformationServiceSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ServiceSummaryReport
{
    public const SLUG = 'ringkasan-laporan-layanan-informasi';

    public const UNAVAILABLE = 'Data laporan layanan informasi untuk tahun ini belum tersedia.';

    /**
     * @return Collection<int, InformationServiceSummary>
     */
    public static function publishedRows(): Collection
    {
        if (! Schema::hasTable('information_service_summaries')) {
            return collect();
        }

        return InformationServiceSummary::query()->published()->orderByDesc('year')->get();
    }

    /**
     * @return array{
     *     availableYears: list<int>,
     *     selectedYear: ?int,
     *     summary: ?InformationServiceSummary,
     *     unavailable: bool,
     *     comparison: Collection<int, InformationServiceSummary>,
     *     charts: array<string, mixed>
     * }
     */
    public static function fromRequest(Request $request): array
    {
        $rows = self::publishedRows();
        $availableYears = $rows->pluck('year')->map(fn ($year) => (int) $year)->values()->all();
        $raw = $request->query('tahun');
        $selectedYear = null;
        $summary = null;
        $unavailable = false;

        if ($raw !== null && $raw !== '') {
            $rawYear = is_scalar($raw) ? (string) $raw : '';
            if (! preg_match('/^\d{4}$/', $rawYear)) {
                $unavailable = true;
            } else {
                $selectedYear = (int) $rawYear;
                $summary = $rows->first(fn (InformationServiceSummary $row) => $row->year === $selectedYear);
                $unavailable = $summary === null;
            }
        } elseif ($rows->isNotEmpty()) {
            $summary = $rows->first();
            $selectedYear = $summary?->year;
        } else {
            $unavailable = true;
        }

        $comparison = $rows
            ->filter(fn (InformationServiceSummary $row) => $row->total_received !== null)
            ->sortBy('year')
            ->values();

        return [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'summary' => $summary,
            'unavailable' => $unavailable,
            'comparison' => $comparison,
            'charts' => self::charts($summary, $comparison, $selectedYear),
        ];
    }

    /**
     * @param  Collection<int, InformationServiceSummary>  $comparison
     * @return array<string, mixed>
     */
    private static function charts(?InformationServiceSummary $summary, Collection $comparison, ?int $year): array
    {
        $status = [];
        $hasSplit = $summary && ($summary->fulfilled_full !== null || $summary->fulfilled_partial !== null);
        if ($hasSplit) {
            foreach ([
                'Dikabulkan seluruhnya' => $summary->fulfilled_full,
                'Dikabulkan sebagian' => $summary->fulfilled_partial,
                'Ditolak' => $summary->rejected,
            ] as $label => $value) {
                if ($value !== null && (int) $value > 0) {
                    $status[] = ['label' => $label, 'value' => (int) $value];
                }
            }
        }

        $periodLabels = [];
        $periodReceived = [];
        $periodProvided = [];
        $periodRejected = [];
        $periodProgress = [];
        if ($summary) {
            foreach ($summary->periodRows() as $row) {
                $periodLabels[] = $row['label'];
                $periodReceived[] = $row['received'];
                $periodProvided[] = $row['provided'];
                $periodRejected[] = $row['rejected'];
                $periodProgress[] = $row['in_progress'];
            }
        }

        $typeLabels = [];
        $typeValues = [];
        if ($summary) {
            foreach ($summary->informationTypes() as $row) {
                $typeLabels[] = $row['label'];
                $typeValues[] = $row['count'];
            }
        }

        $volumeLabels = [];
        $volumeValues = [];
        if ($summary) {
            foreach ($comparison as $row) {
                $volumeLabels[] = (string) $row->year;
                $volumeValues[] = (int) $row->total_received;
            }
        }

        $reasonLabels = [];
        $reasonValues = [];
        if ($summary) {
            foreach ($summary->rejectionBreakdown() as $row) {
                $reasonLabels[] = $row['reason'];
                $reasonValues[] = $row['count'];
            }
        }

        $yearLabel = $year ? (string) $year : 'yang dipilih';

        return [
            'status' => [
                'aria' => 'Diagram status pemenuhan permohonan informasi tahun '.$yearLabel,
                'segments' => $status,
            ],
            'volume' => [
                'aria' => 'Diagram jumlah permohonan informasi publik',
                'labels' => $volumeLabels,
                'values' => $volumeValues,
            ],
            'reasons' => [
                'aria' => 'Diagram alasan penolakan permohonan informasi tahun '.$yearLabel,
                'labels' => $reasonLabels,
                'values' => $reasonValues,
            ],
            'periods' => [
                'aria' => 'Diagram perkembangan permohonan informasi tahun '.$yearLabel,
                'labels' => $periodLabels,
                'received' => $periodReceived,
                'provided' => $periodProvided,
                'rejected' => $periodRejected,
                'in_progress' => $periodProgress,
            ],
            'types' => [
                'aria' => 'Diagram jenis informasi yang diminta tahun '.$yearLabel,
                'labels' => $typeLabels,
                'values' => $typeValues,
            ],
        ];
    }
}
