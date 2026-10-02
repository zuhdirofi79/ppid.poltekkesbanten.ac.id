@extends('admin.layouts.app')
@section('title', 'Kalender Akademik')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">Kalender Akademik</h1>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('pages.show', 'kalender-akademik') }}" target="_blank" rel="noopener" class="rounded-md border border-line px-4 py-2 text-sm font-semibold text-navy">Lihat Halaman Publik</a>
        <a href="{{ route('admin.academic-calendars.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah Kalender</a>
    </div>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul..." class="rounded-md border border-line px-3 py-2 text-sm">
    <button class="rounded-md border border-line px-3 py-2 text-sm">Filter</button>
</form>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Tahun</th>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Dokumen</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Terakhir Diperbarui</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($calendars as $calendar)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-heading text-lg font-bold text-navy">{{ $calendar->year }}</td>
                    <td class="px-4 py-3 font-medium text-navy">{{ $calendar->title }}</td>
                    <td class="px-4 py-3">
                        @if ($calendar->isAvailable())
                            <span class="inline-flex items-center gap-1 font-semibold text-green">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $calendar->getFormatLabel() }}
                            </span>
                            @if ($calendar->getDocumentUrl())
                                <a href="{{ $calendar->getDocumentUrl() }}" target="_blank" rel="noopener" class="ml-2 text-xs text-green hover:underline">Preview</a>
                            @endif
                        @else
                            <span class="font-semibold text-amber-800">Belum ada dokumen</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($calendar->is_published)
                            <span class="inline-flex items-center rounded-full bg-green/10 px-2 py-0.5 text-xs font-semibold text-green">Published</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Draft</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-muted">{{ $calendar->updated_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.academic-calendars.edit', $calendar) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.academic-calendars.destroy', $calendar) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus kalender akademik tahun {{ $calendar->year }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-muted">Belum ada kalender akademik.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $calendars->links() }}</div>
@endsection
