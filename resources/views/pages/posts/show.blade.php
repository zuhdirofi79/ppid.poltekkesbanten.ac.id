@extends('layouts.app')
@section('title', $post->title.' — '.config('ppid.name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $post->excerpt), 160))
@section('og_type', 'article')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li>/</li>
                <li><a href="{{ route('posts.index') }}" class="hover:text-green">Berita</a></li>
                <li>/</li>
                <li class="text-navy" aria-current="page">{{ \Illuminate\Support\Str::limit($post->title, 40) }}</li>
            </ol>
        </nav>
        <p class="text-xs text-muted">
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->locale('id')->translatedFormat('d F Y') }}</time>
            @if ($post->creator) · {{ $post->creator->name }} @endif
            @if ($post->category) · {{ $post->category->name }} @endif
        </p>
        <h1 class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">{{ $post->title }}</h1>
    </div>
</div>
<article class="prose prose-slate mx-auto max-w-3xl px-4 py-10">
    {!! $post->body !!}
</article>
@if ($related->isNotEmpty())
<section class="border-t border-line bg-surface py-10">
    <div class="mx-auto max-w-3xl px-4">
        <h2 class="font-heading mb-4 text-xl font-bold text-navy">Berita terkait</h2>
        <ul class="space-y-2">
            @foreach ($related as $item)
                <li><a href="{{ route('posts.show', $item->slug) }}" class="text-green hover:underline">{{ $item->title }}</a></li>
            @endforeach
        </ul>
    </div>
</section>
@endif
@endsection
