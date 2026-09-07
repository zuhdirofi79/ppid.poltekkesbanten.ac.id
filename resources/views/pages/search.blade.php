@extends('layouts.app')

@section('title', ($hasQuery ? 'Hasil pencarian: '.$q : 'Cari Informasi PPID').' — '.config('ppid.name'))
@section('meta_description', 'Cari layanan, halaman, dan dokumen publik PPID Poltekkes Kemenkes Banten.')

@section('content')
<!-- PPID-SEARCH-PAGE-DEPLOY-20260907-A -->
<div class="ppid-search-page">
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-10">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-navy" aria-current="page">Pencarian</li>
                </ol>
            </nav>
            <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">
                {{ $hasQuery ? 'Hasil Pencarian' : 'Cari Informasi PPID' }}
            </h1>
            @if ($hasQuery)
                <p class="mt-2 text-muted">Hasil pencarian untuk “{{ $q }}”</p>
            @else
                <p class="mt-2 text-muted">Masukkan kata kunci untuk mencari layanan, halaman, dan dokumen publik.</p>
            @endif

            <form method="GET" action="{{ route('search') }}" class="ppid-search-form" role="search">
                <label class="sr-only" for="q">Cari informasi, layanan, atau dokumen PPID</label>
                <span class="ppid-search-form-icon" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                </span>
                <input
                    id="q"
                    name="q"
                    value="{{ $q }}"
                    type="search"
                    maxlength="120"
                    placeholder="Cari informasi, layanan, atau dokumen PPID..."
                    class="ppid-search-form-input"
                >
                <button type="submit" class="ppid-search-form-submit">Cari</button>
            </form>
        </div>
    </div>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-10">
        @if (! $hasQuery)
            <p class="text-sm leading-relaxed text-muted">
                Cari berdasarkan nama dokumen, layanan, tahun, atau kata kunci.
                Contoh: “LAKIN”, “RSB”, “Akreditasi”, “Keberatan”.
            </p>
        @else
            <p class="mb-6 text-sm text-muted">{{ $total }} hasil ditemukan</p>

            @forelse ($groups as $group)
                <section class="ppid-search-group" aria-labelledby="search-group-{{ $group['key'] }}">
                    <h2 id="search-group-{{ $group['key'] }}" class="ppid-search-group-title">{{ $group['label'] }}</h2>
                    <ul class="ppid-search-list">
                        @foreach ($group['items'] as $item)
                            <li class="ppid-search-row">
                                @if (! empty($item['url']) && ! empty($item['available']))
                                    <a
                                        href="{{ $item['url'] }}"
                                        class="ppid-search-row-link"
                                        @if (! empty($item['external']))
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        @endif
                                    >
                                        @include('search.partials.result-body', ['item' => $item])
                                    </a>
                                @else
                                    <div class="ppid-search-row-static">
                                        @include('search.partials.result-body', ['item' => $item])
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @empty
                <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">
                    Tidak ada hasil untuk “{{ $q }}”.
                </p>
            @endforelse
        @endif
    </div>
</div>
@endsection
