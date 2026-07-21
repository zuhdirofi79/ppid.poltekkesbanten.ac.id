<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Models\PostCategory;
use App\Support\UniqueSlug;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::query()
            ->with('category')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.$request->string('q').'%'))
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create', [
            'post' => new Post(['status' => 'draft']),
            'categories' => PostCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Post::class);
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $post = Post::query()->create($data);
        ActivityLog::record('created', $post, 'Membuat berita: '.$post->title);

        return redirect()->route('admin.posts.index')->with('status', 'Berita berhasil dibuat.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', [
            'post' => $post,
            'categories' => PostCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validated($request);
        $data['slug'] = UniqueSlug::make($data['slug'] ?? $data['title'], Post::class, $post->id);
        $data['updated_by'] = $request->user()->id;

        $post->update($data);
        ActivityLog::record('updated', $post, 'Memperbarui berita: '.$post->title);

        return redirect()->route('admin.posts.index')->with('status', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        ActivityLog::record('deleted', $post, 'Menghapus berita: '.$post->title);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Berita berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'post_category_id' => ['nullable', 'exists:post_categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
