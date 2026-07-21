@extends('admin.layouts.app')
@section('title', 'Pengumuman')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="font-heading text-2xl font-bold text-navy">Pengumuman</h1>
    <a href="{{ route('admin.announcements.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Tambah</a>
</div>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr><th class="px-4 py-3">Judul</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Pinned</th><th class="px-4 py-3"></th></tr>
        </thead>
        <tbody>
            @forelse ($announcements as $item)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $item->title }}</td>
                    <td class="px-4 py-3">{{ $item->status }}</td>
                    <td class="px-4 py-3">{{ $item->is_pinned ? 'Ya' : 'Tidak' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.announcements.edit', $item) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.announcements.destroy', $item) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600">Hapus</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada pengumuman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $announcements->links() }}</div>
@endsection
