@extends('layouts.app')
@section('title', 'Daftar Informasi Publik — '.config('ppid.name'))
@section('meta_description', 'Kategori informasi publik PPID Poltekkes Kemenkes Banten.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <h1 class="font-heading text-2xl font-bold text-navy">Daftar Informasi Publik</h1>
    </div>
</div>
<div class="mx-auto grid max-w-7xl gap-4 px-4 py-8 sm:grid-cols-2 lg:grid-cols-4">
    @foreach ($categories as $category)
        <a href="{{ route('information.category', $category->slug) }}" class="rounded-xl border border-line bg-white p-5 hover:border-green">
            <h2 class="font-heading font-bold text-navy">{{ $category->name }}</h2>
            <p class="mt-2 text-sm text-muted">{{ $category->excerpt }}</p>
        </a>
    @endforeach
</div>
@endsection
