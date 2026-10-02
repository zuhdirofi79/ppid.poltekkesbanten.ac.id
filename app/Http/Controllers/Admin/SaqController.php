<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\PublicDocument;
use App\Models\SaqEvidence;
use App\Models\SaqItem;
use App\Services\MediaLibrary;
use App\Services\SaqCompleteness;
use App\Http\Requests\StoreMediaAssetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaqController extends Controller
{
    public function index(Request $request, SaqCompleteness $completeness): View
    {
        $year = (int) $request->input('year', 2026);
        $filter = (string) $request->input('filter', 'all');

        $rows = SaqItem::query()
            ->with(['evidences.page', 'evidences.document', 'evidences.media'])
            ->where('year', $year)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get()
            ->map(function (SaqItem $item) use ($completeness) {
                return [
                    'item' => $item,
                    'assessment' => $completeness->assess($item),
                ];
            });

        $counts = [
            'total' => $rows->count(),
            'missing' => $rows->where('assessment.status', 'missing')->count(),
            'draft' => $rows->where('assessment.status', 'draft')->count(),
            'needs_review' => $rows->where('assessment.status', 'needs_review')->count(),
            'complete' => $rows->where('assessment.status', 'complete')->count(),
            'published' => $rows->where('assessment.status', 'published')->count(),
        ];
        $done = $counts['complete'] + $counts['published'];
        $counts['progress'] = $counts['total'] > 0 ? (int) round(($done / $counts['total']) * 100) : 0;

        $sections = $rows->groupBy(fn ($row) => $row['item']->section)->map(function ($group) {
            $total = $group->count();
            $finished = $group->filter(fn ($row) => in_array($row['assessment']['status'], ['complete', 'published'], true))->count();

            return ['total' => $total, 'done' => $finished];
        });

        $filtered = $rows->filter(function ($row) use ($filter) {
            $status = $row['assessment']['status'];

            return match ($filter) {
                'incomplete' => in_array($status, ['missing', 'draft', 'needs_review'], true),
                'no-evidence' => $row['assessment']['evidence_count'] === 0,
                'draft' => $status === 'draft',
                'needs_review' => $status === 'needs_review',
                'published' => $status === 'published',
                'complete' => in_array($status, ['complete', 'published'], true),
                default => true,
            };
        })->values();

        $recent = SaqEvidence::query()->with('item')->latest()->limit(5)->get();

        return view('admin.saq.index', compact('filtered', 'counts', 'sections', 'filter', 'year', 'recent'));
    }

    public function show(SaqItem $saq, SaqCompleteness $completeness): View
    {
        $saq->load(['evidences.page', 'evidences.document', 'evidences.media']);
        $assessment = $completeness->assess($saq);
        $pages = Page::query()->orderBy('title')->get(['id', 'title', 'slug', 'status']);
        $documents = PublicDocument::query()->orderBy('title')->get(['id', 'title', 'slug', 'status']);

        return view('admin.saq.show', compact('saq', 'assessment', 'pages', 'documents'));
    }

    public function update(Request $request, SaqItem $saq, SaqCompleteness $completeness): RedirectResponse
    {
        $data = $request->validate([
            'answer' => ['required', 'in:yes,no,not_set'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:missing,draft,needs_review,complete,published'],
        ]);

        if ($data['status'] === 'published' && ! $request->boolean('redaction_confirmed')) {
            throw ValidationException::withMessages([
                'redaction_confirmed' => 'Pastikan dokumen tidak memuat data pribadi atau informasi yang dikecualikan.',
            ]);
        }

        $preview = $completeness->assess($saq);
        if ($data['status'] === 'published' && ! $preview['has_public']) {
            return back()->withErrors([
                'status' => 'Indikator belum dapat diterbitkan karena belum ada URL publik atau berkas data dukung yang valid.',
            ])->withInput();
        }

        $saq->answer = $data['answer'];
        $saq->notes = $data['notes'] ?? null;
        $saq->reviewed_by = $request->user()->id;
        $completeness->persist($saq, $data['status']);

        ActivityLog::record('saq_status_changed', $saq, 'Memperbarui indikator '.$saq->code, [
            'status' => $saq->fresh()->status,
        ]);

        return redirect()->route('admin.saq.show', $saq)->with('status', 'Indikator SAQ disimpan.');
    }

    public function storeEvidence(Request $request, SaqItem $saq, SaqCompleteness $completeness, MediaLibrary $library): RedirectResponse
    {
        $data = $request->validate([
            'evidence_type' => ['required', 'in:page,public_document,external_url,media,screenshot'],
            'page_id' => ['nullable', 'exists:pages,id'],
            'public_document_id' => ['nullable', 'exists:public_documents,id'],
            'external_url' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'visibility' => ['required', 'in:public,internal'],
            'file' => ['nullable', 'file'],
        ]);

        if (in_array($data['evidence_type'], ['media', 'screenshot'], true)) {
            if (! $request->hasFile('file')) {
                throw ValidationException::withMessages(['file' => 'Unggah berkas data dukung.']);
            }

            $upload = StoreMediaAssetRequest::createFrom($request);
            $upload->setContainer(app())->setRedirector(app('redirect'));
            $upload->validateResolved();

            $asset = $library->store($request->file('file'), [
                'title' => $data['title'] ?? null,
                'visibility' => $data['visibility'],
            ], $request->user()->id);

            $data['media_asset_id'] = $asset->id;
        }

        if ($data['evidence_type'] === 'external_url') {
            $url = trim((string) ($data['external_url'] ?? ''));
            if (preg_match('#^\s*(javascript|data|vbscript):#i', $url) || ! preg_match('#^(https?://|/[\w\-./]+)#i', $url)) {
                throw ValidationException::withMessages([
                    'external_url' => 'URL publik harus http(s) atau path internal yang diawali garis miring.',
                ]);
            }
        }

        SaqEvidence::query()->create([
            'saq_item_id' => $saq->id,
            'evidence_type' => $data['evidence_type'],
            'media_asset_id' => $data['media_asset_id'] ?? null,
            'page_id' => $data['page_id'] ?? null,
            'public_document_id' => $data['public_document_id'] ?? null,
            'external_url' => $data['external_url'] ?? null,
            'title' => $data['title'] ?? null,
            'visibility' => $data['visibility'],
            'sort_order' => (int) $saq->evidences()->count(),
            'created_by' => $request->user()->id,
        ]);

        $completeness->persist($saq->fresh() ?? $saq);
        ActivityLog::record('saq_evidence_added', $saq, 'Menambah data dukung '.$saq->code);

        return redirect()->route('admin.saq.show', $saq)->with('status', 'Data dukung ditambahkan.');
    }

    public function destroyEvidence(SaqItem $saq, SaqEvidence $evidence, SaqCompleteness $completeness): RedirectResponse
    {
        abort_unless($evidence->saq_item_id === $saq->id, 404);
        $evidence->delete();
        $completeness->persist($saq->fresh() ?? $saq);

        return redirect()->route('admin.saq.show', $saq)->with('status', 'Relasi data dukung dilepas. Berkas di pustaka tidak dihapus.');
    }
}
