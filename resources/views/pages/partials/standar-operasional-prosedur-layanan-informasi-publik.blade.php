{{-- Template: /halaman/standar-operasional-prosedur-layanan-informasi-publik --}}
<!-- PPID-SOP-LAYANAN-INFORMASI-V1 -->

<main class="ppid-sop-page">
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
        <div class="ppid-sop-list">
            @foreach ($documents ?? [] as $document)
                @php
                    $previewId = 'sop-preview-'.$loop->iteration;
                    $titleId = 'sop-title-'.$loop->iteration;
                @endphp
                <article class="ppid-sop-card" aria-labelledby="{{ $titleId }}" @if ($document['available']) x-data="{ previewOpen: false }" @endif>
                    <div class="ppid-sop-card-icon" aria-hidden="true">
                        <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h6.75L19.5 9.5V20.25a.75.75 0 0 1-.75.75H7a.75.75 0 0 1-.75-.75V4.5A.75.75 0 0 1 7 3.75Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75V9.5h5.75" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h6M9 16.5h4.5" />
                        </svg>
                    </div>

                    <div class="ppid-sop-card-body">
                        <div class="ppid-sop-meta">
                            @if ($document['available'])
                                <span class="ppid-sop-badge is-available">Tersedia</span>
                                <span>PDF / Dokumen Resmi</span>
                            @else
                                <span class="ppid-sop-badge is-missing">Dokumen tidak ditemukan</span>
                            @endif
                        </div>

                        <h2 id="{{ $titleId }}" class="ppid-sop-title">{{ $document['title'] }}</h2>

                        @if (! empty($document['description']))
                            <p class="ppid-sop-description">{{ $document['description'] }}</p>
                        @endif

                        @if ($document['available'])
                            <div class="ppid-sop-actions">
                                <a
                                    href="{{ $document['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="ppid-sop-button ppid-sop-button-primary"
                                    aria-label="Buka PDF {{ $document['title'] }} di tab baru"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M4.75 4h4.5a.75.75 0 0 1 0 1.5H5.5v9h9v-3.75a.75.75 0 0 1 1.5 0V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V4.5A.5.5 0 0 1 5 4h-.25Z"/>
                                        <path d="M11 4a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.81l-5.22 5.22a.75.75 0 0 1-1.06-1.06l5.22-5.22H11.75A.75.75 0 0 1 11 4Z"/>
                                    </svg>
                                    Buka PDF
                                </a>

                                <button
                                    type="button"
                                    class="ppid-sop-button ppid-sop-button-secondary"
                                    @click="previewOpen = !previewOpen"
                                    :aria-expanded="previewOpen.toString()"
                                    aria-controls="{{ $previewId }}"
                                    aria-label="Preview {{ $document['title'] }}"
                                >
                                    <span x-show="!previewOpen">Preview</span>
                                    <span x-show="previewOpen" x-cloak>Tutup Preview</span>
                                </button>

                                <a
                                    href="{{ $document['url'] }}"
                                    download="{{ $document['filename'] }}"
                                    class="ppid-sop-button ppid-sop-button-secondary"
                                    aria-label="Download PDF {{ $document['title'] }}"
                                >
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M10 2.75a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 0 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 0 1 1.06-1.06l2.22 2.22V3.5a.75.75 0 0 1 .75-.75Z"/>
                                        <path d="M3.5 13.25a.75.75 0 0 1 .75.75v1.5h11.5V14a.75.75 0 0 1 1.5 0v2a.5.5 0 0 1-.5.5H3.25a.5.5 0 0 1-.5-.5v-2a.75.75 0 0 1 .75-.75Z"/>
                                    </svg>
                                    Download PDF
                                </a>
                            </div>

                            <section
                                id="{{ $previewId }}"
                                class="ppid-sop-preview"
                                x-cloak
                                x-show="previewOpen"
                                x-transition.opacity.duration.200ms
                                aria-label="Preview {{ $document['title'] }}"
                            >
                                <template x-if="previewOpen">
                                    <iframe
                                        src="{{ $document['url'] }}#view=FitH&toolbar=1&navpanes=0"
                                        title="Preview {{ $document['title'] }}"
                                        class="ppid-sop-preview-frame"
                                        loading="lazy"
                                    ></iframe>
                                </template>
                            </section>
                        @else
                            <p class="ppid-sop-missing">Dokumen belum dapat ditampilkan. Silakan hubungi Admin PPID.</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</main>

<style>
    .ppid-sop-page { background: #fff; }
    .ppid-sop-list { display: flex; flex-direction: column; gap: 1rem; }
    .ppid-sop-card {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        padding: 1rem 1.1rem;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    @media (min-width: 768px) {
        .ppid-sop-card { flex-direction: row; align-items: flex-start; gap: 1.1rem; padding: 1.15rem 1.35rem; }
    }
    .ppid-sop-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.8rem;
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }
    .ppid-sop-card-body { min-width: 0; flex: 1; }
    .ppid-sop-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.55rem;
        margin-bottom: 0.35rem;
        font-size: 0.8125rem;
        color: var(--muted);
    }
    .ppid-sop-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.15rem 0.6rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .ppid-sop-badge.is-available { background: color-mix(in srgb, var(--green) 14%, #fff); color: var(--green); }
    .ppid-sop-badge.is-missing { background: #fef2f2; color: #b91c1c; }
    .ppid-sop-title {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.35;
        color: var(--navy);
    }
    .ppid-sop-description { margin: 0.4rem 0 0; font-size: 0.9375rem; line-height: 1.6; color: var(--muted); }
    .ppid-sop-actions { display: flex; flex-direction: column; gap: 0.65rem; margin-top: 0.9rem; }
    @media (min-width: 640px) {
        .ppid-sop-actions { flex-direction: row; flex-wrap: wrap; align-items: center; }
    }
    .ppid-sop-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        min-height: 2.75rem;
        padding: 0.5rem 0.95rem;
        border-radius: 0.5rem;
        border: 1px solid transparent;
        background: #fff;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25;
        text-decoration: none;
        cursor: pointer;
    }
    .ppid-sop-button:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    @media (max-width: 639px) { .ppid-sop-button { width: 100%; } }
    .ppid-sop-button-primary { background: var(--navy); color: #fff; }
    .ppid-sop-button-primary:hover { background: var(--navy-dark); }
    .ppid-sop-button-secondary { border-color: var(--border); color: var(--navy); }
    .ppid-sop-button-secondary:hover { border-color: var(--navy); background: var(--surface); }
    .ppid-sop-missing { margin: 0.75rem 0 0; font-size: 0.9375rem; line-height: 1.6; color: var(--muted); }
    .ppid-sop-preview {
        margin-top: 0.9rem;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 0.85rem;
        background: #f8fafc;
    }
    .ppid-sop-preview-frame {
        display: block;
        width: 100%;
        height: 560px;
        border: 0;
        background: #fff;
    }
    @media (min-width: 768px) {
        .ppid-sop-preview-frame { height: 780px; }
    }
</style>
