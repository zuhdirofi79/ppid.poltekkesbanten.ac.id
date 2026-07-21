<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PublicDocument;
use App\Models\Unit;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'posts' => Post::query()->count(),
            'announcements' => Announcement::query()->count(),
            'documents' => PublicDocument::query()->count(),
            'pages' => Page::query()->count(),
            'faqs' => Faq::query()->count(),
            'units' => Unit::query()->count(),
            'users' => User::query()->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
