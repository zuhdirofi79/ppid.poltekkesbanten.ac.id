{{-- Template: /halaman/kalender-akademik — Kalender Akademik dengan selector tahun (format-aware) --}}
<!-- PPID-KALENDER-AKADEMIK-DEPLOY-20261002-B -->

<main class="ppid-kalender-page">
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

    <div class="ppid-kalender-content mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        @if ($calendars->isEmpty())
            {{-- No calendars available --}}
            <div class="ppid-kalender-empty">
                <div class="ppid-kalender-empty-icon" aria-hidden="true">
                    <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <h2 class="ppid-kalender-empty-title">Kalender Akademik Belum Tersedia</h2>
                <p class="ppid-kalender-empty-text">
                    Dokumen kalender akademik sedang dalam proses penyusunan dan akan segera dipublikasikan.
                </p>
            </div>
        @else
            {{-- Year Selector --}}
            <div class="ppid-kalender-selector">
                <label for="tahun-selector" class="ppid-kalender-selector-label">Pilih Tahun Akademik:</label>
                <div class="ppid-kalender-selector-wrapper">
                    <select
                        id="tahun-selector"
                        class="ppid-kalender-selector-input"
                        onchange="window.location.href = '{{ route('pages.show', ['slug' => 'kalender-akademik']) }}?tahun=' + this.value"
                    >
                        @foreach ($calendars as $calendar)
                            <option
                                value="{{ $calendar->year }}"
                                {{ $selectedYear == $calendar->year ? 'selected' : '' }}
                            >
                                {{ $calendar->getYearLabel() }}
                            </option>
                        @endforeach
                    </select>
                    <div class="ppid-kalender-selector-arrow" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            @if ($selectedCalendar && $selectedCalendar->isAvailable())
                @php
                    $docUrl = $selectedCalendar->getDocumentUrl();
                    $isImage = $selectedCalendar->isImage();
                    $isPdf = $selectedCalendar->isPdf();
                @endphp

                {{-- Document Card --}}
                <section class="ppid-kalender-card" aria-labelledby="kalender-card-title">
                    <div class="ppid-kalender-card-icon {{ $isImage ? 'is-image' : 'is-pdf' }}" aria-hidden="true">
                        @if ($isImage)
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        @else
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 3.75h6.75L19.5 9.5V20.25a.75.75 0 0 1-.75.75H7a.75.75 0 0 1-.75-.75V4.5A.75.75 0 0 1 7 3.75Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 3.75V9.5h5.75" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 13.5h6M9 16.5h4.5" />
                            </svg>
                        @endif
                    </div>

                    <div class="ppid-kalender-card-content">
                        <div class="ppid-kalender-card-header">
                            <span class="ppid-kalender-badge is-available">Tersedia</span>
                            <span class="ppid-kalender-meta-type">{{ $selectedCalendar->getFormatDescription() }}</span>
                        </div>

                        <h2 id="kalender-card-title" class="ppid-kalender-title">
                            {{ $selectedCalendar->title }}
                        </h2>

                        @if ($selectedCalendar->description)
                            <p class="ppid-kalender-description">
                                {{ $selectedCalendar->description }}
                            </p>
                        @endif

                        <div class="ppid-kalender-actions">
                            <a
                                href="{{ $docUrl }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="ppid-kalender-button ppid-kalender-button-primary"
                                aria-label="Lihat {{ $selectedCalendar->title }} di tab baru"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M4.75 4h4.5a.75.75 0 0 1 0 1.5H5.5v9h9v-3.75a.75.75 0 0 1 1.5 0V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V4.5A.5.5 0 0 1 5 4h-.25Z"/>
                                    <path d="M11 4a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0V5.81l-5.22 5.22a.75.75 0 0 1-1.06-1.06l5.22-5.22H11.75A.75.75 0 0 1 11 4Z"/>
                                </svg>
                                {{ $isImage ? 'Lihat Ukuran Penuh' : 'Buka Dokumen' }}
                            </a>

                            <a
                                href="{{ $docUrl }}"
                                download="{{ $selectedCalendar->getDownloadFilename() }}"
                                class="ppid-kalender-button ppid-kalender-button-secondary"
                                aria-label="Unduh {{ $selectedCalendar->title }}"
                            >
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10 2.75a.75.75 0 0 1 .75.75v7.69l2.22-2.22a.75.75 0 0 1 1.06 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 0 1 1.06-1.06l2.22 2.22V3.5a.75.75 0 0 1 .75-.75Z"/>
                                    <path d="M3.5 13.25a.75.75 0 0 1 .75.75v1.5h11.5V14a.75.75 0 0 1 1.5 0v2a.5.5 0 0 1-.5.5H3.25a.5.5 0 0 1-.5-.5v-2a.75.75 0 0 1 .75-.75Z"/>
                                </svg>
                                Unduh Dokumen
                            </a>
                        </div>
                    </div>
                </section>

                {{-- Format-Aware Viewer --}}
                @if ($isImage)
                    {{-- IMAGE VIEWER for PNG/JPG/JPEG --}}
                    <section class="ppid-kalender-image-viewer" aria-label="Gambar Kalender Akademik Tahun {{ $selectedCalendar->getYearLabel() }}">
                        <div class="ppid-kalender-image-container">
                            <a href="{{ $docUrl }}" target="_blank" rel="noopener noreferrer" class="ppid-kalender-image-link">
                                <img
                                    src="{{ $docUrl }}"
                                    alt="{{ $selectedCalendar->getAltText() }}"
                                    class="ppid-kalender-image"
                                    loading="eager"
                                    decoding="async"
                                >
                            </a>
                        </div>
                        <p class="ppid-kalender-image-caption">
                            Klik gambar untuk melihat ukuran penuh di tab baru.
                        </p>
                    </section>
                @elseif ($isPdf)
                    {{-- PDF VIEWER --}}
                    <section class="ppid-kalender-pdf-viewer" aria-label="Preview Kalender Akademik Tahun {{ $selectedCalendar->getYearLabel() }}">
                        <div class="ppid-kalender-pdf-header">
                            <h2 class="ppid-kalender-pdf-heading">Dokumen Kalender Akademik {{ $selectedCalendar->getYearLabel() }}</h2>
                        </div>

                        <div class="ppid-kalender-pdf-container">
                            <iframe
                                src="{{ $docUrl }}#view=FitH&toolbar=1&navpanes=0"
                                title="Kalender Akademik Tahun {{ $selectedCalendar->getYearLabel() }}"
                                class="ppid-kalender-pdf-frame"
                                loading="lazy"
                            ></iframe>
                        </div>

                        <p class="ppid-kalender-pdf-fallback">
                            Apabila dokumen tidak tampil pada browser Anda, silakan
                            <a href="{{ $docUrl }}" target="_blank" rel="noopener noreferrer">
                                buka PDF di tab baru
                            </a>
                            atau unduh file menggunakan tombol di atas.
                        </p>
                    </section>
                @else
                    {{-- GENERIC FILE (download only) --}}
                    <section class="ppid-kalender-download-only">
                        <p class="ppid-kalender-download-text">
                            Dokumen ini tidak dapat ditampilkan langsung di browser.
                            Silakan unduh menggunakan tombol di atas.
                        </p>
                    </section>
                @endif

            @elseif ($selectedYear)
                {{-- Year requested but not available --}}
                <div class="ppid-kalender-not-found">
                    <div class="ppid-kalender-not-found-icon" aria-hidden="true">
                        <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <h2 class="ppid-kalender-not-found-title">Kalender Akademik Tahun {{ $selectedYear }} Belum Tersedia</h2>
                    <p class="ppid-kalender-not-found-text">
                        Dokumen kalender akademik untuk tahun {{ $selectedYear }} belum dipublikasikan atau sedang dalam proses penyusunan.
                    </p>
                    @if ($calendars->isNotEmpty())
                        <a href="{{ route('pages.show', ['slug' => 'kalender-akademik']) }}" class="ppid-kalender-not-found-link">
                            Lihat Tahun Tersedia
                        </a>
                    @endif
                </div>
            @endif
        @endif
    </div>
</main>

<style>
    .ppid-kalender-page {
        background: #fff;
    }

    .ppid-kalender-selector {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .ppid-kalender-selector-label {
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
    }

    .ppid-kalender-selector-wrapper {
        position: relative;
        display: inline-block;
    }

    .ppid-kalender-selector-input {
        appearance: none;
        min-width: 9rem;
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 600;
        color: var(--navy);
        background: #fff;
        border: 2px solid var(--navy);
        border-radius: 0.5rem;
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .ppid-kalender-selector-input:hover {
        border-color: var(--green);
    }

    .ppid-kalender-selector-input:focus {
        outline: none;
        border-color: var(--green);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--green) 20%, transparent);
    }

    .ppid-kalender-selector-arrow {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: var(--navy);
    }

    .ppid-kalender-card {
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
        .ppid-kalender-card {
            flex-direction: row;
            align-items: flex-start;
            gap: 1.35rem;
            padding: 1.5rem 1.75rem;
            border-radius: 1.1rem;
        }
    }

    .ppid-kalender-card-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 3rem;
        height: 3rem;
        border-radius: 0.85rem;
    }

    .ppid-kalender-card-icon.is-pdf {
        color: #b91c1c;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    .ppid-kalender-card-icon.is-image {
        color: var(--green);
        background: color-mix(in srgb, var(--green) 10%, #fff);
        border: 1px solid color-mix(in srgb, var(--green) 25%, transparent);
    }

    @media (min-width: 768px) {
        .ppid-kalender-card-icon {
            width: 3.25rem;
            height: 3.25rem;
        }
    }

    .ppid-kalender-card-content {
        min-width: 0;
        flex: 1;
    }

    .ppid-kalender-card-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.65rem;
        margin-bottom: 0.5rem;
    }

    .ppid-kalender-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.2rem 0.65rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .ppid-kalender-badge.is-available {
        background: color-mix(in srgb, var(--green) 14%, #fff);
        color: var(--green);
    }

    .ppid-kalender-meta-type {
        font-size: 0.8125rem;
        color: var(--muted);
    }

    .ppid-kalender-title {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.3;
        color: var(--navy);
    }

    @media (min-width: 768px) {
        .ppid-kalender-title {
            font-size: 1.45rem;
        }
    }

    .ppid-kalender-description {
        margin: 0.75rem 0 0;
        max-width: 46rem;
        font-size: 0.975rem;
        line-height: 1.7;
        color: var(--muted);
    }

    .ppid-kalender-actions {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 1.15rem;
    }

    @media (min-width: 640px) {
        .ppid-kalender-actions {
            flex-direction: row;
            align-items: center;
        }
    }

    .ppid-kalender-button {
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

    .ppid-kalender-button:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    @media (max-width: 639px) {
        .ppid-kalender-button {
            width: 100%;
        }
    }

    .ppid-kalender-button-primary {
        background: var(--navy);
        color: #fff;
    }

    .ppid-kalender-button-primary:hover {
        background: var(--navy-dark);
    }

    .ppid-kalender-button-secondary {
        background: #fff;
        border-color: var(--border);
        color: var(--navy);
    }

    .ppid-kalender-button-secondary:hover {
        border-color: var(--navy);
        background: var(--surface);
    }

    /* IMAGE VIEWER */
    .ppid-kalender-image-viewer {
        margin-top: 1.5rem;
    }

    .ppid-kalender-image-container {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }

    .ppid-kalender-image-link {
        display: block;
        cursor: zoom-in;
    }

    .ppid-kalender-image {
        display: block;
        width: 100%;
        height: auto;
        object-fit: contain;
    }

    .ppid-kalender-image-caption {
        margin: 0.75rem 0 0;
        text-align: center;
        font-size: 0.875rem;
        color: var(--muted);
    }

    /* PDF VIEWER */
    .ppid-kalender-pdf-viewer {
        margin-top: 1.5rem;
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: #f8fafc;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }

    .ppid-kalender-pdf-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        background: #fff;
    }

    .ppid-kalender-pdf-heading {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 700;
        color: var(--navy);
    }

    .ppid-kalender-pdf-container {
        background: #fff;
    }

    .ppid-kalender-pdf-frame {
        display: block;
        width: 100%;
        min-height: 550px;
        height: 650px;
        border: 0;
        background: #fff;
    }

    @media (min-width: 768px) {
        .ppid-kalender-pdf-frame {
            min-height: 750px;
            height: 850px;
        }
    }

    @media (min-width: 1024px) {
        .ppid-kalender-pdf-frame {
            min-height: 900px;
            height: 1000px;
        }
    }

    .ppid-kalender-pdf-fallback {
        margin: 0;
        padding: 0.85rem 1rem 1rem;
        border-top: 1px solid var(--border);
        background: #fff;
        font-size: 0.875rem;
        line-height: 1.6;
        color: var(--muted);
    }

    .ppid-kalender-pdf-fallback a {
        color: var(--green);
        font-weight: 600;
        text-decoration: none;
    }

    .ppid-kalender-pdf-fallback a:hover {
        text-decoration: underline;
        text-underline-offset: 2px;
    }

    /* DOWNLOAD ONLY */
    .ppid-kalender-download-only {
        margin-top: 1.5rem;
        padding: 2rem 1.5rem;
        text-align: center;
        border: 1px dashed var(--border);
        border-radius: 1rem;
        background: var(--surface);
    }

    .ppid-kalender-download-text {
        margin: 0;
        color: var(--muted);
        font-size: 0.975rem;
    }

    /* EMPTY & NOT FOUND */
    .ppid-kalender-empty,
    .ppid-kalender-not-found {
        text-align: center;
        padding: 3rem 1.5rem;
        border: 1px dashed var(--border);
        border-radius: 1rem;
        background: var(--surface);
    }

    .ppid-kalender-empty-icon,
    .ppid-kalender-not-found-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 4rem;
        height: 4rem;
        border-radius: 9999px;
        background: #fff;
        color: var(--muted);
        margin-bottom: 1rem;
    }

    .ppid-kalender-empty-title,
    .ppid-kalender-not-found-title {
        margin: 0 0 0.5rem;
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--navy);
    }

    .ppid-kalender-empty-text,
    .ppid-kalender-not-found-text {
        margin: 0;
        max-width: 28rem;
        margin-inline: auto;
        font-size: 0.975rem;
        line-height: 1.7;
        color: var(--muted);
    }

    .ppid-kalender-not-found-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 1.25rem;
        padding: 0.6rem 1.25rem;
        border-radius: 0.5rem;
        background: var(--navy);
        color: #fff;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.15s ease;
    }

    .ppid-kalender-not-found-link:hover {
        background: var(--navy-dark);
    }
</style>
