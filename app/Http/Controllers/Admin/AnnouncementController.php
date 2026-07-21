<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = Announcement::query()
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('is_pinned')
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.announcements.index', compact('announcements'));
    }

    public function create(): View
    {
        return view('admin.announcements.create', [
            'announcement' => new Announcement(['status' => 'draft', 'is_pinned' => false]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Announcement::class);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $announcement = Announcement::query()->create($data);
        ActivityLog::record('created', $announcement, 'Membuat pengumuman: '.$announcement->title);

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Announcement::class, $announcement->id);
        $data['updated_by'] = $request->user()->id;

        $announcement->update($data);
        ActivityLog::record('updated', $announcement, 'Memperbarui pengumuman: '.$announcement->title);

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        ActivityLog::record('deleted', $announcement, 'Menghapus pengumuman: '.$announcement->title);
        $announcement->delete();

        return redirect()->route('admin.announcements.index')->with('status', 'Pengumuman berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'is_pinned' => ['sometimes', 'boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_pinned'] = $request->boolean('is_pinned');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
