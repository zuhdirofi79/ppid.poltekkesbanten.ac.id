@extends('layouts.app')

@section('title', $title.' — '.config('ppid.name'))
@section('meta_description', $description ?? config('ppid.name_long'))

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    <li class="text-navy" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
            <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">{{ $title }}</h1>
            @if (! empty($description))
                <p class="mt-2 max-w-3xl text-muted">{{ $description }}</p>
            @endif
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-10">
        <div class="rounded-xl border border-dashed border-line bg-surface/60 p-6 text-sm text-muted">
            <p class="font-medium text-navy">Halaman dalam pengembangan</p>
            <p class="mt-2">
                Konten penuh untuk <strong>{{ $title }}</strong> akan dikelola melalui panel admin pada fase berikutnya.
                Struktur navigasi dan routing sudah tersedia.
            </p>
        </div>
    </div>
@endsection
