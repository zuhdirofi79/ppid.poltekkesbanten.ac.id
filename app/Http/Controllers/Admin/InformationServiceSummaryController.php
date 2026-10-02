<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveInformationServiceSummaryRequest;
use App\Models\InformationServiceSummary;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InformationServiceSummaryController extends Controller
{
    public function index(): View
    {
        return view('admin.service-summaries.index', [
            'summaries' => InformationServiceSummary::query()->orderByDesc('year')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.service-summaries.create', [
            'summary' => new InformationServiceSummary(['is_published' => false]),
        ]);
    }

    public function store(SaveInformationServiceSummaryRequest $request): RedirectResponse
    {
        $summary = InformationServiceSummary::query()->create($request->summaryAttributes());

        return redirect()
            ->route('admin.service-summaries.index')
            ->with('status', 'Laporan layanan informasi tahun '.$summary->year.' disimpan.');
    }

    public function edit(InformationServiceSummary $summary): View
    {
        return view('admin.service-summaries.edit', [
            'summary' => $summary,
        ]);
    }

    public function update(SaveInformationServiceSummaryRequest $request, InformationServiceSummary $summary): RedirectResponse
    {
        $summary->update($request->summaryAttributes());

        return redirect()
            ->route('admin.service-summaries.index')
            ->with('status', 'Laporan layanan informasi tahun '.$summary->year.' diperbarui.');
    }

    public function destroy(InformationServiceSummary $summary): RedirectResponse
    {
        $year = $summary->year;
        $summary->delete();

        return redirect()
            ->route('admin.service-summaries.index')
            ->with('status', 'Laporan layanan informasi tahun '.$year.' dihapus.');
    }

    public function publish(InformationServiceSummary $summary): RedirectResponse
    {
        $publish = ! $summary->is_published;
        $summary->update([
            'is_published' => $publish,
            'published_at' => $publish ? now() : null,
        ]);

        return redirect()
            ->route('admin.service-summaries.index')
            ->with('status', $publish
                ? 'Laporan tahun '.$summary->year.' dipublikasikan.'
                : 'Laporan tahun '.$summary->year.' dikembalikan ke draft.');
    }
}
