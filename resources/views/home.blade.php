@extends('layouts.app')

@section('title', config('ppid.name').' — Layanan Informasi Publik')
@section('meta_description', 'Layanan informasi publik yang cepat, tepat, transparan, akuntabel, dan mudah diakses.')

@section('content')
    @php
        $homeServiceOrder = [
            'permohonan-informasi',
            'pengajuan-keberatan',
            'pengaduan-penyalahgunaan-wewenang',
            'keluhan-pelayanan',
            'surat-keterangan',
            'alur-permohonan-informasi',
        ];
        $homeServices = collect($services ?? [])
            ->filter(fn ($service) => in_array($service['slug'] ?? null, $homeServiceOrder, true))
            ->sortBy(fn ($service) => array_search($service['slug'], $homeServiceOrder, true))
            ->values();
    @endphp

    <x-home.hero />
    <x-home.categories :categories="$categories" />
    <x-home.services :services="$homeServices" />
    <x-home.statistics :statistics="$statistics" />
@endsection
