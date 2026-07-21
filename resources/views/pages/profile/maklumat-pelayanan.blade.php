@extends('layouts.app')

@section('title', 'Maklumat Pelayanan | PPID Poltekkes Kemenkes Banten')
@section('meta_description', 'Maklumat Pelayanan PPID Poltekkes Kemenkes Banten sebagai bentuk komitmen penyelenggaraan pelayanan publik sesuai standar dan peraturan yang berlaku.')
@section('og_image', asset($imageWebp))
@section('canonical', route('profil.maklumat-pelayanan'))

@section('content')
@php
    $breadcrumbs = [
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Profil', 'url' => null],
        ['label' => 'Maklumat Pelayanan', 'url' => null],
    ];
@endphp

<div
    class="border-b border-line bg-surface"
    x-data="{
        visible: false,
        open() {
            this.visible = true;
            document.documentElement.style.overflow = 'hidden';
        },
        close() {
            this.visible = false;
            document.documentElement.style.overflow = '';
            this.$nextTick(() => this.$refs.trigger?.focus());
        }
    }"
    @keydown.escape.window="close()"
>
    <div class="mx-auto max-w-7xl px-4 py-8">
        <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-green">Beranda</a></li>
                <li aria-hidden="true">/</li>
                <li>Profil</li>
                <li aria-hidden="true">/</li>
                <li class="text-navy" aria-current="page">Maklumat Pelayanan</li>
            </ol>
        </nav>
        <h1 class="font-heading text-2xl font-bold text-navy sm:text-3xl">Maklumat Pelayanan</h1>
        <p class="mt-3 max-w-3xl text-sm leading-relaxed text-muted sm:text-base">
            Maklumat Pelayanan merupakan pernyataan kesanggupan Poltekkes Kemenkes Banten dalam menyelenggarakan pelayanan sesuai standar yang telah ditetapkan, melakukan perbaikan secara berkelanjutan, serta menerima konsekuensi sesuai ketentuan apabila pelayanan tidak dilaksanakan sebagaimana mestinya.
        </p>
    </div>

    <div class="mx-auto max-w-7xl px-4 pb-12">
        @if ($imageExists)
            <figure class="mx-auto max-w-[1200px] overflow-hidden rounded-2xl border border-line bg-white p-3 shadow-sm sm:p-4">
                <img
                    src="{{ asset($imageWebp) }}"
                    alt="Maklumat Pelayanan Poltekkes Kemenkes Banten"
                    width="{{ $imageWidth }}"
                    height="{{ $imageHeight }}"
                    class="mx-auto h-auto w-full max-w-full rounded-xl object-contain"
                    loading="lazy"
                    decoding="async"
                >
                <figcaption class="mt-3 text-center text-sm text-muted">
                    Maklumat Pelayanan Poltekkes Kemenkes Banten
                </figcaption>
            </figure>

            <div class="mx-auto mt-6 flex max-w-[1200px] flex-col gap-3 sm:flex-row sm:flex-wrap">
                <button
                    type="button"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark"
                    @click="open()"
                    x-ref="trigger"
                >
                    Lihat Ukuran Penuh
                </button>
                <a
                    href="{{ route('profil.maklumat-pelayanan.download') }}"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-green px-5 py-2.5 text-sm font-semibold text-white hover:bg-green-light"
                >
                    Unduh Maklumat Pelayanan
                </a>
            </div>

            <dl class="mx-auto mt-6 grid max-w-[1200px] gap-2 text-sm text-muted sm:grid-cols-3">
                <div><dt class="font-semibold text-navy">Format</dt><dd>WebP (PNG tersedia di server)</dd></div>
                <div><dt class="font-semibold text-navy">Resolusi</dt><dd>{{ $imageWidth }} × {{ $imageHeight }} px</dd></div>
                <div>
                    <dt class="font-semibold text-navy">Ukuran berkas</dt>
                    <dd>{{ $fileSize ? number_format($fileSize / 1024, 1, ',', '.').' KB' : '—' }}</dd>
                </div>
            </dl>
        @else
            <div class="rounded-xl border border-dashed border-line bg-white p-8 text-sm text-muted">
                Gambar Maklumat Pelayanan belum tersedia. Pastikan file ada di
                <code class="rounded bg-surface px-1">public/images/ppid/maklumat-pelayanan-poltekkes-banten.webp</code>.
            </div>
        @endif
    </div>

    {{-- Lightbox modal --}}
    <div
        x-cloak
        x-show="visible"
        x-transition.opacity
        class="fixed inset-0 z-[80] flex items-center justify-center bg-navy-dark/80 p-4"
        role="dialog"
        aria-modal="true"
        aria-label="Pratinjau Maklumat Pelayanan"
        @click.self="close()"
    >
        <div class="relative max-h-[min(92vh,900px)] w-full max-w-5xl overflow-auto rounded-2xl bg-white p-3 shadow-xl sm:p-4">
            <button
                type="button"
                class="absolute right-3 top-3 z-10 flex h-10 w-10 items-center justify-center rounded-full bg-navy text-white hover:bg-navy-dark"
                @click="close()"
                aria-label="Tutup pratinjau"
            >
                ✕
            </button>
            <img
                src="{{ asset($imageWebp) }}"
                alt="Maklumat Pelayanan Poltekkes Kemenkes Banten — ukuran penuh"
                width="{{ $imageWidth }}"
                height="{{ $imageHeight }}"
                class="mx-auto h-auto max-h-[80vh] w-full object-contain"
            >
        </div>
    </div>
</div>
@endsection
