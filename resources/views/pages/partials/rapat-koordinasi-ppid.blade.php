{{-- Template khusus: /halaman/rapat-koordinasi-ppid — kartu PDF dan pratinjau inline --}}
<!-- PPID-RAPAT-KOORDINASI-PPID-DEPLOY-20261006-A -->
@php
    use App\Support\RapatKoordinasiPpid;

    $documentUrl = RapatKoordinasiPpid::documentUrl();
    $documentAvailable = RapatKoordinasiPpid::isAvailable();
@endphp

<main class="rapat-koordinasi-ppid-page">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-3xl text-base leading-relaxed text-muted">{{ $excerpt }}</p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <section id="dokumen-rapat-koordinasi-ppid" class="ppid-request-receipt" aria-labelledby="rapat-koordinasi-heading">
            <div class="ppid-request-section-intro">
                <h2 id="rapat-koordinasi-heading" class="ppid-request-section-title">{{ RapatKoordinasiPpid::SECTION_HEADING }}</h2>
            </div>

            @if ($documentAvailable)
                <article class="ppid-request-receipt-card" aria-labelledby="rapat-koordinasi-doc-title">
                    <div class="ppid-request-receipt-icon" aria-hidden="true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" />
                            <path d="M14 3v5h5" />
                            <path d="M8 13h8M8 17h5" />
                        </svg>
                    </div>

                    <div class="ppid-request-receipt-body">
                        <p class="ppid-request-receipt-meta">
                            <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                            <span>{{ RapatKoordinasiPpid::FORMAT_LABEL }}</span>
                        </p>
                        <h3 id="rapat-koordinasi-doc-title" class="ppid-request-receipt-title">{{ RapatKoordinasiPpid::DOCUMENT_TITLE }}</h3>
                        <p class="ppid-request-receipt-note">{{ RapatKoordinasiPpid::DOCUMENT_DESCRIPTION }}</p>
                    </div>

                    <div class="ppid-request-receipt-actions">
                        <a
                            href="{{ $documentUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ppid-request-form-button"
                            aria-label="{{ RapatKoordinasiPpid::OPEN_ARIA }}"
                        >
                            {{ RapatKoordinasiPpid::OPEN_LABEL }}
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M14 5h5v5" />
                                <path d="M10 14 19 5" />
                                <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                            </svg>
                        </a>
                        <a
                            href="{{ $documentUrl }}"
                            class="ppid-request-secondary-action"
                            download="{{ RapatKoordinasiPpid::FILENAME }}"
                            aria-label="{{ RapatKoordinasiPpid::DOWNLOAD_ARIA }}"
                        >
                            {{ RapatKoordinasiPpid::DOWNLOAD_LABEL }}
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M12 3v12" />
                                <path d="m7 11 5 5 5-5" />
                                <path d="M5 21h14" />
                            </svg>
                        </a>
                    </div>
                </article>

                <section class="ppid-sk-preview" aria-labelledby="rapat-koordinasi-preview-heading">
                    <div class="ppid-sk-preview-header">
                        <h2 id="rapat-koordinasi-preview-heading" class="ppid-sk-preview-heading">Pratinjau Dokumen</h2>
                    </div>
                    <iframe
                        src="{{ $documentUrl }}#view=FitH&toolbar=1&navpanes=0"
                        title="{{ RapatKoordinasiPpid::DOCUMENT_TITLE }}"
                        class="ppid-sk-preview-frame"
                        loading="lazy"
                    ></iframe>
                    <p class="ppid-sk-preview-fallback">{{ RapatKoordinasiPpid::BROWSER_FALLBACK }}</p>
                </section>
            @else
                <p class="rounded-xl border border-dashed border-line bg-white p-6 text-sm text-muted">{{ RapatKoordinasiPpid::UNAVAILABLE }}</p>
            @endif
        </section>
    </div>
</main>
