{{-- Section pada /informasi-publik/setiap-saat, tepat sebelum Statistik Kepegawaian --}}
<!-- PPID-DOKUMEN-PBJ-DEPLOY-20261005-A -->
<section id="surat-perjanjian-pengadaan-barang-jasa" class="dokumen-pbj" aria-labelledby="surat-perjanjian-pengadaan-title">
    <h2 id="surat-perjanjian-pengadaan-title" class="font-heading text-xl font-bold text-navy sm:text-2xl">{{ $access['title'] }}</h2>
    <p class="mt-2 max-w-3xl text-base leading-relaxed text-ink">{{ $access['description'] }}</p>

    <div class="dokumen-pbj-card">
        <div class="dokumen-pbj-layout">
            @if ($access['qr_available'])
                <div class="dokumen-pbj-qr">
                    <a href="{{ $access['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $access['link_label'] }}">
                        <img
                            src="{{ asset($access['qr_path']) }}"
                            alt="{{ $access['qr_alt'] }}"
                            class="dokumen-pbj-qr-image"
                            width="360"
                            height="360"
                            loading="lazy"
                            decoding="async"
                        >
                    </a>
                </div>
            @endif

            <div class="dokumen-pbj-copy">
                <p class="dokumen-pbj-meta">
                    <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                    <span>Tautan / Dokumen PBJ</span>
                </p>
                <p class="dokumen-pbj-kicker">Dokumen Pengadaan Barang dan Jasa</p>
                <p class="mt-2 text-sm leading-relaxed text-ink sm:text-base">{{ $access['hint'] }}</p>
                <a class="dokumen-pbj-cta" href="{{ $access['url'] }}" target="_blank" rel="noopener noreferrer">
                    {{ $access['cta'] }}
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M5.22 14.78a.75.75 0 0 1 0-1.06L12.94 6H8.75a.75.75 0 0 1 0-1.5h6.5a.75.75 0 0 1 .75.75v6.5a.75.75 0 0 1-1.5 0V7.06l-7.72 7.72a.75.75 0 0 1-1.06 0Z"/>
                    </svg>
                </a>
                <a class="dokumen-pbj-url" href="{{ $access['url'] }}" target="_blank" rel="noopener noreferrer">{{ $access['display_url'] }}</a>
            </div>
        </div>
    </div>
</section>

<style>
    .dokumen-pbj {
        margin-bottom: 2.5rem;
    }

    .dokumen-pbj-card {
        margin-top: 1.25rem;
        overflow: hidden;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.75rem;
        background: #fff;
        padding: 1.25rem;
        box-shadow: 0 1px 2px rgba(23, 24, 79, 0.06);
    }

    .dokumen-pbj-layout {
        display: grid;
        gap: 1.5rem;
        align-items: center;
    }

    .dokumen-pbj-qr {
        display: flex;
        justify-content: center;
    }

    .dokumen-pbj-qr a:focus-visible,
    .dokumen-pbj-cta:focus-visible,
    .dokumen-pbj-url:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 3px;
    }

    .dokumen-pbj-qr-image {
        display: block;
        width: 100%;
        max-width: 360px;
        height: auto;
        object-fit: contain;
        background: #fff;
    }

    .dokumen-pbj-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
        color: var(--muted, #5c6570);
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .dokumen-pbj-kicker {
        margin: 0.85rem 0 0;
        color: var(--navy, #17184f);
        font-family: var(--font-heading);
        font-size: 1rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .dokumen-pbj-cta {
        display: inline-flex;
        width: 100%;
        min-height: 2.75rem;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 1rem;
        border-radius: 0.5rem;
        background: var(--green, #0f9f6e);
        padding: 0.65rem 1.1rem;
        color: #fff;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.35;
        text-align: center;
        text-decoration: none;
    }

    .dokumen-pbj-cta:hover {
        background: var(--green-light, #14b87a);
    }

    .dokumen-pbj-url {
        display: inline-block;
        margin-top: 0.85rem;
        color: var(--green, #0f9f6e);
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.45;
        overflow-wrap: anywhere;
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    @media (min-width: 1024px) {
        .dokumen-pbj-layout {
            grid-template-columns: minmax(0, 360px) minmax(0, 1fr);
        }

        .dokumen-pbj-cta {
            width: auto;
        }
    }
</style>
