@extends('admin.layouts.app')
@section('title', 'Dokumen Publik')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">Dokumen Publik</h1>
    <a href="{{ route('admin.documents.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah dokumen</a>
</div>
<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari..." class="rounded-md border border-line px-3 py-2 text-sm">
    <input type="number" name="year" value="{{ request('year') }}" placeholder="Tahun" class="w-28 rounded-md border border-line px-3 py-2 text-sm">
    <button class="rounded-md border border-line px-3 py-2 text-sm">Filter</button>
</form>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Tahun</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Unduhan</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($documents as $document)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $document->title }}</td>
                    <td class="px-4 py-3">{{ $document->year ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $document->status }}</td>
                    <td class="px-4 py-3">{{ $document->download_count }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.documents.edit', $document) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus dokumen?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-muted">Belum ada dokumen.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $documents->links() }}</div>
@endsection
