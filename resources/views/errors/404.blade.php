@extends('layouts.app')

@section('title', 'Halaman tidak ditemukan — '.config('ppid.name'))

@section('content')
<div class="mx-auto flex max-w-lg flex-col items-center px-4 py-24 text-center">
    <p class="font-heading text-6xl font-bold text-navy">404</p>
    <h1 class="font-heading mt-4 text-2xl font-bold text-navy">Halaman tidak ditemukan</h1>
    <p class="mt-2 text-muted">Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
    <a href="{{ route('home') }}" class="mt-6 rounded-md bg-navy px-4 py-2.5 text-sm font-semibold text-white">Kembali ke beranda</a>
</div>
@endsection
