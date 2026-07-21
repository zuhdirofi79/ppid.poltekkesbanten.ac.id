@extends('layouts.app')
@section('title', 'Dokumen Publik — '.config('ppid.name'))
@section('meta_description', 'Daftar dokumen publik PPID Poltekkes Kemenkes Banten. Filter kategori, jenis informasi, dan tahun.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">Dokumen Publik</h1>
        <p class="mt-2 text-muted">Cari dan unduh dokumen informasi publik.</p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-8">
    <form method="GET" class="mb-6 grid gap-3 rounded-xl border border-line bg-white p-4 sm:grid-cols-2 lg:grid-cols-5">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari dokumen..." class="rounded-md border border-line px-3 py-2 text-sm lg:col-span-2">
        <select name="category" class="rounded-md border border-line px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            @foreach ($documentCategories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="info" class="rounded-md border border-line px-3 py-2 text-sm">
            <option value="">Jenis informasi</option>
            @foreach ($informationCategories as $cat)
                <option value="{{ $cat->id }}" @selected(request('info') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <select name="year" class="w-full rounded-md border border-line px-3 py-2 text-sm">
                <option value="">Tahun</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                @endforeach
            </select>
            <select name="sort" class="w-full rounded-md border border-line px-3 py-2 text-sm">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Terbaru</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Terlama</option>
            </select>
        </div>
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white sm:col-span-2 lg:col-span-5 lg:w-auto lg:justify-self-start">Terapkan filter</button>
    </form>

    <div class="overflow-x-auto rounded-xl border border-line bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
                <tr>
                    <th class="px-4 py-3">Nama dokumen</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">Tahun</th>
                    <th class="px-4 py-3">Format</th>
                    <th class="px-4 py-3">Ukuran</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr class="border-b border-line/70">
                        <td class="px-4 py-3 font-medium text-navy">
                            <a href="{{ route('documents.show', $document->slug) }}" class="hover:text-green">{{ $document->title }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $document->documentCategory?->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $document->year ?? '—' }}</td>
                        <td class="px-4 py-3 uppercase">{{ $document->file_type ?? ($document->is_external ? 'URL' : '—') }}</td>
                        <td class="px-4 py-3">
                            @if ($document->file_size)
                                {{ number_format($document->file_size / 1024, 1, ',', '.') }} KB
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('documents.show', $document->slug) }}" class="text-green hover:underline">Lihat</a>
                            <a href="{{ route('documents.download', $document->slug) }}" class="ml-2 font-semibold text-navy hover:underline">Unduh</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-muted">Tidak ada dokumen yang cocok.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $documents->links() }}</div>
</div>
@endsection
