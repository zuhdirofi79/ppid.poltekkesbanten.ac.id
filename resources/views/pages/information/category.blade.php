@extends('layouts.app')
@section('title', $category->name.' — '.config('ppid.name'))
@section('meta_description', $category->excerpt ?? config('ppid.name_long'))

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li>/</li>
                <li><a href="{{ route('information.index') }}" class="hover:text-green">Informasi Publik</a></li>
                <li>/</li>
                <li class="text-navy" aria-current="page">{{ $pageTitle ?? $category->name }}</li>
            </ol>
        </nav>
        <h1 class="font-heading text-2xl font-bold text-navy">{{ $pageTitle ?? $category->name }}</h1>
        <p class="mt-2 max-w-3xl text-muted">{{ $pageExcerpt ?? $category->excerpt }}</p>
    </div>
</div>
<div class="mx-auto max-w-7xl px-4 py-8">
    @if (! empty($excludedInformation))
        @include('pages.information.partials.informasi-dikecualikan', ['excluded' => $excludedInformation])
    @endif

    @if (! empty($procurementAccess))
        @include('pages.information.partials.dokumen-pengadaan-barang-jasa', ['access' => $procurementAccess])
    @endif

    @if (! empty($bmnInventory))
        @include('pages.information.partials.inventaris-barang-milik-negara', ['access' => $bmnInventory])
    @endif

    @if (($staffingChartGroups ?? collect())->isNotEmpty())
        @include('pages.information.partials.statistik-kepegawaian', ['groups' => $staffingChartGroups])
    @endif

    @if ($documents->isNotEmpty())
        <ul class="space-y-3">
            @foreach ($documents as $document)
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line bg-white p-4">
                    <a href="{{ route('documents.show', $document->slug) }}" class="font-medium text-navy hover:text-green">{{ $document->title }}</a>
                    <a href="{{ route('documents.download', $document->slug) }}" class="text-sm font-semibold text-green">Unduh</a>
                </li>
            @endforeach
        </ul>
        <div class="mt-4">{{ $documents->links() }}</div>
    @elseif (($staffingChartGroups ?? collect())->isEmpty() && empty($procurementAccess) && empty($bmnInventory) && empty($excludedInformation))
        <p class="rounded-xl border border-dashed border-line p-6 text-sm text-muted">Belum ada dokumen pada kategori ini.</p>
    @endif
</div>
@endsection
