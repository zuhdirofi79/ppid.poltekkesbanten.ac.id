<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Faq;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $faqs = Faq::query()->orderBy('sort_order')->orderBy('id')->paginate(20);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        return view('admin.faqs.create', ['faq' => new Faq(['is_active' => true, 'sort_order' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['question'], Faq::class);

        $faq = Faq::query()->create($data);
        ActivityLog::record('created', $faq, 'Membuat FAQ');

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil dibuat.');
    }

    public function edit(Faq $faq): View
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['question'], Faq::class, $faq->id);
        $faq->update($data);
        ActivityLog::record('updated', $faq, 'Memperbarui FAQ');

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil diperbarui.');
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        ActivityLog::record('deleted', $faq, 'Menghapus FAQ');
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'FAQ berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
