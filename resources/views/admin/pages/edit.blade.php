@extends('admin.layouts.app')
@section('title', 'Edit Halaman')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">Edit Halaman</h1>
<form method="POST" action="{{ route('admin.pages.update', $page) }}" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf @method('PUT')
    @include('admin.pages._form')
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Perbarui</button>
</form>
@endsection
