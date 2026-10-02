<?php

namespace App\Support;

use App\Models\InformationServiceSummary;

class ServiceSummaryPdf
{
    /**
     * @param  array<string, mixed>  $report
     */
    public static function render(array $report): string
    {
        $pdf = new PlainPdf();
        $summary = $report['summary'] ?? null;
        $year = $report['selectedYear'] ?? null;
        $yearLabel = $year ? (string) $year : 'tidak dipilih';

        $pdf->heading('PPID Poltekkes Kemenkes Banten');
        $pdf->heading('RINGKASAN LAPORAN LAYANAN INFORMASI PUBLIK', 14);
        $pdf->line('TAHUN '.$yearLabel, 12, true);
        $pdf->gap(8);

        if (! $summary instanceof InformationServiceSummary) {
            $pdf->line(ServiceSummaryReport::UNAVAILABLE);
            $pdf->gap(12);
            $pdf->line('Tanggal generate laporan: '.now()->timezone('Asia/Jakarta')->format('d-m-Y H:i').' WIB', 9);
            return $pdf->output();
        }

        $pdf->numbered(1, 'Jumlah permohonan diterima', $summary->countText('total_received'));
        $pdf->numbered(2, 'Dikabulkan seluruhnya', $summary->countText('fulfilled_full'));
        $pdf->numbered(3, 'Dikabulkan sebagian', $summary->countText('fulfilled_partial'));
        $pdf->numbered(4, 'Ditolak', $summary->countText('rejected'));
        $pdf->numbered(5, 'Rata-rata waktu respons', $summary->average_response_days === null
            ? 'Data belum tersedia'
            : $summary->responseDaysText().' hari kerja');
        $pdf->gap(6);
        $pdf->line('Keterangan waktu respons', 11, true);
        $pdf->paragraph(trim((string) $summary->response_time_description) !== ''
            ? (string) $summary->response_time_description
            : 'Data belum tersedia');
        $pdf->gap(6);
        $pdf->line('Alasan penolakan', 11, true);

        $lines = $summary->rejectionReasonLines();
        $breakdown = $summary->rejectionBreakdown();
        if ($lines === [] && $breakdown === []) {
            $pdf->paragraph('Data belum tersedia');
        }
        foreach ($lines as $line) {
            $pdf->paragraph('- '.$line);
        }
        if ($breakdown !== []) {
            $pdf->gap(4);
            $max = max(1, ...array_column($breakdown, 'count'));
            foreach ($breakdown as $row) {
                $pdf->bar($row['reason'].' ('.$row['count'].')', $row['count'], $max);
            }
        }

        $pdf->gap(8);
        $pdf->line('Status pemenuhan', 11, true);
        $status = [
            ['Dikabulkan seluruhnya', $summary->fulfilled_full],
            ['Dikabulkan sebagian', $summary->fulfilled_partial],
            ['Ditolak', $summary->rejected],
        ];
        $known = array_values(array_filter($status, fn (array $row) => $row[1] !== null));
        if ($known === []) {
            $pdf->paragraph('Data belum tersedia');
        } else {
            $max = max(1, ...array_map(fn (array $row) => (int) $row[1], $known));
            foreach ($known as [$label, $value]) {
                $pdf->bar($label.' ('.(int) $value.')', (int) $value, $max);
            }
        }

        $pdf->gap(10);
        $pdf->line('Tanggal generate laporan: '.now()->timezone('Asia/Jakarta')->format('d-m-Y H:i').' WIB', 9);

        return $pdf->output();
    }
}

class PlainPdf
{
    /** @var list<string> */
    private array $pages = [];

    private string $stream = '';

    private float $y = 800;

    public function heading(string $text, int $size = 16): void
    {
        $this->ensureSpace(28);
        $this->text($text, $size, true);
        $this->y -= $size + 8;
    }

    public function line(string $text, int $size = 11, bool $bold = false): void
    {
        $this->ensureSpace(18);
        $this->text($text, $size, $bold);
        $this->y -= $size + 6;
    }

    public function paragraph(string $text): void
    {
        foreach ($this->wrap($text, 92) as $line) {
            $this->line($line, 10);
        }
    }

    public function numbered(int $number, string $label, string $value): void
    {
        $this->line($number.'. '.$label.': '.$value, 11);
    }

    public function bar(string $label, int $value, int $max): void
    {
        $this->ensureSpace(28);
        $this->text($label, 10, false);
        $this->y -= 14;
        $width = $max > 0 ? max(0, min(420, ($value / $max) * 420)) : 0;
        $this->stream .= sprintf("0.93 0.94 0.95 rg 48 %.2F 420 8 re f\n", $this->y);
        $this->stream .= sprintf("0 0.553 0.439 rg 48 %.2F %.2F 8 re f\n", $this->y, $width);
        $this->stream .= "0 0 0 rg\n";
        $this->y -= 16;
    }

    public function gap(float $amount): void
    {
        $this->y -= $amount;
    }

    public function output(): string
    {
        $this->finishPage();
        if ($this->pages === []) {
            $this->pages[] = '';
        }

        $fontId = 3 + (count($this->pages) * 2);
        $pageIds = [];
        $next = 3;
        foreach ($this->pages as $unused) {
            $pageIds[] = $next;
            $next += 2;
        }

        $kids = implode(' ', array_map(fn (int $id) => $id.' 0 R', $pageIds));
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            2 => '<< /Type /Pages /Count '.count($this->pages).' /Kids ['.$kids.'] >>',
        ];

        foreach ($this->pages as $index => $content) {
            $pageId = $pageIds[$index];
            $contentId = $pageId + 1;
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents '.$contentId.' 0 R /Resources << /Font << /F1 '.$fontId.' 0 R >> >> >>';
            $objects[$contentId] = "<< /Length ".strlen($content)." >>\nstream\n".$content."endstream";
        }
        $objects[$fontId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        ksort($objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $id => $body) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id." 0 obj\n".$body."\nendobj\n";
        }
        $xref = strlen($pdf);
        $size = $fontId + 1;
        $pdf .= "xref\n0 {$size}\n";
        $pdf .= "0000000000 65535 f \n";
        for ($id = 1; $id <= $fontId; $id++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }
        $pdf .= "trailer << /Size {$size} /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";

        return $pdf;
    }

    private function text(string $text, int $size, bool $bold): void
    {
        $encoded = $this->encode($text);
        $this->stream .= "BT /F1 {$size} Tf 48 ".number_format($this->y, 2, '.', '')." Td ({$encoded}) Tj ET\n";
        if ($bold) {
            $this->stream .= "BT /F1 {$size} Tf 48.4 ".number_format($this->y, 2, '.', '')." Td ({$encoded}) Tj ET\n";
        }
    }

    private function ensureSpace(float $needed): void
    {
        if ($this->y - $needed < 48) {
            $this->finishPage();
        }
    }

    private function finishPage(): void
    {
        if ($this->stream !== '') {
            $this->pages[] = $this->stream;
        }
        $this->stream = '';
        $this->y = 800;
    }

    /**
     * @return list<string>
     */
    private function wrap(string $text, int $width): array
    {
        $text = trim(preg_replace("/\s+/", ' ', str_replace(["\r\n", "\r", "\n"], ' ', $text)) ?? '');
        if ($text === '') {
            return [''];
        }

        $words = preg_split('/\s+/', $text) ?: [];
        $lines = [];
        $current = '';
        foreach ($words as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;
            if (strlen($candidate) > $width && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $candidate;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines === [] ? [''] : $lines;
    }

    private function encode(string $text): string
    {
        $converted = iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
        if ($converted === false) {
            $converted = preg_replace('/[^\x20-\x7E]/', '?', $text) ?? '';
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $converted);
    }
}
