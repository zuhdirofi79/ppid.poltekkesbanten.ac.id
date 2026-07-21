@extends('admin.layouts.app')
@section('title', 'Berita')
@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="font-heading text-2xl font-bold text-navy">Berita</h1>
        <p class="text-sm text-muted">Kelola berita PPID.</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">Tambah berita</a>
</div>
<form method="GET" class="mb-4">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berita..." class="w-full max-w-sm rounded-md border border-line px-3 py-2 text-sm">
</form>
<div class="overflow-x-auto rounded-xl border border-line bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-line bg-surface text-xs uppercase text-muted">
            <tr>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($posts as $post)
                <tr class="border-b border-line/70">
                    <td class="px-4 py-3 font-medium text-navy">{{ $post->title }}</td>
                    <td class="px-4 py-3">{{ $post->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $post->status }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="text-green hover:underline">Edit</a>
                        <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="ml-3 inline" onsubmit="return confirm('Hapus berita ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada berita.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $posts->links() }}</div>
@endsection
