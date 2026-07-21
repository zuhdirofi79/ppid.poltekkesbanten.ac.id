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
                    <li><a href="{{ route('home') }}#layanan-heading" class="hover:text-green">Layanan</a></li>
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

    <div class="mx-auto max-w-3xl px-4 py-10">
        <div class="rounded-xl border border-line bg-white p-6 shadow-sm">
            <p class="text-sm leading-relaxed text-ink">
                {{ $service['excerpt'] ?? 'Layanan ini dikelola oleh PPID Poltekkes Kemenkes Banten.' }}
            </p>

            <div class="mt-6 rounded-lg border border-dashed border-line bg-surface p-4 text-sm text-muted">
                <p class="font-medium text-navy">Formulir belum terhubung</p>
                <p class="mt-1">
                    Tautan Google Form / e-PPID dapat diisi pada
                    <code class="rounded bg-white px-1">config/ppid.php</code>
                    → <code class="rounded bg-white px-1">services.*.external_url</code>
                    (nanti diganti panel admin).
                </p>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('home') }}" class="font-heading rounded-md bg-navy px-4 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark">
                    Kembali ke beranda
                </a>
                <a href="{{ route('contact') }}" class="font-heading rounded-md border border-line px-4 py-2.5 text-sm font-semibold text-navy hover:border-green">
                    Hubungi PPID
                </a>
            </div>
        </div>
    </div>
@endsection
