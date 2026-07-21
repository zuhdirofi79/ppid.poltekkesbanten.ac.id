@extends('admin.layouts.app')
@section('title', isset($service->id) ? 'Edit Layanan' : 'Tambah Layanan')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($service->id) ? 'Edit' : 'Tambah' }} Layanan</h1>
<form method="POST" action="{{ isset($service->id) ? route('admin.services.update', $service) : route('admin.services.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($service->id)) @method('PUT') @endif
    <x-admin.input label="Judul" name="title" :value="$service->title" required />
    <x-admin.input label="Slug" name="slug" :value="$service->slug" />
    <x-admin.textarea label="Ringkasan" name="excerpt" :value="$service->excerpt" :rows="2" />
    <x-admin.textarea label="Deskripsi" name="description" :value="$service->description" />
    <x-admin.input label="URL eksternal / Google Form" name="external_url" type="url" :value="$service->external_url" />
    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$service->sort_order ?? 0" />
    <x-admin.checkbox name="is_featured" label="Tampilkan di shortcut" :checked="$service->is_featured" />
    <x-admin.checkbox name="is_active" label="Aktif" :checked="$service->is_active" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
