@extends('admin.layouts.app')

@section('title', 'Dashboard Admin PPID')

@section('content')
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-navy">Dashboard Admin PPID</h1>
        <p class="mt-1 text-sm text-muted">Kelola dan lengkapi dokumen Informasi Publik PPID Poltekkes Kemenkes Banten.</p>
    </div>

    @if ($stats['missing'] > 0)
        <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-5" role="alert">
            <p class="font-heading text-sm font-bold uppercase tracking-wide text-amber-900">Perhatian — Dokumen Belum Lengkap</p>
            <p class="mt-2 text-sm text-amber-950">
                Masih terdapat <strong>{{ $stats['missing'] }}</strong> dokumen Informasi Berkala yang wajib dilengkapi.
            </p>
            <a href="{{ route('admin.informasi-publik.berkala', ['status' => 'missing']) }}" class="mt-4 inline-flex rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">
                Kelola Dokumen Sekarang
            </a>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['label' => 'Total Dokumen', 'value' => $stats['total']],
            ['label' => 'Tersedia', 'value' => $stats['available']],
            ['label' => 'Belum Tersedia', 'value' => $stats['missing']],
            ['label' => 'Wajib Dilengkapi', 'value' => $stats['required']],
        ] as $card)
            <div class="rounded-xl border border-line bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-muted">{{ $card['label'] }}</p>
                <p class="font-heading mt-2 text-3xl font-bold text-navy">{{ number_format($card['value'], 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-8 overflow-hidden rounded-xl border border-line bg-white">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line px-5 py-4">
            <h2 class="font-heading text-lg font-bold text-navy">Dokumen yang Wajib Dilengkapi</h2>
            <a href="{{ route('admin.informasi-publik.berkala') }}" class="text-sm font-semibold text-green hover:underline">Kelola Informasi Berkala →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-surface text-xs uppercase tracking-wide text-muted">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3">Kelompok</th>
                        <th class="px-4 py-3">Nama Dokumen</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">File/URL</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($missing as $index => $doc)
                        <tr class="border-t border-line">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">Informasi Berkala</td>
                            <td class="px-4 py-3">{{ $doc->section_title }}</td>
                            <td class="px-4 py-3 font-medium text-navy">{{ $doc->title }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-900">WAJIB UNGGAH</span>
                            </td>
                            <td class="px-4 py-3 text-muted">—</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.documents.edit', $doc) }}" class="rounded-md bg-navy px-3 py-1.5 text-xs font-semibold text-white hover:bg-navy-dark">Upload Dokumen</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-muted">Semua dokumen Informasi Berkala sudah tersedia.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($available->isNotEmpty())
        <div class="mt-8 overflow-hidden rounded-xl border border-line bg-white">
            <div class="border-b border-line px-5 py-4">
                <h2 class="font-heading text-lg font-bold text-navy">Dokumen Tersedia</h2>
            </div>
            <ul class="divide-y divide-line">
                @foreach ($available as $doc)
                    <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-3 text-sm">
                        <div>
                            <p class="font-medium text-navy">{{ $doc->title }}</p>
                            <p class="text-xs text-muted">{{ $doc->section_title }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-full bg-green/10 px-2.5 py-0.5 text-xs font-bold text-green">TERSEDIA</span>
                            @if ($doc->publicHref())
                                <a href="{{ $doc->publicHref() }}" class="text-xs font-semibold text-green hover:underline" @if($doc->opensInNewTab()) target="_blank" rel="noopener noreferrer" @endif>Lihat</a>
                            @endif
                            <a href="{{ route('admin.documents.edit', $doc) }}" class="text-xs font-semibold text-navy hover:underline">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
