{{-- Template khusus: /halaman/dasar-hukum-sk-ppid — kartu dokumen + preview PDF --}}
@php
    $pdfUrl = asset('docs/SK_PPID_2025.pdf');
@endphp

<main class="ppid-sk-page">
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

            <p class="text-sm font-semibold uppercase tracking-wider text-green">Dokumen PPID</p>

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

    <div
        class="ppid-sk-document mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8"
        x-data="{ previewOpen: false }"
    >
        <section class="ppid-sk-card" aria-labelledby="sk-ppid-card-title">
            <div class="ppid-sk-card-icon" aria-hidden="true">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h6.75L19.5 9.5V20.25a.75.75 0 0 1-.75.75H7a.75.75 0 0 1-.75-.75V4.5A.75.75 0 0 1 7 3.75Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75V9.5h5.75" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h6M9 16.5h4.5" />
                </svg>
            </div>

            <div class="ppid-sk-card-content">
                <p class="ppid-sk-eyebrow">Dokumen Resmi PPID</p>

                <h2 id="sk-ppid-card-title" class="ppid-sk-title">
                    SK Pejabat Pengelola Informasi dan Dokumentasi Tahun 2025
                </h2>

                <p class="ppid-sk-description">
                    Keputusan Direktur Poltekkes Kemenkes Banten Nomor HK.02.03/F.XXII/4671/2025
                    tentang Pejabat Pengelola Informasi dan Dokumentasi (PPID)
                    Poltekkes Kemenkes Banten Tahun 2025.
                </p>

                <p class="ppid-sk-meta">
                    8 halaman • PDF • Ditandatangani secara elektronik
                </p>

                <div class="ppid-sk-actions">
                    <a
                        href="{{ $pdfUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-sk-button ppid-sk-button-primary"
                    >
                        Lihat SK PPID Poltekkes Banten
                    </a>

                    <a
                        href="{{ $pdfUrl }}"
                        download="SK-PPID-Poltekkes-Kemenkes-Banten-2025.pdf"
                        class="ppid-sk-button ppid-sk-button-secondary"
                    >
                        Download PDF
                    </a>

                    <button
                        type="button"
                        class="ppid-sk-button ppid-sk-button-preview"
                        @click="previewOpen = !previewOpen"
                        :aria-expanded="previewOpen.toString()"
                        :class="{ 'is-active': previewOpen }"
                        aria-controls="sk-ppid-preview"
                    >
                        <span x-text="previewOpen ? 'Tutup Preview' : 'Preview SK PPID'">Preview SK PPID</span>
                    </button>
                </div>
            </div>
        </section>

        <section
            x-cloak
            x-show="previewOpen"
            x-transition.opacity.duration.200ms
            id="sk-ppid-preview"
            class="ppid-sk-preview"
            aria-label="Preview dokumen SK PPID"
        >
            <div class="ppid-sk-preview-header">
                <h2 class="ppid-sk-preview-heading">Preview SK PPID Tahun 2025</h2>
                <button
                    type="button"
                    class="ppid-sk-preview-close"
                    @click="previewOpen = false"
                    aria-label="Tutup preview PDF"
                >
                    Tutup
                </button>
            </div>

            <iframe
                src="{{ $pdfUrl }}#view=FitH&toolbar=1&navpanes=0"
                title="Preview SK PPID Poltekkes Kemenkes Banten Tahun 2025"
                class="ppid-sk-preview-frame"
                loading="lazy"
            ></iframe>

            <p class="ppid-sk-preview-fallback">
                Apabila dokumen tidak tampil pada browser Anda, silakan
                <a href="{{ $pdfUrl }}" target="_blank" rel="noopener noreferrer">
                    buka PDF di tab baru
                </a>
                atau unduh file PDF.
            </p>
        </section>
    </div>
</main>
