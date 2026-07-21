@extends('layouts.app')
@section('title', 'Berita — '.config('ppid.name'))
@section('meta_description', 'Berita dan kegiatan PPID Poltekkes Kemenkes Banten.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">Berita</h1>
    </div>
</div>
<div class="mx-auto max-w-7xl px-4 py-8">
    <form method="GET" class="mb-6 flex flex-wrap gap-2">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita..." class="rounded-md border border-line px-3 py-2 text-sm">
        <select name="category" class="rounded-md border border-line px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Filter</button>
    </form>
    <div class="grid gap-5 md:grid-cols-3">
        @forelse ($posts as $post)
            <article class="border-b border-line pb-4">
                <time class="text-xs text-muted" datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->locale('id')->translatedFormat('d F Y') }}</time>
                <h2 class="font-heading mt-2 text-lg font-bold text-navy">
                    <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-green">{{ $post->title }}</a>
                </h2>
                <p class="mt-2 text-sm text-muted">{{ $post->excerpt }}</p>
            </article>
        @empty
            <p class="text-muted md:col-span-3">Belum ada berita dipublikasikan.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $posts->links() }}</div>
</div>
@endsection
