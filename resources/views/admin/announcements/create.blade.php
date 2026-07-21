@extends('admin.layouts.app')
@section('title', isset($announcement->id) ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($announcement->id) ? 'Edit' : 'Tambah' }} Pengumuman</h1>
<form method="POST" action="{{ isset($announcement->id) ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($announcement->id)) @method('PUT') @endif
    <x-admin.input label="Judul" name="title" :value="$announcement->title" required />
    <x-admin.input label="Slug" name="slug" :value="$announcement->slug" />
    <x-admin.textarea label="Ringkasan" name="excerpt" :value="$announcement->excerpt" :rows="2" />
    <x-admin.textarea label="Isi" name="body" :value="$announcement->body" :rows="8" />
    <div class="grid gap-4 sm:grid-cols-2">
        <x-admin.select label="Status" name="status" :value="$announcement->status" :options="['draft'=>'Draft','published'=>'Published']" required />
        <x-admin.input label="Tanggal terbit" name="published_at" type="datetime-local" :value="optional($announcement->published_at)->format('Y-m-d\TH:i')" />
        <x-admin.input label="Mulai" name="starts_at" type="datetime-local" :value="optional($announcement->starts_at)->format('Y-m-d\TH:i')" />
        <x-admin.input label="Selesai" name="ends_at" type="datetime-local" :value="optional($announcement->ends_at)->format('Y-m-d\TH:i')" />
    </div>
    <x-admin.checkbox name="is_pinned" label="Pinned" :checked="$announcement->is_pinned" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
