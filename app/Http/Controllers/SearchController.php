<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PublicDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->string('q'));

        $posts = collect();
        $documents = collect();
        $pages = collect();
        $announcements = collect();
        $faqs = collect();

        if ($q !== '') {
            $like = '%'.$q.'%';

            $posts = Post::query()->published()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('excerpt', 'like', $like))
                ->latest('published_at')
                ->limit(10)
                ->get();

            $documents = PublicDocument::query()->published()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('description', 'like', $like))
                ->latest('published_at')
                ->limit(10)
                ->get();

            $pages = Page::query()->published()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('excerpt', 'like', $like))
                ->limit(10)
                ->get();

            $announcements = Announcement::query()->published()
                ->where(fn ($query) => $query->where('title', 'like', $like)->orWhere('excerpt', 'like', $like))
                ->limit(10)
                ->get();

            $faqs = Faq::query()->where('is_active', true)
                ->where(fn ($query) => $query->where('question', 'like', $like)->orWhere('answer', 'like', $like))
                ->limit(10)
                ->get();
        }

        return view('pages.search', compact('q', 'posts', 'documents', 'pages', 'announcements', 'faqs'));
    }
}
