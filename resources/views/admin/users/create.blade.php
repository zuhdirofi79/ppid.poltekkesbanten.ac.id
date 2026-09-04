@extends('admin.layouts.app')
@section('title', isset($user->id) ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('content')
<h1 class="font-heading mb-6 text-2xl font-bold text-navy">{{ isset($user->id) ? 'Edit' : 'Tambah' }} Pengguna</h1>
<form method="POST" action="{{ isset($user->id) ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-xl space-y-4 rounded-xl border border-line bg-white p-5">
    @csrf
    @if(isset($user->id)) @method('PUT') @endif
    <x-admin.input label="Nama" name="name" :value="$user->name" required />
    <x-admin.input label="Email" name="email" type="email" :value="$user->email" required />
    <x-admin.input label="Username" name="username" :value="$user->username" help="Opsional. Digunakan untuk login alternatif." />
    <x-admin.select label="Role" name="role" :value="$user->role?->value" :options="collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->label()])->all()" required />
    <x-admin.input label="Kata sandi" name="password" type="password" :required="!isset($user->id)" help="{{ isset($user->id) ? 'Kosongkan jika tidak diubah.' : '' }}" />
    <x-admin.input label="Konfirmasi kata sandi" name="password_confirmation" type="password" :required="!isset($user->id)" />
    <x-admin.checkbox name="is_active" label="Aktif" :checked="$user->is_active ?? true" />
    <button class="rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white">Simpan</button>
</form>
@endsection
