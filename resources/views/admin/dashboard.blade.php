@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-navy">Dashboard</h1>
        <p class="mt-1 text-sm text-muted">Ringkasan konten PPID. Modul CRUD penuh dilanjutkan di FASE 4.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Berita', 'value' => $stats['posts']],
            ['label' => 'Pengumuman', 'value' => $stats['announcements']],
            ['label' => 'Dokumen', 'value' => $stats['documents']],
            ['label' => 'Halaman', 'value' => $stats['pages']],
            ['label' => 'FAQ', 'value' => $stats['faqs']],
            ['label' => 'Unit PPID', 'value' => $stats['units']],
            ['label' => 'Pengguna', 'value' => $stats['users']],
        ] as $card)
            <div class="rounded-xl border border-line bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $card['label'] }}</p>
                <p class="font-heading mt-2 text-3xl font-bold text-navy">{{ number_format($card['value'], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 rounded-xl border border-dashed border-line bg-white p-5 text-sm text-muted">
        <p class="font-medium text-navy">Status otorisasi</p>
        <p class="mt-2">
            Role Anda: <strong>{{ auth()->user()->role?->label() }}</strong>
            ({{ auth()->user()->role?->value }}).
            Izin divalidasi di middleware server-side, bukan hanya UI.
        </p>
        <ul class="mt-3 list-disc space-y-1 pl-5">
            @foreach (auth()->user()->role?->abilities() ?? [] as $ability)
                <li><code>{{ $ability }}</code></li>
            @endforeach
        </ul>
    </div>
@endsection
