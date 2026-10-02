@extends('admin.layouts.app')
@section('title', 'Edit Profil Pimpinan')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">Edit Profil Pimpinan</h1>
<form method="POST" action="{{ route('admin.leadership-profiles.update', $profile) }}" enctype="multipart/form-data" class="max-w-3xl space-y-4 rounded-xl border border-line bg-white p-5">
    @method('PUT')
    @include('admin.leadership-profiles._form')
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
