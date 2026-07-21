@extends('admin.layouts.app')
@section('title', isset($document->id) ? 'Edit Dokumen' : 'Tambah Dokumen')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($document->id) ? 'Edit' : 'Tambah' }} Dokumen</h1>
<form method="POST" enctype="multipart/form-data" action="{{ isset($document->id) ? route('admin.documents.update', $document) : route('admin.documents.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($document->id)) @method('PUT') @endif
    <x-admin.input label="Judul" name="title" :value="$document->title" required />
    <x-admin.input label="Slug" name="slug" :value="$document->slug" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-admin.select label="Kategori dokumen" name="document_category_id" :value="$document->document_category_id" placeholder="— Pilih —" :options="$documentCategories->pluck('name','id')->all()" />
        <x-admin.select label="Jenis informasi" name="public_information_category_id" :value="$document->public_information_category_id" placeholder="— Pilih —" :options="$informationCategories->pluck('name','id')->all()" />
    </div>
    <x-admin.textarea label="Deskripsi" name="description" :value="$document->description" />
    <div class="grid gap-4 sm:grid-cols-3">
        <x-admin.input label="Tahun" name="year" type="number" :value="$document->year" />
        <x-admin.select label="Status" name="status" :value="$document->status" :options="['draft'=>'Draft','published'=>'Published']" required />
        <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($document->published_at)->format('Y-m-d\TH:i')" />
    </div>
    <x-admin.input label="URL eksternal / Google Drive" name="external_url" type="url" :value="$document->external_url" help="Opsional jika tidak mengunggah file." />
    <x-admin.checkbox name="is_external" label="Utamakan tautan eksternal" :checked="$document->is_external" />
    <div class="space-y-1">
        <label class="block text-sm font-medium text-navy">Unggah berkas</label>
        <input type="file" name="file" class="block w-full text-sm" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip">
        @if($document->file_path)
            <p class="text-xs text-muted">File saat ini: {{ $document->file_name }}</p>
        @endif
        @error('file')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        @error('external_url')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
    </div>
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
