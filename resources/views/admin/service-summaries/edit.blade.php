@extends('admin.layouts.app')
@section('title', 'Edit Laporan Layanan Informasi')
@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.service-summaries.index') }}" class="text-muted hover:text-navy">&larr;</a>
    <h1 class="font-heading text-2xl font-bold text-navy">Edit Laporan Tahun {{ $summary->year }}</h1>
</div>
<form method="POST" action="{{ route('admin.service-summaries.update', $summary) }}" class="max-w-3xl rounded-xl border border-line bg-white p-5">
    @method('PUT')
    @include('admin.service-summaries._form')
</form>
@endsection
