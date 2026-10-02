@extends('admin.layouts.app')
@section('title', isset($document->id) ? 'Edit Dokumen' : 'Tambah Dokumen')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($document->id) ? 'Lengkapi / Edit' : 'Tambah' }} Dokumen</h1>

@if (isset($document->id) && ! $document->isAvailable())
    <div class="mb-4 rounded-md border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-950" role="alert">
        <strong>WAJIB UNGGAH</strong> — Dokumen belum tersedia. Unggah PDF, isi URL eksternal, atau pilih halaman internal.
    </div>
@endif

<form method="POST" enctype="multipart/form-data" action="{{ isset($document->id) ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5" x-data="{ source: '{{ old('source_type', $document->resolveSourceType()) }}' }">
    @csrf
    @if(isset($document->id)) @method('PUT') @endif

    <x-admin.input label="Nama Dokumen" name="title" :value="$document->title" required />
    <x-admin.input label="Slug" name="slug" :value="$document->slug" />

    <div class="grid gap-4 sm:grid-cols-2">
        <x-admin.select label="Kategori dokumen" name="document_category_id" :value="$document->document_category_id" placeholder="— Pilih —" :options="$documentCategories->pluck('name','id')->all()" />
        <x-admin.select label="Jenis informasi" name="public_information_category_id" :value="$document->public_information_category_id" placeholder="— Pilih —" :options="$informationCategories->pluck('name','id')->all()" />
    </div>

    <x-admin.input label="Kelompok / Section" name="section_title" :value="$document->section_title" help="Contoh: Laporan Kinerja (LAKIN) Poltekkes Kemenkes Banten" />
    <x-admin.textarea label="Keterangan" name="description" :value="$document->description" />

    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.input label="Tahun" name="year" type="number" :value="$document->year" />
        <x-admin.select label="Status publikasi" name="status" :value="$document->status" :options="['draft'=>'Draft','published'=>'Published']" required />
        <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($document->published_at)->format('Y-m-d\TH:i')" />
    </div>

    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$document->sort_order ?? 0" />

    <div>
        <label class="mb-1 block text-sm font-medium text-navy">Jenis Sumber</label>
        <select name="source_type" x-model="source" class="w-full rounded-md border border-line px-3 py-2 text-sm" required>
            <option value="uploaded_file">Upload PDF</option>
            <option value="external_url">URL Eksternal</option>
            <option value="internal_page">Halaman Internal</option>
        </select>
    </div>

    <div x-show="source === 'uploaded_file'" x-cloak class="space-y-1">
        <label class="block text-sm font-medium text-navy">File PDF</label>
        <input type="file" name="file" accept="application/pdf,.pdf" class="block w-full text-sm">
        @if($document->file_path)
            <p class="text-xs text-muted">File saat ini: {{ $document->file_name }}</p>
        @endif
        @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div x-show="source === 'external_url'" x-cloak>
        <x-admin.input label="URL Eksternal" name="external_url" type="url" :value="$document->external_url" help="Hanya http/https." />
    </div>

    <div x-show="source === 'internal_page'" x-cloak>
        <label class="mb-1 block text-sm font-medium text-navy">Halaman Internal</label>
        <select name="internal_url" class="w-full rounded-md border border-line px-3 py-2 text-sm">
            <option value="">— Pilih halaman —</option>
            @php $currentInternal = old('internal_url', $document->internal_url); @endphp
            @if (filled($currentInternal) && ! $pages->contains(fn ($page) => '/halaman/'.$page->slug === $currentInternal))
                <option value="{{ $currentInternal }}" selected>Path dokumen saat ini</option>
            @endif
            @foreach ($pages as $page)
                @php $path = '/halaman/'.$page->slug; @endphp
                <option value="{{ $path }}" @selected($currentInternal === $path)>{{ $page->title }}</option>
            @endforeach
        </select>
        @if (isset($document->id) && $document->publicHref())
            <a href="{{ $document->publicHref() }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-sm font-semibold text-green hover:underline">Lihat dokumen saat ini</a>
        @endif
        @error('internal_url')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-wrap gap-3">
        <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
        @if(isset($document->id) && $document->file_path)
            <button
                form="remove-file-form"
                type="submit"
                class="rounded-md border border-amber-400 px-4 py-2 text-sm font-semibold text-amber-900"
                onclick="return confirm('Hapus file dan tandai dokumen belum tersedia?')"
            >
                Hapus File / Tandai Belum Tersedia
            </button>
        @endif
    </div>
</form>

@if(isset($document->id) && $document->file_path)
    <form id="remove-file-form" method="POST" action="{{ route('admin.documents.remove-file', $document) }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endif
@endsection
