@extends('admin.layouts.app')
@section('title', isset($faq->id) ? 'Edit FAQ' : 'Tambah FAQ')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($faq->id) ? 'Edit' : 'Tambah' }} FAQ</h1>
<form method="POST" action="{{ isset($faq->id) ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($faq->id)) @method('PUT') @endif
    <x-admin.input label="Pertanyaan" name="question" :value="$faq->question" required />
    <x-admin.input label="Slug" name="slug" :value="$faq->slug" />
    <x-admin.textarea label="Jawaban" name="answer" :value="$faq->answer" :rows="6" required />
    <x-admin.input label="Urutan" name="sort_order" type="number" :value="$faq->sort_order ?? 0" />
    <x-admin.checkbox name="is_active" label="Aktif" :checked="$faq->is_active" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
