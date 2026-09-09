<!-- PPID-PROTECTED-ADMIN-MANAGEMENT-DEPLOY-20260909-A -->
@extends('admin.layouts.app')

@section('title', isset($user->id) ? 'Edit Admin PPID' : 'Tambah Admin PPID')

@section('content')
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-navy">{{ isset($user->id) ? 'Edit Admin PPID' : 'Tambah Admin PPID' }}</h1>
        <p class="mt-1 text-sm text-muted">Role ditetapkan otomatis sebagai Admin PPID. Password tidak disimpan dalam bentuk teks biasa.</p>
    </div>

    <form
        method="POST"
        action="{{ isset($user->id) ? route('admin.ppid.admins.update', $user) : route('admin.ppid.admins.store') }}"
        class="max-w-xl space-y-4 rounded-xl border border-line bg-white p-5"
    >
        @csrf
        @if (isset($user->id))
            @method('PUT')
        @endif

        <x-admin.input label="Nama" name="name" :value="old('name', $user->name)" required />
        <x-admin.input label="Username" name="username" :value="old('username', $user->username)" required help="3–64 karakter: huruf, angka, titik, underscore, atau strip." />
        <x-admin.input label="Email" name="email" type="email" :value="old('email', $user->email)" required />
        <x-admin.input label="Password" name="password" type="password" :required="! isset($user->id)" help="{{ isset($user->id) ? 'Kosongkan jika tidak diubah.' : '' }}" />
        <x-admin.input label="Konfirmasi Password" name="password_confirmation" type="password" :required="! isset($user->id)" />

        <div class="flex gap-3">
            <button type="submit" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark">Simpan</button>
            <a href="{{ route('admin.ppid.admins.index') }}" class="rounded-md border border-line px-4 py-2 text-sm font-semibold text-navy">Batal</a>
        </div>
    </form>
@endsection
