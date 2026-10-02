<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveInformationServiceSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canDo('manage-pages') === true;
    }

    protected function prepareForValidation(): void
    {
        $merged = [];
        foreach (['total_received', 'provided_total', 'fulfilled_full', 'fulfilled_partial', 'rejected', 'in_progress', 'average_response_days', 'source_document_path'] as $field) {
            if ($this->input($field) === '' || $this->input($field) === null) {
                $merged[$field] = null;
            }
        }

        $periods = $this->normalizeLabeledRows('period_breakdown', ['received', 'provided', 'rejected', 'in_progress']);
        $types = $this->normalizeLabeledRows('information_type_breakdown', ['count']);
        $merged['period_breakdown'] = $periods === [] ? null : $periods;
        $merged['information_type_breakdown'] = $types === [] ? null : $types;

        $rows = collect($this->input('rejection_reason_breakdown', []))
            ->filter(function ($row) {
                if (! is_array($row)) {
                    return false;
                }

                return filled($row['reason'] ?? null) || filled($row['count'] ?? null);
            })
            ->values()
            ->all();

        $merged['rejection_reason_breakdown'] = $rows === [] ? null : $rows;
        $this->merge($merged);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $summaryId = $this->route('summary')?->getKey();

        return [
            'year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
                Rule::unique('information_service_summaries', 'year')->ignore($summaryId),
            ],
            'total_received' => ['nullable', 'integer', 'min:0'],
            'provided_total' => ['nullable', 'integer', 'min:0'],
            'fulfilled_full' => ['nullable', 'integer', 'min:0'],
            'fulfilled_partial' => ['nullable', 'integer', 'min:0'],
            'rejected' => ['nullable', 'integer', 'min:0'],
            'in_progress' => ['nullable', 'integer', 'min:0'],
            'average_response_days' => ['nullable', 'numeric', 'min:0'],
            'response_time_description' => ['nullable', 'string', 'max:5000'],
            'rejection_reasons' => ['nullable', 'string', 'max:5000'],
            'source_document_path' => ['nullable', 'string', 'max:255'],
            'analysis_text' => ['nullable', 'string', 'max:10000'],
            'improvement_text' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'period_breakdown' => ['nullable', 'array', 'max:12'],
            'period_breakdown.*.label' => ['required', 'string', 'max:80'],
            'period_breakdown.*.received' => ['nullable', 'integer', 'min:0'],
            'period_breakdown.*.provided' => ['nullable', 'integer', 'min:0'],
            'period_breakdown.*.rejected' => ['nullable', 'integer', 'min:0'],
            'period_breakdown.*.in_progress' => ['nullable', 'integer', 'min:0'],
            'information_type_breakdown' => ['nullable', 'array', 'max:20'],
            'information_type_breakdown.*.label' => ['required', 'string', 'max:200'],
            'information_type_breakdown.*.count' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:published,draft'],
            'rejection_reason_breakdown' => ['nullable', 'array', 'max:20'],
            'rejection_reason_breakdown.*.reason' => ['required', 'string', 'max:200'],
            'rejection_reason_breakdown.*.count' => ['required', 'integer', 'min:0', 'max:1000000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'year.unique' => 'Laporan untuk tahun ini sudah ada. Gunakan halaman edit.',
            'status.required' => 'Status publikasi wajib dipilih.',
            'rejection_reason_breakdown.*.reason.required' => 'Setiap alasan penolakan wajib diisi.',
            'rejection_reason_breakdown.*.count.required' => 'Jumlah pada setiap alasan penolakan wajib diisi.',
            'rejection_reason_breakdown.*.count.integer' => 'Jumlah pada setiap alasan penolakan harus berupa angka.',
            'rejection_reason_breakdown.*.count.min' => 'Jumlah pada setiap alasan penolakan tidak boleh negatif.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $total = $this->input('total_received');
            if ($total === null || $total === '') {
                return;
            }

            $parts = [
                $this->input('fulfilled_full'),
                $this->input('fulfilled_partial'),
                $this->input('rejected'),
            ];
            $known = false;
            $sum = 0;
            foreach ($parts as $part) {
                if ($part !== null && $part !== '') {
                    $known = true;
                    $sum += (int) $part;
                }
            }

            if ($known && $sum > (int) $total) {
                $validator->errors()->add(
                    'total_received',
                    'Jumlah dikabulkan seluruhnya, dikabulkan sebagian, dan ditolak tidak boleh melebihi jumlah permohonan yang diterima.',
                );
            }

            $provided = $this->input('provided_total');
            $rejected = $this->input('rejected');
            $progress = $this->input('in_progress');
            if ($this->filledNumber($provided) && $this->filledNumber($rejected) && $this->filledNumber($progress)) {
                $outcome = (int) $provided + (int) $rejected + (int) $progress;
                if ($outcome !== (int) $total) {
                    $validator->errors()->add(
                        'provided_total',
                        'Jumlah informasi diberikan, ditolak, dan dalam proses harus sama dengan jumlah permohonan yang diterima.',
                    );
                }
            }

            $this->assertBreakdownMatches($validator, 'period_breakdown', 'received', $total, 'Jumlah permintaan masuk per periode harus sama dengan jumlah permohonan yang diterima.');
            if ($this->filledNumber($provided)) {
                $this->assertBreakdownMatches($validator, 'period_breakdown', 'provided', $provided, 'Jumlah informasi diberikan per periode harus sama dengan total informasi diberikan.');
            }
            if ($this->filledNumber($rejected)) {
                $this->assertBreakdownMatches($validator, 'period_breakdown', 'rejected', $rejected, 'Jumlah ditolak per periode harus sama dengan total permohonan ditolak.');
            }
            if ($this->filledNumber($progress)) {
                $this->assertBreakdownMatches($validator, 'period_breakdown', 'in_progress', $progress, 'Jumlah dalam proses per periode harus sama dengan total dalam proses.');
            }

            $types = $this->input('information_type_breakdown');
            if (is_array($types) && $types !== []) {
                $typeSum = 0;
                $complete = true;
                foreach ($types as $row) {
                    if (! is_array($row) || ! $this->filledNumber($row['count'] ?? null)) {
                        $complete = false;
                        break;
                    }
                    $typeSum += (int) $row['count'];
                }
                if ($complete && $typeSum !== (int) $total) {
                    $validator->errors()->add(
                        'information_type_breakdown',
                        'Jumlah seluruh jenis informasi harus sama dengan jumlah permohonan yang diterima.',
                    );
                }
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryAttributes(): array
    {
        $data = $this->safe()->except(['status', 'analysis_text', 'improvement_text']);
        $data['analysis_paragraphs'] = $this->textBlocks($this->input('analysis_text'));
        $data['improvement_points'] = $this->textLines($this->input('improvement_text'));
        $publish = $this->input('status') === 'published';
        $existing = $this->route('summary');

        $data['is_published'] = $publish;
        $data['published_at'] = $publish
            ? ($existing?->published_at ?? now())
            : null;

        return $data;
    }

    /**
     * @param  list<string>  $numericKeys
     * @return list<array<string, mixed>>
     */
    private function normalizeLabeledRows(string $key, array $numericKeys): array
    {
        $rows = [];
        foreach ($this->input($key, []) as $row) {
            if (! is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $hasNumber = false;
            foreach ($numericKeys as $numeric) {
                if (filled($row[$numeric] ?? null)) {
                    $hasNumber = true;
                }
            }
            if ($label === '' && ! $hasNumber) {
                continue;
            }

            $clean = ['label' => $label];
            foreach ($numericKeys as $numeric) {
                $value = $row[$numeric] ?? null;
                $clean[$numeric] = ($value === '' || $value === null) ? null : (int) $value;
            }
            $rows[] = $clean;
        }

        return $rows;
    }

    private function filledNumber(mixed $value): bool
    {
        return $value !== null && $value !== '';
    }

    private function assertBreakdownMatches($validator, string $key, string $field, mixed $expected, string $message): void
    {
        $rows = $this->input($key);
        if (! is_array($rows) || $rows === []) {
            return;
        }

        $sum = 0;
        foreach ($rows as $row) {
            if (! is_array($row) || ! $this->filledNumber($row[$field] ?? null)) {
                return;
            }
            $sum += (int) $row[$field];
        }

        if ($sum !== (int) $expected) {
            $validator->errors()->add($key, $message);
        }
    }

    /**
     * @return list<string>|null
     */
    private function textBlocks(mixed $text): ?array
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null;
        }

        $parts = preg_split("/\n\s*\n/", $text) ?: [];
        $parts = array_values(array_filter(array_map(static fn ($part) => trim((string) $part), $parts)));

        return $parts === [] ? null : $parts;
    }

    /**
     * @return list<string>|null
     */
    private function textLines(mixed $text): ?array
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null;
        }

        $parts = preg_split("/\r\n|\n|\r/", $text) ?: [];
        $parts = array_values(array_filter(array_map(static fn ($part) => trim((string) $part), $parts)));

        return $parts === [] ? null : $parts;
    }
}
