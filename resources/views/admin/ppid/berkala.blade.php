@extends('admin.layouts.app')

@section('title', 'Informasi Berkala')

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="font-heading text-2xl font-bold text-navy">Informasi Berkala</h1>
            <p class="mt-1 text-sm text-muted">Kelola dokumen katalog Informasi Berkala (publik: /informasi-publik/berkala).</p>
        </div>
        <a href="{{ route('admin.ppid.dashboard') }}" class="text-sm font-semibold text-green hover:underline">← Dashboard PPID</a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-md border border-green/30 bg-green/5 px-4 py-3 text-sm text-navy" role="status">{{ session('status') }}</div>
    @endif

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-line bg-white p-4">
            <p class="text-xs font-semibold uppercase text-muted">Total Dokumen</p>
            <p class="font-heading mt-1 text-2xl font-bold text-navy">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <p class="text-xs font-semibold uppercase text-muted">Dokumen Tersedia</p>
            <p class="font-heading mt-1 text-2xl font-bold text-navy">{{ $stats['available'] }}</p>
        </div>
        <div class="rounded-xl border border-line bg-white p-4">
            <p class="text-xs font-semibold uppercase text-muted">Dokumen Belum Tersedia</p>
            <p class="font-heading mt-1 text-2xl font-bold text-navy">{{ $stats['missing'] }}</p>
        </div>
    </div>

    @if ($stats['missing'] > 0)
        <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-950" role="alert">
            Masih terdapat <strong>{{ $stats['missing'] }}</strong> dokumen Informasi Berkala yang wajib dilengkapi.
        </div>
    @endif

    @foreach ($sections as $section)
        <section class="mb-8 rounded-xl border border-line bg-white">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-heading text-lg font-bold text-navy">{{ $section['title'] }}</h2>
            </div>
            <ul class="divide-y divide-line">
                @foreach ($section['documents'] as $document)
                    @php $available = $document->isAvailable(); @endphp
                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                        <div>
                            <p class="font-medium text-navy">{{ $document->title }}</p>
                            <p class="mt-1 text-xs text-muted">
                                @if ($available)
                                    <span class="font-bold text-green">TERSEDIA</span>
                                @else
                                    <span class="font-bold text-amber-800">WAJIB UNGGAH</span>
                                    — Dokumen belum tersedia. Admin PPID wajib mengunggah file atau menambahkan URL dokumen.
                                @endif
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if ($available && $document->publicHref())
                                <a href="{{ $document->publicHref() }}" class="rounded-md border border-line px-3 py-1.5 text-xs font-semibold text-navy hover:border-green" @if($document->opensInNewTab()) target="_blank" rel="noopener noreferrer" @endif>Lihat</a>
                                <a href="{{ route('admin.documents.edit', $document) }}" class="rounded-md border border-line px-3 py-1.5 text-xs font-semibold text-navy hover:border-green">Edit</a>
                            @else
                                <a href="{{ route('admin.documents.edit', $document) }}" class="rounded-md bg-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-navy-dark">Upload Dokumen</a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endforeach
@endsection
