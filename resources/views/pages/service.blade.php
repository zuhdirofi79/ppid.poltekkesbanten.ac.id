@extends('layouts.app')

@php
    $serviceSlug = (string) ($service['slug'] ?? '');
    $documentTitle = match ($serviceSlug) {
        'alur-pengajuan-keberatan' => 'Alur Pengajuan Keberatan | '.config('ppid.name'),
        'alur-pengajuan-sengketa' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran | '.config('ppid.name'),
        default => $title.' — '.config('ppid.name'),
    };
    $metaDescription = match ($serviceSlug) {
        'alur-pengajuan-keberatan' => 'Tahapan dan tata cara pengajuan keberatan atas layanan informasi publik PPID Poltekkes Kemenkes Banten.',
        'alur-pengajuan-sengketa' => 'Informasi tata cara pengaduan dugaan penyalahgunaan wewenang, pelanggaran, pungutan liar, konflik kepentingan, gratifikasi, dan pelanggaran pelayanan publik di lingkungan Poltekkes Kemenkes Banten.',
        default => $description ?? config('ppid.name_long'),
    };
@endphp

@section('title', $documentTitle)
@section('meta_description', $metaDescription)
@if (in_array($serviceSlug, ['alur-pengajuan-keberatan', 'alur-pengajuan-sengketa'], true))
    @section('og_title', $documentTitle)
    @section('og_description', $metaDescription)
@endif

@section('content')
    <div class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                    <li aria-hidden="true">/</li>
                    @if ($serviceSlug === 'daftar-registrasi-pemohon')
                        <li>SOP & Layanan</li>
                    @else
                        <li><a href="{{ route('home') }}#layanan-heading" class="hover:text-green">Layanan</a></li>
                    @endif
                    <li aria-hidden="true">/</li>
                    <li class="text-navy" aria-current="page">{{ $title }}</li>
                </ol>
            </nav>
            @if ($serviceSlug === 'alur-pengajuan-sengketa')
                <p class="text-sm font-semibold uppercase tracking-wider text-green">Layanan PPID</p>
            @elseif ($serviceSlug === 'daftar-registrasi-pemohon')
                <p class="text-sm font-semibold uppercase tracking-wider text-green">SOP & Layanan</p>
            @endif
            <h1 class="font-heading text-2xl font-bold leading-tight text-navy sm:text-3xl">{{ $title }}</h1>
            @if (! empty($description))
                <p class="mt-2 max-w-3xl text-muted">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if (($service['slug'] ?? '') === 'permohonan-informasi')
        @include('pages.partials.permohonan-informasi', [
            'formUrl' => $formUrl ?? null,
            'ppidEmail' => $ppidEmail ?? 'humas@poltekkesbanten.ac.id',
        ])
    @elseif (($service['slug'] ?? '') === 'pengajuan-keberatan')
        @include('pages.partials.pengajuan-keberatan', [
            'formUrl' => $formUrl ?? null,
        ])
    @elseif (($service['slug'] ?? '') === 'alur-permohonan-informasi')
        @include('pages.partials.alur-permohonan-informasi')
    @elseif (($service['slug'] ?? '') === 'alur-pengajuan-keberatan')
        @include('pages.partials.alur-pengajuan-keberatan')
    @elseif (($service['slug'] ?? '') === 'alur-pengajuan-sengketa')
        @include('pages.partials.alur-pengajuan-sengketa', [
            'complaintFormUrl' => $complaintFormUrl ?? null,
        ])
    @elseif (($service['slug'] ?? '') === 'daftar-registrasi-pemohon')
        @include('pages.partials.daftar-registrasi-pemohon')
    @else
        <div class="mx-auto max-w-3xl px-4 py-10">
            <div class="rounded-xl border border-line bg-white p-6 shadow-sm">
                <p class="text-sm leading-relaxed text-ink">
                    {{ $service['excerpt'] ?? 'Layanan ini dikelola oleh PPID Poltekkes Kemenkes Banten.' }}
                </p>

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
    @endif
@endsection
