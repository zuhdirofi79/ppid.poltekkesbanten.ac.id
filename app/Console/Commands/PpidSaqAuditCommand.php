<?php

namespace App\Console\Commands;

use App\Models\SaqItem;
use App\Services\SaqCompleteness;
use Illuminate\Console\Command;

class PpidSaqAuditCommand extends Command
{
    protected $signature = 'ppid:saq-audit {--year=2026} {--json}';

    protected $description = 'Ringkasan kelengkapan indikator SAQ Monev Internal';

    public function handle(SaqCompleteness $completeness): int
    {
        $year = (int) $this->option('year');
        $items = SaqItem::query()
            ->with(['evidences.page', 'evidences.document', 'evidences.media'])
            ->where('year', $year)
            ->orderBy('sort_order')
            ->get();

        $rows = $items->map(function (SaqItem $item) use ($completeness) {
            $assessment = $completeness->assess($item);

            return [
                'code' => $item->code,
                'section' => $item->section,
                'question' => $item->question,
                'status' => $assessment['status'],
                'evidence_count' => $assessment['evidence_count'],
                'public_url' => $assessment['public_url'],
                'public_state' => $assessment['public_state'],
                'updated_at' => optional($item->updated_at)->toDateTimeString(),
            ];
        });

        $sections = $rows->groupBy('section')->map(function ($group, $section) {
            $done = $group->filter(fn ($row) => in_array($row['status'], ['complete', 'published'], true))->count();

            return [
                'section' => $section,
                'done' => $done,
                'total' => $group->count(),
            ];
        })->values();

        if ($this->option('json')) {
            $this->line(json_encode([
                'year' => $year,
                'sections' => $sections,
                'items' => $rows->values(),
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->line('PPID SAQ MONEV '.$year);
        $this->line(str_repeat('=', 28));
        $this->newLine();

        foreach ($sections as $section) {
            $label = $section['section'];
            $pad = max(1, 36 - mb_strlen($label));
            $this->line($label.str_repeat('.', $pad).' '.$section['done'].'/'.$section['total']);
        }

        $missing = $rows->where('status', 'missing');
        $this->newLine();
        $this->line('Missing:');
        if ($missing->isEmpty()) {
            $this->line('- (tidak ada)');
        } else {
            foreach ($missing as $row) {
                $this->line('- '.$row['code'].' '.$row['question']);
            }
        }

        $this->newLine();
        foreach ($rows as $row) {
            $this->line(implode(' | ', [
                $row['code'],
                $row['status'],
                'bukti:'.$row['evidence_count'],
                'url:'.($row['public_url'] ?: '-'),
                'publik:'.$row['public_state'],
                'diperbarui:'.($row['updated_at'] ?: '-'),
            ]));
        }

        return self::SUCCESS;
    }
}
