{{-- Template khusus: /halaman/struktur-organisasi — gambar struktur organisasi yang sudah ada di public/images/ppid --}}
<!-- PPID-STRUKTUR-ORGANISASI-DEPLOY-20260923-A -->
<style>
    .ppid-org-chart {
        display: block;
        width: 100%;
        max-width: none;
        min-width: 1100px;
        height: auto;
        object-fit: contain;
    }

    @media (min-width: 1024px) {
        .ppid-org-chart {
            min-width: 0;
        }
    }
</style>
@php
    $imageRelativePath = 'images/ppid/struktur-organisasi-ppid.jpeg';
    $imageUrl = asset($imageRelativePath);
    $downloadName = 'struktur-organisasi-ppid-poltekkes-kemenkes-banten.jpeg';
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

            @if (! empty($excerpt))
                <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-[1200px] px-4 py-10 sm:px-6 sm:py-12 lg:px-8">
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-6" aria-labelledby="struktur-organisasi-heading">
            <h2 id="struktur-organisasi-heading" class="font-heading text-lg font-bold text-navy sm:text-xl">
                Struktur Organisasi PPID
            </h2>
            <p class="mt-2 text-sm leading-relaxed text-muted">
                Struktur organisasi PPID Poltekkes Kemenkes Banten periode 2025-2029.
            </p>

            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
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
