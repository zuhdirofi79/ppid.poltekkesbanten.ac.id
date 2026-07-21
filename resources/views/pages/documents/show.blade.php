@extends('layouts.app')
@section('title', $document->title.' — '.config('ppid.name'))
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags((string) $document->description), 160))

@section('content')
@php
    $breadcrumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Dokumen Publik', 'url' => route('documents.index')],
        ['label' => $document->title, 'url' => null],
    ];
@endphp
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li>/</li>
                <li><a href="{{ route('documents.index') }}" class="hover:text-green">Dokumen Publik</a></li>
                <li>/</li>
                <li class="text-navy" aria-current="page">{{ \Illuminate\Support\Str::limit($document->title, 40) }}</li>
            </ol>
        </nav>
        <h1 class="font-heading text-2xl font-bold text-navy">{{ $document->title }}</h1>
    </div>
</div>
<div class="mx-auto max-w-3xl space-y-4 px-4 py-10 text-sm">
    <dl class="grid gap-3 sm:grid-cols-2">
        <div><dt class="text-muted">Kategori</dt><dd class="font-medium">{{ $document->documentCategory?->name ?? '—' }}</dd></div>
        <div><dt class="text-muted">Jenis informasi</dt><dd class="font-medium">{{ $document->informationCategory?->name ?? '—' }}</dd></div>
        <div><dt class="text-muted">Tahun</dt><dd class="font-medium">{{ $document->year ?? '—' }}</dd></div>
        <div><dt class="text-muted">Unduhan</dt><dd class="font-medium">{{ number_format($document->download_count, 0, ',', '.') }}</dd></div>
    </dl>
    @if ($document->description)
        <div class="prose prose-sm max-w-none">{!! nl2br(e($document->description)) !!}</div>
    @endif
    <a href="{{ route('documents.download', $document->slug) }}" class="font-heading inline-flex rounded-md bg-green px-4 py-2.5 font-semibold text-white hover:bg-green-light">
        Unduh / buka dokumen
        @if ($document->is_external)
            <span class="sr-only">(tautan eksternal)</span>
        @endif
    </a>
</div>
@endsection
