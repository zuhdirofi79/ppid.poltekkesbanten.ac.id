{{-- Template khusus: /halaman/dasar-hukum-sk-ppid --}}
@php
    $pdfRelativePath = 'docs/2025070308315420250702075057SK-PPID-2025.pdf';
    $pdfUrl = asset($pdfRelativePath);
@endphp

<main class="ppid-document-page">
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    @foreach ($breadcrumbs ?? [] as $index => $crumb)
                        @if ($index > 0)
                            <li aria-hidden="true">/</li>
                        @endif

                        <li
                            @if ($loop->last)
                                class="text-navy"
                                aria-current="page"
                            @endif
                        >
                            @if (! empty($crumb['url']) && ! $loop->last)
                                <a
                                    href="{{ $crumb['url'] }}"
                                    class="hover:text-green"
                                >
                                    {{ $crumb['label'] }}
                                </a>
                            @else
                                {{ $crumb['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            <p class="text-sm font-semibold uppercase tracking-wider text-green">
                Dokumen PPID
            </p>

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-3xl lg:text-[2.5rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    <div class="mx-auto w-full max-w-[1280px] px-4 pb-12 pt-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:justify-end">
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
                download="SK-PPID-Poltekkes-Kemenkes-Banten-2025.pdf"
                class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-sm font-semibold text-navy hover:border-green hover:text-green"
            >
                Unduh PDF
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-line bg-white shadow-sm sm:rounded-2xl">
            <iframe
                src="{{ $pdfUrl }}#view=FitH&toolbar=1&navpanes=0"
                title="SK PPID Poltekkes Kemenkes Banten Tahun 2025"
                class="block w-full border-0"
                style="height: max(800px, calc(100vh - 180px));"
                loading="eager"
            ></iframe>
        </div>

        <p class="mt-4 text-sm leading-relaxed text-muted">
            Apabila dokumen tidak tampil,
            <a
                href="{{ $pdfUrl }}"
                target="_blank"
                rel="noopener noreferrer"
                class="font-semibold text-green hover:underline"
            >
                buka PDF melalui tab baru
            </a>.
        </p>
    </div>
</main>