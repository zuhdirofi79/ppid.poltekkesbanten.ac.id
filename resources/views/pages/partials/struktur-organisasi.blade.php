{{-- Template khusus: /halaman/struktur-organisasi — gambar struktur organisasi yang sudah ada di public/images/ppid --}}
<!-- PPID-STRUKTUR-ORGANISASI-DEPLOY-20260923-B -->
<style>
    .ppid-org-chart {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    @media (max-width: 1023px) {
        .ppid-org-chart {
            width: 1100px;
            max-width: none;
        }
    }

    .ppid-org-structure-actions a:focus-visible {
        outline: 2px solid #17184f;
        outline-offset: 2px;
    }
</style>
@php
    $imageRelativePath = 'images/ppid/struktur-organisasi-ppid.png';
    $imageExists = is_file(public_path($imageRelativePath));
    $imageUrl = $imageExists
        ? asset($imageRelativePath)
        : 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/struktur-organisasi-ppid.png';
    $downloadName = 'struktur-organisasi-ppid-poltekkes-kemenkes-banten.png';
    $imageAlt = 'Struktur Organisasi PPID Poltekkes Kemenkes Banten Periode 2025-2029';
@endphp

<main class="ppid-org-structure-page">
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem] lg:text-[2.5rem]">
                {{ $title }}
            </h1>

            <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                {{ $excerpt ?? 'Struktur organisasi Pejabat Pengelola Informasi dan Dokumentasi (PPID) Poltekkes Kemenkes Banten.' }}
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-[1200px] px-4 py-10 sm:px-6 sm:py-12 lg:px-8">
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-6" aria-labelledby="struktur-organisasi-heading">
            <h2 id="struktur-organisasi-heading" class="font-heading text-lg font-bold text-navy sm:text-xl">
                Struktur Organisasi PPID
            </h2>
            <p class="mt-2 text-sm leading-relaxed text-ink sm:text-base">
                Struktur organisasi PPID Poltekkes Kemenkes Banten menggambarkan susunan pengelola informasi publik dalam mendukung pelaksanaan keterbukaan informasi publik.
            </p>

            <div class="ppid-org-structure-actions mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a
                    href="{{ $imageUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark"
                >
                    Lihat Ukuran Penuh
                </a>
                <a
                    href="{{ $imageUrl }}"
                    download="{{ $downloadName }}"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-sm font-semibold text-navy hover:border-green hover:text-green"
                >
                    Download Struktur Organisasi
                </a>
            </div>

            <div class="mt-5 overflow-x-auto rounded-xl border border-line bg-white shadow-sm">
                <img
                    src="{{ $imageUrl }}"
                    alt="{{ $imageAlt }}"
                    width="1600"
                    height="1120"
                    class="ppid-org-chart h-auto w-full object-contain"
                    loading="lazy"
                    decoding="async"
                >
            </div>
            <p class="mt-3 text-sm text-muted">
                Pada layar kecil, geser gambar ke samping agar seluruh bagan tetap terbaca.
            </p>
        </section>
    </div>
</main>
