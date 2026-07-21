@extends('admin.layouts.app')
@section('title', 'Tambah Halaman')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">Tambah Halaman</h1>
<form method="POST" action="{{ route('admin.pages.store') }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @include('admin.pages._form')
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
