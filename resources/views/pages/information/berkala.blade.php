@extends('layouts.app')
@section('title', $category->name.' — '.config('ppid.name'))
@section('meta_description', $category->excerpt ?? config('ppid.name_long'))

@section('content')
<!-- PPID-INFORMASI-BERKALA-DEPLOY-20260904-A -->
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="{{ route('information.index') }}" class="hover:text-green">Informasi Publik</a></li>
                <li aria-hidden="true">/</li>
                <li class="text-navy" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>
        <h1 class="font-heading text-[1.625rem] font-bold leading-tight text-navy sm:text-[1.875rem] lg:text-[2.125rem]">
            {{ $category->name }}
        </h1>
        @if ($category->excerpt)
            <p class="mt-2 max-w-3xl text-muted">{{ $category->excerpt }}</p>
        @endif
    </div>
</div>

<div class="ppid-berkala-page mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
    @forelse ($sections as $section)
        <section class="ppid-berkala-section" aria-labelledby="section-{{ $loop->index }}">
            <h2 id="section-{{ $loop->index }}" class="ppid-berkala-section-title">
                {{ $section['title'] }}
            </h2>

            <ul class="ppid-berkala-list">
                @foreach ($section['documents'] as $document)
                    @php
                        $available = $document->isAvailable();
                        $href = $document->publicHref();
                    @endphp
                    <li class="ppid-berkala-item {{ $available ? 'is-available' : 'is-missing' }}">
                        <div class="ppid-berkala-item-main">
                            <span class="ppid-berkala-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5">
                                    <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                                    <path d="M14 3v5h5"/>
                                </svg>
                            </span>
                            <div class="ppid-berkala-item-body">
                                <p class="ppid-berkala-item-title">{{ $document->title }}</p>
                                <p class="ppid-berkala-item-meta">
                                    @if ($available)
                                        <span class="ppid-berkala-badge is-available">Tersedia</span>
                                        <span>PDF / Dokumen</span>
                                    @else
                                        <span class="ppid-berkala-badge is-missing">Dokumen belum tersedia</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="ppid-berkala-item-action">
                            @if ($available && $href)
                                <a
                                    href="{{ $href }}"
                                    class="ppid-berkala-link"
                                    @if ($document->opensInNewTab())
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        aria-label="Lihat dokumen {{ $document->title }} (tab baru)"
                                    @else
                                        aria-label="Lihat dokumen {{ $document->title }}"
                                    @endif
                                >
                                    Lihat Dokumen
                                    <span aria-hidden="true">→</span>
                                </a>
                            @else
                                <span class="ppid-berkala-unavailable" role="status">Dokumen belum tersedia</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @empty
        <p class="rounded-xl border border-dashed border-line p-6 text-sm text-muted">
            Belum ada dokumen pada kategori ini.
        </p>
    @endforelse
</div>
@endsection
