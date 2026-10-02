{{-- Template: /halaman/lhkpn-pimpinan-tertinggi --}}
<!-- PPID-LHKPN-PIMPINAN-V1 -->
@php
    $documents = collect($documents ?? []);
    $sections = [
        'pimpinan' => ['title' => 'Pimpinan Direktorat', 'items' => $documents->where('group_key', 'pimpinan')->values()],
        'terkait' => ['title' => 'Pejabat Terkait', 'items' => $documents->where('group_key', 'terkait')->values()],
    ];
    $listedIds = $sections['pimpinan']['items']->pluck('id')->merge($sections['terkait']['items']->pluck('id'));
    $others = $documents->reject(fn ($document) => $listedIds->contains($document->id))->values();
    if ($others->isNotEmpty()) {
        $sections['lainnya'] = ['title' => 'Dokumen Lainnya', 'items' => $others];
    }
    $number = 0;
@endphp

<main class="ppid-lhkpn-page">
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

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8" x-data="{ openKey: null }">
        <p class="max-w-3xl text-base leading-relaxed text-ink">
            Halaman ini menyediakan dokumen Laporan Harta Kekayaan Penyelenggara Negara (LHKPN) dan/atau tanda terima pelaporan e-LHKPN yang tersedia untuk pimpinan dan pejabat terkait di lingkungan Poltekkes Kemenkes Banten.
        </p>
        <p class="mt-2 text-sm text-muted">Dokumen LHKPN / tanda terima pelaporan yang tersedia.</p>

        @if ($documents->isEmpty())
            <p class="mt-8 rounded-xl border border-line bg-white px-5 py-6 text-muted">Katalog LHKPN belum ditayangkan.</p>
        @endif

        @foreach ($sections as $section)
            @if ($section['items']->isNotEmpty())
                <section class="mt-8" aria-labelledby="lhkpn-section-{{ $loop->index }}">
                    <h2 id="lhkpn-section-{{ $loop->index }}" class="font-heading text-sm font-bold uppercase tracking-wider text-green">
                        {{ $section['title'] }}
                    </h2>
                    <div class="mt-4 flex flex-col gap-4">
                        @foreach ($section['items'] as $document)
                            @php
                                $number++;
                                $available = $document->isAvailable();
                                $url = $available ? $document->documentUrl() : null;
                                $titleId = 'lhkpn-title-'.$document->position_key;
                                $previewId = 'lhkpn-preview-'.$document->position_key;
                            @endphp
                            <article id="{{ $document->anchor() }}" class="ppid-lhkpn-card" aria-labelledby="{{ $titleId }}">
                                <div class="ppid-lhkpn-identity">
                                    <span class="ppid-lhkpn-order" aria-hidden="true">{{ $number }}</span>
                                    <div class="min-w-0 flex-1">
                                        <p class="ppid-lhkpn-kicker">
                                            <span class="ppid-lhkpn-badge {{ $available ? 'is-available' : 'is-missing' }}">{{ $document->statusLabel() }}</span>
                                            @if ($available)
                                                <span>{{ $document->typeLabel() }}</span>
                                            @endif
                                        </p>
                                        <h3 id="{{ $titleId }}" class="ppid-lhkpn-name">{{ $document->person_name }}</h3>
                                        <p class="ppid-lhkpn-role">{{ $document->position }}</p>
                                        <p class="ppid-lhkpn-status">{{ $document->availabilitySentence() }}</p>
                                    </div>
                                </div>

                                @if ($available && $url)
                                    <div class="ppid-lhkpn-actions">
                                        <a
                                            href="{{ $url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="ppid-lhkpn-button ppid-lhkpn-button-primary"
                                            aria-label="Buka dokumen {{ $document->person_name }}"
                                        >Buka Dokumen</a>
                                        <button
                                            type="button"
                                            class="ppid-lhkpn-button ppid-lhkpn-button-secondary"
                                            @click="openKey = openKey === '{{ $document->position_key }}' ? null : '{{ $document->position_key }}'"
                                            :aria-expanded="(openKey === '{{ $document->position_key }}').toString()"
                                            aria-controls="{{ $previewId }}"
                                        >
                                            <span x-show="openKey !== '{{ $document->position_key }}'">Preview</span>
                                            <span x-show="openKey === '{{ $document->position_key }}'" x-cloak>Tutup Preview</span>
                                        </button>
                                        <a
                                            href="{{ $url }}"
                                            download="{{ $document->downloadFilename() }}"
                                            class="ppid-lhkpn-button ppid-lhkpn-button-secondary"
                                            aria-label="Download PDF {{ $document->person_name }}"
                                        >Download PDF</a>
                                    </div>
                                    <section
                                        id="{{ $previewId }}"
                                        class="ppid-lhkpn-preview"
                                        x-cloak
                                        x-show="openKey === '{{ $document->position_key }}'"
                                        aria-label="Preview dokumen {{ $document->person_name }}"
                                    >
                                        <template x-if="openKey === '{{ $document->position_key }}'">
                                            <iframe
                                                src="{{ $url }}#view=FitH&toolbar=1&navpanes=0"
                                                title="Preview dokumen LHKPN {{ $document->person_name }}"
                                                class="ppid-lhkpn-frame"
                                                loading="lazy"
                                            ></iframe>
                                        </template>
                                    </section>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</main>

<style>
    .ppid-lhkpn-page { background: #fff; }
    .ppid-lhkpn-card {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 1.1rem;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    @media (min-width: 1024px) {
        .ppid-lhkpn-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: center;
            gap: 1rem 1.5rem;
            padding: 1.15rem 1.35rem;
        }
        .ppid-lhkpn-preview { grid-column: 1 / -1; }
    }
    .ppid-lhkpn-identity { display: flex; gap: 0.9rem; min-width: 0; }
    .ppid-lhkpn-order {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 9999px;
        background: color-mix(in srgb, var(--navy) 8%, #fff);
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 0.95rem;
        font-weight: 700;
    }
    .ppid-lhkpn-kicker {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        color: var(--muted);
        font-size: 0.8125rem;
    }
    .ppid-lhkpn-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 9999px;
        padding: 0.15rem 0.6rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .ppid-lhkpn-badge.is-available { background: color-mix(in srgb, var(--green) 14%, #fff); color: var(--green); }
    .ppid-lhkpn-badge.is-missing { background: #fff7ed; color: #9a3412; }
    .ppid-lhkpn-name {
        margin: 0.35rem 0 0;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }
    .ppid-lhkpn-role { margin: 0.2rem 0 0; color: var(--muted); font-size: 0.95rem; line-height: 1.5; }
    .ppid-lhkpn-status { margin: 0.45rem 0 0; color: var(--ink); font-size: 0.9375rem; line-height: 1.5; }
    .ppid-lhkpn-actions { display: flex; flex-direction: column; gap: 0.65rem; }
    @media (min-width: 640px) {
        .ppid-lhkpn-actions { flex-direction: row; flex-wrap: wrap; }
    }
    .ppid-lhkpn-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.75rem;
        padding: 0.5rem 0.95rem;
        border: 1px solid transparent;
        border-radius: 0.5rem;
        background: #fff;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.25;
        text-decoration: none;
        cursor: pointer;
    }
    .ppid-lhkpn-button:focus-visible { outline: 3px solid var(--gold); outline-offset: 2px; }
    @media (max-width: 639px) { .ppid-lhkpn-button { width: 100%; } }
    .ppid-lhkpn-button-primary { background: var(--navy); color: #fff; }
    .ppid-lhkpn-button-primary:hover { background: var(--navy-dark); }
    .ppid-lhkpn-button-secondary { border-color: var(--border); }
    .ppid-lhkpn-button-secondary:hover { border-color: var(--navy); background: var(--surface); }
    .ppid-lhkpn-preview {
        overflow: hidden;
        border: 1px solid var(--border);
        border-radius: 0.85rem;
        background: #f8fafc;
    }
    .ppid-lhkpn-frame {
        display: block;
        width: 100%;
        height: 560px;
        border: 0;
        background: #fff;
    }
    @media (min-width: 768px) { .ppid-lhkpn-frame { height: 650px; } }
    @media (min-width: 1024px) { .ppid-lhkpn-frame { height: 800px; } }
</style>
