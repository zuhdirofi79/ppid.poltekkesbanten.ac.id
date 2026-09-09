@extends('admin.layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-navy">Profil Saya</h1>
        <p class="mt-1 text-sm text-muted">Ubah nama tampilan atau password akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" class="max-w-xl space-y-4 rounded-xl border border-line bg-white p-5">
        @csrf
        @method('PUT')
        <x-admin.input label="Nama" name="name" :value="old('name', $user->name)" required />
        <p class="text-sm text-muted">Email: <strong>{{ $user->email }}</strong>@if($user->username) · Username: <strong>{{ $user->username }}</strong>@endif</p>
        <x-admin.input label="Password baru" name="password" type="password" help="Kosongkan jika tidak diubah." />
        <x-admin.input label="Konfirmasi password baru" name="password_confirmation" type="password" />
        <button type="submit" class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
    </form>
@endsection
