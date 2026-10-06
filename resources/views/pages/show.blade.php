@extends('layouts.app')

@section('title', ($page->slug ?? '') === \App\Support\InformationDesk::SLUG
    ? \App\Support\InformationDesk::TITLE.' | '.config('ppid.name')
    : (($seoTitle ?? $title).' — '.config('ppid.name')))
@section('meta_description', $description ?? config('ppid.name_long'))
@section('og_title', $seoTitle ?? $title)
@section('og_description', $description ?? config('ppid.name_long'))
@section('canonical', route('pages.show', $page->slug ?? request()->route('slug')))

@section('content')
    @switch((string) ($page->slug ?? ''))
        @case('dasar-hukum-sk-ppid')
            @include('pages.partials.dasar-hukum-sk-ppid', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('profil-singkat-ppid')
            @include('pages.partials.profil-singkat-ppid')
            @break

        @case('visi-dan-misi')
            @include('pages.partials.visi-dan-misi', [
                'page' => $page,
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('tugas-fungsi-wewenang')
            @include('pages.partials.tugas-fungsi-wewenang', [
                'page' => $page,
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('struktur-organisasi')
            @include('pages.partials.struktur-organisasi', [
                'page' => $page,
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('alamat-kantor-satuan-kerja')
            @include('pages.partials.alamat-kantor-satuan-kerja', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('visi-misi-satuan-kerja')
            @include('pages.partials.visi-misi-satuan-kerja', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('tugas-fungsi-satuan-kerja')
            @include('pages.partials.tugas-fungsi-satuan-kerja', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('profil-pimpinan-satuan-kerja')
            @include('pages.partials.profil-pimpinan-satuan-kerja', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'profiles' => $leadershipProfiles ?? collect(),
            ])
            @break

        @case('program-kegiatan-prioritas-2026')
            @include('pages.partials.program-kegiatan-prioritas-2026', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'body' => $body ?? '',
            ])
            @break

        @case('kebijakan-mengikat-bagi-publik')
            @include('pages.partials.kebijakan-mengikat-bagi-publik', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('kalender-akademik')
            @include('pages.partials.kalender-akademik', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'calendars' => $academicCalendars ?? collect(),
                'selectedCalendar' => $selectedCalendar ?? null,
                'selectedYear' => $selectedYear ?? null,
            ])
            @break

        @case('tata-cara-pengaduan-penyalahgunaan-wewenang')
            @include('pages.partials.tata-cara-pengaduan-penyalahgunaan-wewenang', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('ringkasan-laporan-layanan-informasi')
            @include('pages.partials.ringkasan-laporan-layanan-informasi', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'report' => $serviceReport ?? [],
            ])
            @break

        @case('standar-operasional-prosedur-layanan-informasi-publik')
            @include('pages.partials.standar-operasional-prosedur-layanan-informasi-publik', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'documents' => $sopDocuments ?? [],
            ])
            @break

        @case('lhkpn-pimpinan-tertinggi')
            @include('pages.partials.lhkpn-pimpinan-tertinggi', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'documents' => $lhkpnDocuments ?? collect(),
            ])
            @break

        @case('information-desk')
            @include('pages.partials.information-desk', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
                'body' => $body ?? '',
            ])
            @break

        @case('statistik-keuangan')
            @include('pages.partials.statistik-keuangan', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('daftar-informasi-publik')
            @include('pages.partials.daftar-informasi-publik', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @case('rapat-koordinasi-ppid')
            @include('pages.partials.rapat-koordinasi-ppid', [
                'title' => $title ?? $page->title,
                'excerpt' => $excerpt ?? $page->excerpt,
                'label' => $label ?? null,
                'breadcrumbs' => $breadcrumbs ?? [],
            ])
            @break

        @default
            @if (($page->slug ?? '') === 'akreditasi')
                <!-- PPID-AKREDITASI-DEPLOY-20260904-A -->
            @endif
            <div class="border-b border-line bg-surface">
                <div class="mx-auto max-w-7xl px-4 py-8">
                    <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                        <ol class="flex flex-wrap items-center gap-2">
                            @foreach ($breadcrumbs ?? [] as $index => $crumb)
                                @if ($index > 0)
                                    <li aria-hidden="true">/</li>
                                @endif
                                <li @if ($loop->last) class="text-navy" aria-current="page" @endif>
                                    @if (! empty($crumb['url']) && ! $loop->last)
                                        <a href="{{ $crumb['url'] }}" class="hover:text-green">{{ $crumb['label'] }}</a>
                                    @else
                                        {{ $crumb['label'] }}
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    </nav>

                    @if (! empty($label))
                        <p class="text-sm font-semibold uppercase tracking-wider text-green">{{ $label }}</p>
                    @endif

                    <h1 class="font-heading mt-1 text-2xl font-bold text-navy sm:text-3xl">{{ $title }}</h1>

                    @if (! empty($excerpt))
                        <p class="mt-2 max-w-3xl text-muted">{{ $excerpt }}</p>
                    @endif
                </div>
            </div>

            <article class="cms-page mx-auto max-w-3xl px-4 py-10">
                {!! $body !!}
            </article>
            @include('pages.partials.service-summary-table', ['rows' => $serviceSummaries ?? collect()])
    @endswitch

    @include('pages.partials.public-attachments', ['publicAttachments' => $publicAttachments ?? collect()])
@endsection
