<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'creator'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($inner) => $inner->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            })
            ->when($request->filled('category'), fn ($q) => $q->where('post_category_id', $request->integer('category')))
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('pages.posts.index', [
            'posts' => $posts,
            'categories' => PostCategory::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'creator'])
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->post_category_id, fn ($q) => $q->where('post_category_id', $post->post_category_id))
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('pages.posts.show', compact('post', 'related'));
    }
}
