{{-- Template: /halaman/tata-cara-pengaduan-penyalahgunaan-wewenang --}}
<!-- PPID-TATA-CARA-PENGADUAN-WEWENANG-DEPLOY-20261002-A -->
@php
    $imageRelativePath = 'docs/tata-cara-pengaduan-penyalahgunaan-wewenang.png';
    $imageFile = public_path($imageRelativePath);
    $imageAvailable = is_file($imageFile);
    $imageUrl = asset($imageRelativePath);
    $imageSize = $imageAvailable ? @getimagesize($imageFile) : false;
    $altText = 'Tata Cara Pengaduan Penyalahgunaan Wewenang atau Pelanggaran oleh Pejabat Satuan Kerja Poltekkes Kemenkes Banten';
@endphp

<main class="ppid-tata-cara-page">
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

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <section class="ppid-tata-cara-card" aria-labelledby="tata-cara-pengaduan-title">
            <h2 id="tata-cara-pengaduan-title" class="ppid-tata-cara-card-title">
                {{ $title }}
            </h2>

            @if ($imageAvailable)
                <figure class="ppid-tata-cara-figure">
                    <div class="ppid-tata-cara-frame">
                        <img
                            src="{{ $imageUrl }}"
                            alt="{{ $altText }}"
                            class="ppid-tata-cara-image"
                            @if (is_array($imageSize))
                                width="{{ $imageSize[0] }}"
                                height="{{ $imageSize[1] }}"
                            @endif
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </figure>

                <div class="ppid-tata-cara-actions">
                    <a
                        href="{{ $imageUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-tata-cara-button ppid-tata-cara-button-primary"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M4.75 4h4.5a.75.75 0 0 1 0 1.5H5.5v9h9v-3.75a.75.75 0 0 1 1.5 0V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V4.5A.5.5 0 0 1 5 4h-.25Z"/>
                            <path d="M11 4a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.81l-5.22 5.22a.75.75 0 0 1-1.06-1.06l5.22-5.22H11.75A.75.75 0 0 1 11 4Z"/>
                        </svg>
                        Buka Gambar
                    </a>

                    <a
                        href="{{ $imageUrl }}"
                        download="tata-cara-pengaduan-penyalahgunaan-wewenang.png"
                        class="ppid-tata-cara-button ppid-tata-cara-button-secondary"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 2.75a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 0 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 0 1 1.06-1.06l2.22 2.22V3.5a.75.75 0 0 1 .75-.75Z"/>
                            <path d="M3.5 13.25a.75.75 0 0 1 .75.75v1.5h11.5V14a.75.75 0 0 1 1.5 0v2a.5.5 0 0 1-.5.5H3.25a.5.5 0 0 1-.5-.5v-2a.75.75 0 0 1 .75-.75Z"/>
                        </svg>
                        Download Infografis
                    </a>
                </div>
            @else
                <p class="ppid-tata-cara-fallback" role="status">
                    Infografis tata cara pengaduan belum tersedia.
                </p>
            @endif
        </section>
    </div>
</main>

<style>
    .ppid-tata-cara-page {
        background: #fff;
    }

    .ppid-tata-cara-card {
        margin-inline: auto;
        max-width: 52rem;
        padding: 1.15rem 1.25rem 1.35rem;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04), 0 8px 24px rgb(15 23 42 / 0.04);
    }

    @media (min-width: 768px) {
        .ppid-tata-cara-card {
            padding: 1.5rem 1.75rem 1.75rem;
            border-radius: 1.1rem;
        }
    }

    .ppid-tata-cara-card-title {
        margin: 0 0 1rem;
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--navy);
    }

    @media (min-width: 768px) {
        .ppid-tata-cara-card-title {
            font-size: 1.45rem;
        }
    }

    .ppid-tata-cara-figure {
        margin: 0;
    }

    .ppid-tata-cara-frame {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 0.85rem;
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }

    .ppid-tata-cara-image {
        display: block;
        width: 100%;
        height: auto;
        margin-inline: auto;
        object-fit: contain;
        background: #fff;
    }

    .ppid-tata-cara-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.15rem;
    }

    @media (min-width: 640px) {
        .ppid-tata-cara-actions {
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
        }
    }

    .ppid-tata-cara-button {
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
        text-decoration: none;
    }

    .ppid-tata-cara-button:focus-visible {
        outline: 2px solid var(--gold, #b8860b);
        outline-offset: 2px;
    }

    .ppid-tata-cara-button-primary {
        background: var(--navy);
        color: #fff;
    }

    .ppid-tata-cara-button-primary:hover {
        background: var(--navy-dark, #10113b);
    }

    .ppid-tata-cara-button-secondary {
        background: #fff;
        border-color: var(--border);
        color: var(--navy);
    }

    .ppid-tata-cara-button-secondary:hover {
        border-color: var(--navy);
        background: var(--surface);
    }

    .ppid-tata-cara-fallback {
        margin: 0;
        border: 1px dashed var(--border);
        border-radius: 0.85rem;
        background: var(--surface);
        padding: 1.25rem 1.1rem;
        text-align: center;
        font-size: 0.975rem;
        line-height: 1.7;
        color: var(--muted);
    }
</style>
