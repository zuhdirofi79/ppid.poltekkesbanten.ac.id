{{-- Section pada /informasi-publik/setiap-saat --}}
<!-- PPID-STATISTIK-KEPEGAWAIAN-DEPLOY-20261002-A -->
<section id="statistik-kepegawaian" class="statistik-kepegawaian" aria-labelledby="statistik-kepegawaian-title">
    <h2 id="statistik-kepegawaian-title" class="font-heading text-xl font-bold text-navy sm:text-2xl">Statistik Kepegawaian</h2>
    <p class="mt-2 max-w-3xl text-base leading-relaxed text-ink">{{ \App\Support\StatistikKepegawaian::INTRO }}</p>
    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-muted sm:text-base">{{ \App\Support\StatistikKepegawaian::NOTE }}</p>

    @foreach ($groups as $group)
        <p class="mt-6 inline-flex rounded-full border border-line bg-white px-3 py-1 text-sm font-semibold text-navy">
            Periode Data: {{ $group['label'] }}
        </p>

        <div class="mt-4 space-y-6">
            @foreach ($group['charts'] as $chart)
                <article class="statistik-kepegawaian-card">
                    <p class="statistik-kepegawaian-meta">
                        <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                        <span>JPEG / Statistik</span>
                    </p>
                    <figure>
                        <a
                            class="statistik-kepegawaian-link"
                            href="{{ asset($chart['path']) }}"
                            data-statistik-preview
                            data-alt="{{ $chart['alt'] }}"
                            data-caption="{{ $chart['caption'] }}"
                        >
                            <img
                                src="{{ asset($chart['path']) }}"
                                alt="{{ $chart['alt'] }}"
                                width="{{ $chart['width'] }}"
                                height="{{ $chart['height'] }}"
                                class="statistik-kepegawaian-photo"
                                loading="lazy"
                                decoding="async"
                            >
                        </a>
                        <figcaption class="statistik-kepegawaian-caption">{{ $chart['caption'] }}</figcaption>
                    </figure>
                    <a class="ppid-berkala-link" href="{{ asset($chart['path']) }}" target="_blank" rel="noopener noreferrer">
                        Lihat Gambar Penuh
                        <span aria-hidden="true">↗</span>
                    </a>
                </article>
            @endforeach
        </div>
    @endforeach
</section>

<dialog id="statistik-kepegawaian-lightbox" class="statistik-kepegawaian-lightbox" aria-labelledby="statistik-kepegawaian-lightbox-caption">
    <button type="button" class="statistik-kepegawaian-close" data-statistik-close>Tutup</button>
    <img alt="" width="1448" height="1086">
    <p id="statistik-kepegawaian-lightbox-caption" data-statistik-caption></p>
</dialog>

<style>
    .statistik-kepegawaian + .statistik-kepegawaian-list,
    .statistik-kepegawaian {
        margin-bottom: 2rem;
    }

    .statistik-kepegawaian-card {
        overflow: hidden;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.75rem;
        background: #fff;
        padding: 1rem;
        box-shadow: 0 1px 2px rgba(23, 24, 79, 0.06);
    }

    .statistik-kepegawaian-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin: 0 0 0.85rem;
        color: var(--muted, #5c6570);
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .statistik-kepegawaian-link {
        display: block;
        max-width: 100%;
        background: var(--surface, #f7f8fa);
    }

    .statistik-kepegawaian-link:focus-visible,
    .statistik-kepegawaian-close:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 2px;
    }

    .statistik-kepegawaian-photo {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        object-fit: contain;
        background: var(--surface, #f7f8fa);
    }

    .statistik-kepegawaian-card figure {
        margin: 0;
    }

    .statistik-kepegawaian-caption {
        margin-top: 0.85rem;
        color: var(--navy, #17184f);
        font-size: 0.9375rem;
        font-weight: 600;
        line-height: 1.45;
    }

    .statistik-kepegawaian-lightbox {
        width: min(1100px, calc(100vw - 2rem));
        max-height: calc(100dvh - 2rem);
        padding: 1rem;
        border: 0;
        border-radius: 0.75rem;
        background: #fff;
    }

    .statistik-kepegawaian-lightbox::backdrop {
        background: rgba(16, 17, 59, 0.72);
    }

    .statistik-kepegawaian-lightbox img {
        display: block;
        width: 100%;
        height: auto;
        max-height: calc(100dvh - 8rem);
        object-fit: contain;
        background: var(--surface, #f7f8fa);
    }

    .statistik-kepegawaian-lightbox p {
        margin-top: 0.75rem;
        color: var(--navy, #17184f);
        font-weight: 600;
    }

    .statistik-kepegawaian-close {
        margin-bottom: 0.75rem;
        min-height: 2.5rem;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.5rem;
        background: #fff;
        padding: 0.4rem 0.9rem;
        color: var(--navy, #17184f);
        font-weight: 600;
    }
</style>

@push('scripts')
    <script>
        (function () {
            var dialog = document.getElementById('statistik-kepegawaian-lightbox');
            if (!dialog || typeof dialog.showModal !== 'function') {
                return;
            }

            var image = dialog.querySelector('img');
            var caption = dialog.querySelector('[data-statistik-caption]');
            var closeButton = dialog.querySelector('[data-statistik-close]');

            document.querySelectorAll('[data-statistik-preview]').forEach(function (link) {
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
