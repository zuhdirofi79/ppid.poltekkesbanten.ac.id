@extends('admin.layouts.app')
@section('title', 'Tambah Kalender Akademik')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.academic-calendars.index') }}" class="text-muted hover:text-navy">&larr;</a>
    <h1 class="font-heading text-2xl font-bold text-navy">Tambah Kalender Akademik</h1>
</div>

<form
    method="POST"
    enctype="multipart/form-data"
    action="{{ route('admin.academic-calendars.store') }}"
    class="max-w-2xl space-y-4 rounded-xl border border-line bg-white p-5"
    x-data="{ source: '{{ old('source_type', $calendar->source_type ?? 'uploaded_file') }}' }"
>
    @csrf

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label for="year" class="mb-1 block text-sm font-medium text-navy">Tahun <span class="text-red-600">*</span></label>
            <input
                type="number"
                id="year"
                name="year"
                value="{{ old('year', $calendar->year ?? date('Y')) }}"
                min="2020"
                max="2100"
                required
                class="w-full rounded-md border border-line px-3 py-2 text-sm @error('year') border-red-300 @enderror"
            >
            @error('year')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="sort_order" class="mb-1 block text-sm font-medium text-navy">Urutan</label>
            <input
                type="number"
                id="sort_order"
                name="sort_order"
                value="{{ old('sort_order', $calendar->sort_order ?? '') }}"
                min="0"
                max="9999"
                placeholder="Default: tahun"
                class="w-full rounded-md border border-line px-3 py-2 text-sm"
            >
        </div>
    </div>

    <div>
        <label for="title" class="mb-1 block text-sm font-medium text-navy">Judul <span class="text-red-600">*</span></label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $calendar->title ?? '') }}"
            required
            placeholder="Kalender Akademik Tahun 2026"
            class="w-full rounded-md border border-line px-3 py-2 text-sm @error('title') border-red-300 @enderror"
        >
        @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-navy">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="3"
            placeholder="Keterangan singkat tentang kalender akademik ini..."
            class="w-full rounded-md border border-line px-3 py-2 text-sm"
        >{{ old('description', $calendar->description ?? '') }}</textarea>
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-navy">Jenis Sumber Dokumen</label>
        <select name="source_type" x-model="source" class="w-full rounded-md border border-line px-3 py-2 text-sm" required>
            <option value="uploaded_file">Upload PDF Baru</option>
            <option value="public_asset">Asset Publik (sudah ada di folder docs/)</option>
            <option value="external_url">URL Eksternal</option>
        </select>
    </div>

    {{-- Upload File (PDF/PNG/JPG) --}}
    <div x-show="source === 'uploaded_file'" x-cloak class="space-y-1">
        <label class="block text-sm font-medium text-navy">File Kalender <span class="text-red-600">*</span></label>
        <input type="file" name="file" accept="application/pdf,.pdf,image/png,.png,image/jpeg,.jpg,.jpeg" class="block w-full text-sm">
        <p class="text-xs text-muted">Maksimal 20MB. Format: PDF, PNG, JPG, atau JPEG.</p>
        @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Public Asset --}}
    <div x-show="source === 'public_asset'" x-cloak>
        <label for="public_asset_path" class="mb-1 block text-sm font-medium text-navy">Path Asset Publik <span class="text-red-600">*</span></label>
        <input
            type="text"
            id="public_asset_path"
            name="public_asset_path"
            value="{{ old('public_asset_path', $calendar->public_asset_path ?? '') }}"
            placeholder="docs/Poltekkes Banten Academic Calendar 2026_2027.png"
            class="w-full rounded-md border border-line px-3 py-2 text-sm"
        >
        <p class="mt-1 text-xs text-muted">Path relatif dari folder public. Contoh: docs/Poltekkes Banten Academic Calendar 2026_2027.png</p>
        @error('public_asset_path')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- External URL --}}
    <div x-show="source === 'external_url'" x-cloak>
        <label for="external_url" class="mb-1 block text-sm font-medium text-navy">URL Eksternal <span class="text-red-600">*</span></label>
        <input
            type="url"
            id="external_url"
            name="external_url"
            value="{{ old('external_url', $calendar->external_url ?? '') }}"
            placeholder="https://example.com/kalender.pdf"
            class="w-full rounded-md border border-line px-3 py-2 text-sm"
        >
        <p class="mt-1 text-xs text-muted">URL harus http:// atau https://</p>
        @error('external_url')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="flex items-center gap-2 text-sm font-medium text-navy">
                <input
                    type="checkbox"
                    name="is_published"
                    value="1"
                    @checked(old('is_published', $calendar->is_published ?? true))
                    class="rounded border-line"
                >
                Publikasikan
            </label>
            <p class="mt-1 text-xs text-muted">Jika tidak dicentang, kalender tidak muncul di halaman publik.</p>
        </div>

        <div>
            <label for="published_at" class="mb-1 block text-sm font-medium text-navy">Tanggal Terbit</label>
            <input
                type="datetime-local"
                id="published_at"
                name="published_at"
                value="{{ old('published_at', optional($calendar->published_at ?? now())->format('Y-m-d\TH:i')) }}"
                class="w-full rounded-md border border-line px-3 py-2 text-sm"
            >
        </div>
    </div>

    <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">
            Simpan
        </button>
        <a href="{{ route('admin.academic-calendars.index') }}" class="rounded-md border border-line px-4 py-2 text-sm font-semibold text-navy hover:bg-surface">
            Batal
        </a>
    </div>
</form>
@endsection
