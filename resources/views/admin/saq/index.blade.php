@extends('admin.layouts.app')
@section('title', 'SAQ Monev 2026')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">SAQ Monev {{ $year }}</h1>
        <p class="text-sm text-muted">Kelengkapan dihitung dari data dukung, bukan angka tetap.</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Buat halaman</a>
</div>

<div class="mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ([
        'total' => 'Total',
        'missing' => 'Belum diisi',
        'draft' => 'Draft',
        'needs_review' => 'Perlu review',
        'complete' => 'Lengkap',
        'published' => 'Terbit',
    ] as $key => $label)
        <div class="rounded-xl border border-line bg-white p-4">
            <p class="text-xs uppercase text-muted">{{ $label }}</p>
            <p class="font-heading text-2xl font-bold text-navy">{{ $counts[$key] }}</p>
        </div>
    @endforeach
    <div class="rounded-xl border border-line bg-white p-4 sm:col-span-2">
        <p class="text-xs uppercase text-muted">Progres</p>
        <p class="font-heading text-2xl font-bold text-navy">{{ $counts['progress'] }}%</p>
    </div>
</div>

<div class="mb-4 flex flex-wrap gap-2 text-sm">
    @foreach ([
        'all' => 'Semua',
        'incomplete' => 'Belum lengkap',
        'no-evidence' => 'Tidak ada data dukung',
        'draft' => 'Draft',
        'needs_review' => 'Perlu review',
        'published' => 'Sudah dipublikasikan',
    ] as $key => $label)
        <a href="{{ route('admin.saq.index', ['filter' => $key, 'year' => $year]) }}" class="rounded-full px-3 py-1 {{ $filter === $key ? 'bg-navy text-white' : 'bg-white text-navy' }}">{{ $label }}</a>
    @endforeach
</div>

<div class="mb-6 grid gap-3 md:grid-cols-3">
    @foreach ($sections as $name => $section)
        <div class="rounded-xl border border-line bg-white p-4 text-sm">
            <p class="font-semibold text-navy">{{ $name }}</p>
            <p class="text-muted">{{ $section['done'] }}/{{ $section['total'] }}</p>
        </div>
    @endforeach
</div>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Kode</th>
                <th class="px-4 py-3">Indikator</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Bukti</th>
                <th class="px-4 py-3">URL publik</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($filtered as $row)
                @php
                    $status = $row['assessment']['status'];
                    $badge = match ($status) {
                        'published' => 'bg-navy text-white',
                        'complete' => 'bg-green text-white',
                        'needs_review' => 'bg-amber-400 text-navy-dark',
                        'draft' => 'border border-line bg-white text-navy',
                        default => 'bg-amber-100 text-navy',
                    };
                @endphp
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-semibold text-navy">{{ $row['item']->code }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.saq.show', $row['item']) }}" class="font-medium text-navy hover:underline">{{ $row['item']->question }}</a>
                        <p class="text-xs text-muted">{{ $row['item']->section }}</p>
                    </td>
                    <td class="px-4 py-3"><span class="rounded-full px-2 py-1 text-xs font-semibold {{ $badge }}">{{ $status }}</span></td>
                    <td class="px-4 py-3">{{ $row['assessment']['evidence_count'] }}</td>
                    <td class="px-4 py-3">
                        @if ($row['assessment']['public_url'])
                            <input readonly value="{{ $row['assessment']['public_url'] }}" class="w-56 rounded border border-line px-2 py-1 text-xs" onclick="this.select()">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-muted">Tidak ada indikator pada filter ini. Jalankan SaqItemSeeder bila registry masih kosong.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($recent->isNotEmpty())
    <h2 class="font-heading mb-2 mt-8 text-lg font-bold text-navy">Data dukung terbaru</h2>
    <ul class="space-y-1 text-sm text-muted">
        @foreach ($recent as $evidence)
            <li>{{ $evidence->item?->code }} — {{ $evidence->title ?: $evidence->evidence_type }}</li>
        @endforeach
    </ul>
@endif
@endsection
