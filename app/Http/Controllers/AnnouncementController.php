<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::query()
            ->published()
            ->orderByDesc('is_pinned')
            ->latest('starts_at')
            ->paginate(12);

        return view('pages.announcements.index', compact('announcements'));
    }

    public function show(string $slug): View
    {
        $announcement = Announcement::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('pages.announcements.show', compact('announcement'));
    }
}
