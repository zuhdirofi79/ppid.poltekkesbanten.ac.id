@extends('admin.layouts.app')
@section('title', 'Halaman')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">Halaman</h1>
        <p class="text-sm text-muted">Kelola halaman profil dan konten statis.</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">Tambah halaman</a>
</div>

<form method="GET" class="mb-4">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul..." class="w-full max-w-sm rounded-md border border-line px-3 py-2 text-sm">
</form>

<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Terbit</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pages as $page)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $page->title }}</td>
                    <td class="px-4 py-3">{{ $page->status }}</td>
                    <td class="px-4 py-3 text-muted">{{ $page->published_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus halaman ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada halaman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pages->links() }}</div>
@endsection
