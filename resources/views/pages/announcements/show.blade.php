@extends('layouts.app')
@section('title', $announcement->title.' — '.config('ppid.name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $announcement->excerpt), 160))

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li>/</li>
                <li><a href="{{ route('announcements.index') }}" class="hover:text-green">Pengumuman</a></li>
                <li>/</li>
                <li class="text-navy" aria-current="page">{{ \Illuminate\Support\Str::limit($announcement->title, 40) }}</li>
            </ol>
        </nav>
        <h1 class="font-heading text-2xl font-bold text-navy">{{ $announcement->title }}</h1>
    </div>
</div>
<article class="prose prose-slate mx-auto max-w-3xl px-4 py-10">
    {!! $announcement->body !!}
</article>
@endsection
