{{-- Template khusus: /halaman/profil-singkat-ppid — tidak mempengaruhi halaman CMS lain --}}
@php
    $buildingCandidates = [
        'images/ppid/gedung-poltekkes-kemenkes-banten.jpg',
        'images/ppid/gedung-poltekkes-kemenkes-banten.webp',
        config('ppid.hero'),
    ];

    $buildingSrc = null;
    foreach ($buildingCandidates as $candidate) {
        if (is_string($candidate) && $candidate !== '' && is_file(public_path($candidate))) {
            $buildingSrc = $candidate;
            break;
        }
    }

    if ($buildingSrc === null) {
        logger()->warning('Gambar gedung tidak ditemukan untuk halaman profil-singkat-ppid.', [
            'candidates' => $buildingCandidates,
        ]);
    }

    $pdfRelativePath = 'docs/2.-PROFIL-PPID-POLTEKKES-BANTEN.pdf';
    $pdfExists = is_file(public_path($pdfRelativePath));
    $pdfUrl = asset($pdfRelativePath);

    if (! $pdfExists) {
        logger()->warning('Dokumen PDF Profil PPID tidak ditemukan di public path.', [
            'relative_path' => $pdfRelativePath,
        ]);
    }
@endphp

<main class="ppid-profile-page">
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

            <h1 class="font-heading mt-1 text-[1.875rem] font-bold leading-tight text-navy sm:text-3xl lg:text-[2.125rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-3xl text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    @if ($buildingSrc)
        <section class="ppid-profile-hero mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8" aria-label="Foto gedung">
            <figure class="overflow-hidden rounded-xl bg-surface shadow-sm sm:rounded-2xl">
                <img
                    src="{{ asset($buildingSrc) }}"
                    alt="Gedung Poltekkes Kemenkes Banten"
                    width="1600"
                    height="800"
                    class="ppid-profile-hero__image h-[210px] w-full object-cover object-center sm:h-[300px] lg:h-[400px]"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async"
                >
                <figcaption class="border-t border-line bg-white px-4 py-2.5 text-center text-sm text-muted">
                    Gedung Poltekkes Kemenkes Banten
                </figcaption>
            </figure>
        </section>
    @endif

    <div class="mx-auto max-w-[1000px] px-5 py-10 sm:px-6 lg:px-8">
        <article class="ppid-profile-content">
            {!! $body !!}
        </article>

        <section class="ppid-pdf-section mt-12 border-t border-line pt-10 sm:mt-14" aria-labelledby="profil-pdf-heading">
            <h2 id="profil-pdf-heading" class="font-heading text-[1.5rem] font-bold leading-tight text-navy sm:text-[1.75rem]">
                Dokumen Profil PPID Lengkap
            </h2>
            <p class="mt-3 text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                Dokumen lengkap Profil PPID Poltekkes Kemenkes Banten dapat dibaca langsung melalui penampil berikut.
            </p>

            @if ($pdfExists)
                <div class="ppid-pdf-toolbar mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a
                        href="{{ $pdfUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-dark"
                    >
                        Buka PDF di Tab Baru
                    </a>
                    <a
                        href="{{ $pdfUrl }}"
                        download="Profil-PPID-Poltekkes-Kemenkes-Banten.pdf"
                        class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-sm font-semibold text-navy hover:border-green hover:text-green"
                    >
                        Unduh PDF
                    </a>
                </div>

                <div class="ppid-pdf-viewer mt-5 overflow-hidden rounded-[14px] border border-line bg-surface shadow-sm sm:rounded-[16px]">
                    <object
                        data="{{ $pdfUrl }}#view=FitH"
                        type="application/pdf"
                        class="ppid-pdf-viewer__object block w-full"
                        aria-label="Dokumen Profil PPID Poltekkes Kemenkes Banten"
                    >
                        <div class="space-y-3 p-6 text-sm leading-relaxed text-muted sm:text-base">
                            <p>Browser Anda tidak dapat menampilkan dokumen PDF secara langsung.</p>
                            <p>
                                <a
                                    href="{{ $pdfUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="font-semibold text-green hover:underline"
                                >
                                    Buka dokumen PDF
                                </a>
                            </p>
                        </div>
                    </object>
                </div>
            @else
                <div class="mt-6 rounded-xl border border-dashed border-line bg-surface px-5 py-6 text-sm leading-relaxed text-muted sm:text-base" role="status">
                    Dokumen Profil PPID belum tersedia.
                </div>
            @endif
        </section>
    </div>
</main>
