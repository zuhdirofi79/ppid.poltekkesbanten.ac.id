<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InformationServiceSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InformationServiceSummaryController extends Controller
{
    public function index(): View
    {
        $summaries = InformationServiceSummary::query()->orderByDesc('year')->get();

        return view('admin.service-summaries.index', [
            'summaries' => $summaries,
            'summary' => new InformationServiceSummary,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        InformationServiceSummary::query()->updateOrCreate(
            ['year' => $data['year']],
            $data,
        );

        return redirect()->route('admin.service-summaries.index')->with('status', 'Ringkasan layanan disimpan. Angka hanya berasal dari isian admin.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'total_received' => ['nullable', 'integer', 'min:0'],
            'fulfilled_full' => ['nullable', 'integer', 'min:0'],
            'fulfilled_partial' => ['nullable', 'integer', 'min:0'],
            'rejected' => ['nullable', 'integer', 'min:0'],
            'average_response_days' => ['nullable', 'numeric', 'min:0'],
            'response_time_description' => ['nullable', 'string', 'max:5000'],
            'rejection_reasons' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
