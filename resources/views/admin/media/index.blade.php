@extends('admin.layouts.app')
@section('title', 'Media Library')
@section('content')
<div class="mb-6">
    <h1 class="font-heading text-2xl font-bold text-navy">Media Library</h1>
    <p class="text-sm text-muted">PNG, JPG, PDF, DOC, dan DOCX. Berkas internal tidak punya URL publik.</p>
</div>

<form method="GET" class="mb-4 flex flex-wrap gap-2">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari berkas" class="rounded-md border border-line px-3 py-2 text-sm">
    <select name="type" class="rounded-md border border-line px-3 py-2 text-sm">
        <option value="">Semua jenis</option>
        @foreach (['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx'] as $type)
            <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
        @endforeach
    </select>
    <input type="number" name="year" value="{{ request('year') }}" placeholder="Tahun" class="w-28 rounded-md border border-line px-3 py-2 text-sm">
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Saring</button>
</form>

<form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="mb-8 grid gap-3 rounded-xl border border-line bg-white p-5 md:grid-cols-2">
    @csrf
    <h2 class="font-heading text-lg font-bold text-navy md:col-span-2">Unggah</h2>
    <input type="file" name="file" class="text-sm md:col-span-2">
    <input name="title" placeholder="Judul" class="rounded-md border border-line px-3 py-2 text-sm">
    <input name="alt_text" placeholder="Teks alternatif" class="rounded-md border border-line px-3 py-2 text-sm">
    <input name="caption" placeholder="Keterangan" class="rounded-md border border-line px-3 py-2 text-sm md:col-span-2">
    <select name="visibility" class="rounded-md border border-line px-3 py-2 text-sm">
        <option value="public">Publik</option>
        <option value="internal">Internal</option>
    </select>
    <select name="page_id" class="rounded-md border border-line px-3 py-2 text-sm">
        <option value="">Tanpa halaman</option>
        @foreach ($pages as $page)
            <option value="{{ $page->id }}">Lampirkan ke {{ $page->title }}</option>
        @endforeach
    </select>
    <p class="text-xs text-muted md:col-span-2">Pastikan dokumen tidak memuat data pribadi atau informasi yang dikecualikan sebelum memilih publik.</p>
    @error('file')<p class="text-sm text-red-600 md:col-span-2">{{ $message }}</p>@enderror
    <button class="w-fit rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan berkas</button>
</form>

<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    @forelse ($assets as $asset)
        <article class="rounded-xl border border-line bg-white p-4 text-sm">
            <p class="font-semibold text-navy">{{ $asset->title ?: $asset->original_name }}</p>
            <p class="text-xs text-muted">{{ strtoupper($asset->extension) }} · {{ $asset->visibility }} · {{ number_format($asset->size / 1024, 0) }} KB</p>
            @if ($asset->publicUrl())
                <input readonly value="{{ $asset->publicUrl() }}" class="mt-2 w-full rounded border border-line px-2 py-1 text-xs" onclick="this.select()">
            @else
                <p class="mt-2 text-xs text-muted">Tidak ada URL publik.</p>
            @endif
            <div class="mt-3 flex flex-wrap gap-3">
                <a href="{{ route('admin.media.download', $asset) }}" class="text-navy hover:underline">Buka</a>
                <form method="POST" action="{{ route('admin.media.destroy', $asset) }}" onsubmit="return confirm('Hapus berkas yang tidak sedang dipakai?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Hapus</button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.media.replace', $asset) }}" enctype="multipart/form-data" class="mt-3 space-y-2">
                @csrf
                <input type="hidden" name="visibility" value="{{ $asset->visibility }}">
                <input type="file" name="file" class="block w-full text-xs">
                <button class="text-navy">Ganti berkas</button>
            </form>
        </article>
    @empty
        <p class="text-muted">Pustaka masih kosong.</p>
    @endforelse
</div>

<div class="mt-4">{{ $assets->links() }}</div>
@endsection
