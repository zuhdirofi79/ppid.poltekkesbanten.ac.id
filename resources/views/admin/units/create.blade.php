@extends('admin.layouts.app')
@section('title', isset($unit->id) ? 'Edit Unit' : 'Tambah Unit')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($unit->id) ? 'Edit' : 'Tambah' }} Unit</h1>
<form method="POST" action="{{ isset($unit->id) ? route('admin.units.update', $unit) : route('admin.units.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($unit->id)) @method('PUT') @endif
    <x-admin.input label="Nama" name="name" :value="$unit->name" required />
    <x-admin.input label="Slug" name="slug" :value="$unit->slug" />
    <x-admin.textarea label="Deskripsi" name="description" :value="$unit->description" />
    <x-admin.input label="URL (opsional)" name="url" type="url" :value="$unit->url" />
    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$unit->sort_order ?? 0" />
    <x-admin.checkbox name="is_active" label="Aktif" :checked="$unit->is_active" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
