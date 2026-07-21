<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Page;
use App\Support\HtmlSanitizer;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        $pages = Page::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create', ['page' => new Page(['status' => 'draft'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Page::class);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $page = Page::query()->create($data);
        ActivityLog::record('created', $page, 'Membuat halaman: '.$page->title);

        return redirect()->route('admin.pages.index')->with('status', 'Halaman berhasil dibuat.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $this->validated($request, $page);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Page::class, $page->id);
        $data['updated_by'] = $request->user()->id;

        $page->update($data);
        ActivityLog::record('updated', $page, 'Memperbarui halaman: '.$page->title);

        return redirect()->route('admin.pages.index')->with('status', 'Halaman berhasil diperbarui.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        ActivityLog::record('deleted', $page, 'Menghapus halaman: '.$page->title);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'Halaman berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Page $page = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['body'] = HtmlSanitizer::clean($data['body'] ?? null);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
