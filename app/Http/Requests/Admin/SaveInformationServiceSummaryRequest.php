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
        foreach (['total_received', 'fulfilled_full', 'fulfilled_partial', 'rejected', 'average_response_days'] as $field) {
            if ($this->input($field) === '' || $this->input($field) === null) {
                $merged[$field] = null;
            }
        }

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
            'fulfilled_full' => ['nullable', 'integer', 'min:0'],
            'fulfilled_partial' => ['nullable', 'integer', 'min:0'],
            'rejected' => ['nullable', 'integer', 'min:0'],
            'average_response_days' => ['nullable', 'numeric', 'min:0'],
            'response_time_description' => ['nullable', 'string', 'max:5000'],
            'rejection_reasons' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
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
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function summaryAttributes(): array
    {
        $data = $this->safe()->except(['status']);
        $publish = $this->input('status') === 'published';
        $existing = $this->route('summary');

        $data['is_published'] = $publish;
        $data['published_at'] = $publish
            ? ($existing?->published_at ?? now())
            : null;

        return $data;
    }
}
