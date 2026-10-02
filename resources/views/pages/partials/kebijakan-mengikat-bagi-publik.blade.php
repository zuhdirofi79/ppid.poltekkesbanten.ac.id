{{-- Template: /halaman/kebijakan-mengikat-bagi-publik — kartu dokumen + embedded PDF viewer --}}
<!-- PPID-KEBIJAKAN-MENGIKAT-BAGI-PUBLIK-DEPLOY-20261002-A -->
@php
    $pdfUrl = asset('docs/2026permenkes013.pdf');
@endphp

<main class="ppid-kebijakan-page">
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

    <div class="ppid-kebijakan-document mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        {{-- Document Card --}}
        <section class="ppid-kebijakan-card" aria-labelledby="kebijakan-card-title">
            <div class="ppid-kebijakan-card-icon" aria-hidden="true">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h6.75L19.5 9.5V20.25a.75.75 0 0 1-.75.75H7a.75.75 0 0 1-.75-.75V4.5A.75.75 0 0 1 7 3.75Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75V9.5h5.75" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h6M9 16.5h4.5" />
                </svg>
            </div>

            <div class="ppid-kebijakan-card-content">
                <div class="ppid-kebijakan-card-header">
                    <span class="ppid-kebijakan-badge is-available">Tersedia</span>
                    <span class="ppid-kebijakan-meta-type">PDF / Dokumen Resmi</span>
                </div>

                <h2 id="kebijakan-card-title" class="ppid-kebijakan-title">
                    Peraturan Menteri Kesehatan Republik Indonesia Nomor 13 Tahun 2026
                </h2>

                <p class="ppid-kebijakan-description">
                    Perubahan Kedua atas Peraturan Menteri Kesehatan Nomor 71 Tahun 2020 tentang Organisasi dan Tata Kerja Politeknik Kesehatan di Lingkungan Kementerian Kesehatan.
                </p>

                <div class="ppid-kebijakan-actions">
                    <a
                        href="{{ $pdfUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-kebijakan-button ppid-kebijakan-button-primary"
                        aria-label="Buka dokumen Permenkes Nomor 13 Tahun 2026 di tab baru"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M4.75 4h4.5a.75.75 0 0 1 0 1.5H5.5v9h9v-3.75a.75.75 0 0 1 1.5 0V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V4.5A.5.5 0 0 1 5 4h-.25Z"/>
                            <path d="M11 4a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.81l-5.22 5.22a.75.75 0 0 1-1.06-1.06l5.22-5.22H11.75A.75.75 0 0 1 11 4Z"/>
                        </svg>
                        Buka PDF
                    </a>

                    <a
                        href="{{ $pdfUrl }}"
                        download="Permenkes-13-Tahun-2026-OTK-Poltekkes.pdf"
                        class="ppid-kebijakan-button ppid-kebijakan-button-secondary"
                        aria-label="Unduh dokumen Permenkes Nomor 13 Tahun 2026"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 2.75a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 0 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 0 1 1.06-1.06l2.22 2.22V3.5a.75.75 0 0 1 .75-.75Z"/>
                            <path d="M3.5 13.25a.75.75 0 0 1 .75.75v1.5h11.5V14a.75.75 0 0 1 1.5 0v2a.5.5 0 0 1-.5.5H3.25a.5.5 0 0 1-.5-.5v-2a.75.75 0 0 1 .75-.75Z"/>
                        </svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </section>

        {{-- PDF Viewer --}}
        <section class="ppid-kebijakan-viewer" aria-label="Preview dokumen Peraturan Menteri Kesehatan Nomor 13 Tahun 2026">
            <div class="ppid-kebijakan-viewer-header">
                <h2 class="ppid-kebijakan-viewer-heading">Dokumen Permenkes No. 13 Tahun 2026</h2>
            </div>

            <div class="ppid-kebijakan-viewer-container">
                <iframe
                    src="{{ $pdfUrl }}#view=FitH&toolbar=1&navpanes=0"
                    title="Peraturan Menteri Kesehatan Republik Indonesia Nomor 13 Tahun 2026"
                    class="ppid-kebijakan-viewer-frame"
                    loading="lazy"
                ></iframe>
            </div>

            <p class="ppid-kebijakan-viewer-fallback">
                Apabila dokumen tidak tampil pada browser Anda, silakan
                <a href="{{ $pdfUrl }}" target="_blank" rel="noopener noreferrer">
                    buka PDF di tab baru
                </a>
                atau unduh file PDF menggunakan tombol di atas.
            </p>
        </section>
    </div>
</main>

<style>
    .ppid-kebijakan-page {
        background: #fff;
    }

    .ppid-kebijakan-card {
        display: flex;
        flex-direction: column;
        gap: 1.15rem;
        padding: 1.15rem 1.25rem;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04), 0 8px 24px rgb(15 23 42 / 0.04);
    }

    @media (min-width: 768px) {
        .ppid-kebijakan-card {
            flex-direction: row;
            align-items: flex-start;
            gap: 1.35rem;
            padding: 1.5rem 1.75rem;
            border-radius: 1.1rem;
        }
    }

    .ppid-kebijakan-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 3rem;
        height: 3rem;
        border-radius: 0.85rem;
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    @media (min-width: 768px) {
        .ppid-kebijakan-card-icon {
            width: 3.25rem;
            height: 3.25rem;
        }
    }

    .ppid-kebijakan-card-content {
        min-width: 0;
        flex: 1;
    }

    .ppid-kebijakan-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.5rem;
    }

    .ppid-kebijakan-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.2rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .ppid-kebijakan-badge.is-available {
        background: color-mix(in srgb, var(--green) 14%, #fff);
        color: var(--green);
    }

    .ppid-kebijakan-meta-type {
        font-size: 0.8125rem;
        color: var(--muted);
    }

    .ppid-kebijakan-title {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.3;
        color: var(--navy);
    }

    @media (min-width: 768px) {
        .ppid-kebijakan-title {
            font-size: 1.45rem;
        }
    }

    .ppid-kebijakan-description {
        margin: 0.75rem 0 0;
        max-width: 46rem;
        font-size: 0.975rem;
        line-height: 1.7;
        color: var(--muted);
    }

    .ppid-kebijakan-actions {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.15rem;
    }

    @media (min-width: 640px) {
        .ppid-kebijakan-actions {
            flex-direction: row;
            align-items: center;
        }
    }

    .ppid-kebijakan-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        min-height: 2.75rem;
        padding: 0.55rem 1.05rem;
        border-radius: 0.5rem;
        border: 1px solid transparent;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25;
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    }

    .ppid-kebijakan-button:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    @media (max-width: 639px) {
        .ppid-kebijakan-button {
            width: 100%;
        }
    }

    .ppid-kebijakan-button-primary {
        background: var(--navy);
        color: #fff;
    }

    .ppid-kebijakan-button-primary:hover {
        background: var(--navy-dark);
    }

    .ppid-kebijakan-button-secondary {
        background: #fff;
        border-color: var(--border);
        color: var(--navy);
    }

    .ppid-kebijakan-button-secondary:hover {
        border-color: var(--navy);
        background: var(--surface);
    }

    .ppid-kebijakan-viewer {
        margin-top: 1.5rem;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: #f8fafc;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }

    .ppid-kebijakan-viewer-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .ppid-kebijakan-viewer-heading {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 700;
        color: var(--navy);
    }

    .ppid-kebijakan-viewer-container {
        background: #fff;
    }

    .ppid-kebijakan-viewer-frame {
        display: block;
        width: 100%;
        min-height: 600px;
        height: 700px;
        border: 0;
        background: #fff;
    }

    @media (min-width: 768px) {
        .ppid-kebijakan-viewer-frame {
            min-height: 780px;
            height: 800px;
        }
    }

    @media (min-width: 1024px) {
        .ppid-kebijakan-viewer-frame {
            min-height: 900px;
            height: 1000px;
        }
    }

    .ppid-kebijakan-viewer-fallback {
        margin: 0;
        padding: 0.85rem 1rem 1rem;
        border-top: 1px solid var(--border);
        background: #fff;
        font-size: 0.875rem;
        line-height: 1.6;
        color: var(--muted);
    }

    .ppid-kebijakan-viewer-fallback a {
        color: var(--green);
        font-weight: 600;
        text-decoration: none;
    }

    .ppid-kebijakan-viewer-fallback a:hover {
        text-decoration: underline;
        text-underline-offset: 2px;
    }
</style>
