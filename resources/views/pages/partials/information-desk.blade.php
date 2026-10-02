{{-- Template khusus: /halaman/information-desk --}}
<!-- PPID-INFORMATION-DESK-DEPLOY-20261002-A -->
@php
    use App\Support\InformationDesk;

    $photos = InformationDesk::photos();
    $intro = trim(strip_tags((string) ($body ?? '')));
@endphp

<div class="information-desk-page">
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

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="information-desk-intro rounded-xl border border-line bg-white p-5 shadow-sm sm:p-6" aria-labelledby="information-desk-facility-title">
            <div class="flex items-start gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy/5 text-navy" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path d="M4 10.5 12 4l8 6.5" />
                        <path d="M6 10v8.5h12V10" />
                        <path d="M10 18.5v-4h4v4" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <h2 id="information-desk-facility-title" class="font-heading text-xl font-bold text-navy sm:text-2xl">
                        Information Desk PPID Poltekkes Kemenkes Banten
                    </h2>
                    <p class="mt-1 text-sm font-semibold text-green">Fasilitas Pelayanan Informasi Publik</p>
                </div>
            </div>

            <div class="information-desk-copy mt-4 max-w-3xl text-base leading-relaxed text-ink">
                @if ($intro !== '')
                    {!! $body !!}
                @else
                    @foreach (InformationDesk::introParagraphs() as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                @endif
            </div>

            <p class="mt-4 border-l-4 border-green bg-surface px-4 py-3 text-sm font-semibold leading-relaxed text-navy sm:text-base">
                {{ InformationDesk::HIGHLIGHT }}
            </p>
        </section>

        <section class="mt-10" aria-labelledby="information-desk-gallery-title">
            <h2 id="information-desk-gallery-title" class="font-heading text-xl font-bold text-navy sm:text-2xl">Dokumentasi Fasilitas</h2>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-muted sm:text-base">Meja pelayanan, kursi petugas, kursi tunggu, ruang penerimaan tamu, dan sarana penerimaan informasi (desk information).</p>

            <ul class="information-desk-gallery mt-6">
                @foreach ($photos as $photo)
                    <li class="information-desk-card">
                        <figure>
                            <a
                                class="information-desk-link"
                                href="{{ asset($photo['path']) }}"
                                data-information-desk-photo
                                data-caption="{{ $photo['caption'] }}"
                                data-alt="{{ $photo['alt'] }}"
                            >
                                <img
                                    src="{{ asset($photo['path']) }}"
                                    alt="{{ $photo['alt'] }}"
                                    width="{{ $photo['width'] }}"
                                    height="{{ $photo['height'] }}"
                                    class="information-desk-photo"
                                    loading="lazy"
                                    decoding="async"
                                >
                            </a>
                            <figcaption>{{ $photo['caption'] }}</figcaption>
                        </figure>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</div>

<dialog id="information-desk-lightbox" class="information-desk-lightbox" aria-labelledby="information-desk-lightbox-caption">
    <button type="button" class="information-desk-lightbox-close" data-lightbox-close>Tutup</button>
    <img alt="" width="1280" height="720">
    <p id="information-desk-lightbox-caption" data-lightbox-caption></p>
</dialog>

<style>
    .information-desk-copy > * + * {
        margin-top: 0.85rem;
    }

    .information-desk-gallery {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .information-desk-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 1px 2px rgba(23, 24, 79, 0.06);
    }

    .information-desk-card figure {
        margin: 0;
    }

    .information-desk-link {
        display: block;
        background: var(--surface, #f7f8fa);
    }

    .information-desk-link:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 2px;
    }

    .information-desk-photo {
        display: block;
        width: 100%;
        height: auto;
        aspect-ratio: 16 / 9;
        object-fit: cover;
        background: var(--surface, #f7f8fa);
    }

    .information-desk-card figcaption {
        padding: 0.85rem 1rem 1rem;
        color: var(--navy, #17184f);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.45;
    }

    @media (min-width: 768px) {
        .information-desk-gallery {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1024px) {
        .information-desk-gallery {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        .information-desk-gallery > :nth-child(-n+2) {
            grid-column: span 3;
        }

        .information-desk-gallery > :nth-child(n+3) {
            grid-column: span 2;
        }
    }

    .information-desk-lightbox {
        width: min(960px, calc(100vw - 2rem));
        max-height: calc(100dvh - 2rem);
        padding: 1rem;
        border: 0;
        border-radius: 0.75rem;
        background: #fff;
    }

    .information-desk-lightbox::backdrop {
        background: rgba(16, 17, 59, 0.72);
    }

    .information-desk-lightbox img {
        display: block;
        width: 100%;
        height: auto;
        max-height: calc(100dvh - 8rem);
        object-fit: contain;
        background: var(--surface, #f7f8fa);
    }

    .information-desk-lightbox p {
        margin-top: 0.75rem;
        color: var(--navy, #17184f);
        font-weight: 600;
    }

    .information-desk-lightbox-close {
        margin-bottom: 0.75rem;
        min-height: 2.5rem;
        border-radius: 0.5rem;
        border: 1px solid var(--line, #e6e8ee);
        background: #fff;
        padding: 0.4rem 0.9rem;
        color: var(--navy, #17184f);
        font-weight: 600;
    }

    .information-desk-lightbox-close:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 2px;
    }
</style>

@push('scripts')
    <script>
        (function () {
            var dialog = document.getElementById('information-desk-lightbox');
            if (!dialog || typeof dialog.showModal !== 'function') {
                return;
            }

            var image = dialog.querySelector('img');
            var caption = dialog.querySelector('[data-lightbox-caption]');
            var closeButton = dialog.querySelector('[data-lightbox-close]');

            document.querySelectorAll('[data-information-desk-photo]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    image.src = link.href;
                    image.alt = link.getAttribute('data-alt') || '';
                    caption.textContent = link.getAttribute('data-caption') || '';
                    dialog.showModal();
                    closeButton.focus();
                });
            });

            closeButton.addEventListener('click', function () {
                dialog.close();
            });

            dialog.addEventListener('click', function (event) {
                if (event.target === dialog) {
                    dialog.close();
                }
            });

            dialog.addEventListener('close', function () {
                image.removeAttribute('src');
                image.alt = '';
            });
        }());
    </script>
@endpush
