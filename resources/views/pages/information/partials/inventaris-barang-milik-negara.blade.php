{{-- Section pada /informasi-publik/setiap-saat. Bukti SAQ: #inventaris-barang-milik-negara --}}
<section id="{{ $access['anchor'] }}" class="inventaris-bmn" aria-labelledby="inventaris-bmn-title">
    <h2 id="inventaris-bmn-title" class="inventaris-bmn-title">{{ $access['heading'] }}</h2>
    <p class="inventaris-bmn-desc">{{ $access['description'] }}</p>

    <article class="inventaris-bmn-card">
        <div class="inventaris-bmn-meta">
            <span class="inventaris-bmn-badge">TERSEDIA</span>
            <span>Portal BMN</span>
        </div>

        <div class="inventaris-bmn-row">
            <div class="inventaris-bmn-copy">
                <p class="inventaris-bmn-kicker">{{ $access['kicker'] }}</p>
                <p class="inventaris-bmn-summary">{{ $access['summary'] }}</p>
                <p class="inventaris-bmn-host">{{ $access['portal_host'] }}</p>
            </div>

            <a class="inventaris-bmn-cta"
               href="{{ $access['portal_url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="{{ $access['cta_aria'] }}">
                <span>{{ $access['cta_label'] }}</span>
                <svg class="inventaris-bmn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M14 4h6v6M10 14 20 4M20 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h5" />
                </svg>
            </a>
        </div>
    </article>
</section>

<style>
    .inventaris-bmn {
        margin-bottom: 2rem;
        max-width: 100%;
        /* Sticky header: top bar py-2 + text-sm ≈ 2.25rem; main row py-3 + logo h-12 = 4.5rem. Total 6.75rem, plus 0.75rem clearance. */
        scroll-margin-top: 7.5rem;
    }

    .inventaris-bmn-title {
        color: var(--navy, #17184f);
        font-family: var(--font-heading, Georgia, serif);
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 700;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .inventaris-bmn-desc,
    .inventaris-bmn-summary,
    .inventaris-bmn-host {
        overflow-wrap: anywhere;
    }

    .inventaris-bmn-desc {
        margin-top: 0.75rem;
        max-width: 46rem;
        color: var(--muted, #5c6570);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .inventaris-bmn-card {
        margin-top: 1.25rem;
        max-width: 100%;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.75rem;
        background: #fff;
        padding: 1.25rem;
        box-shadow: 0 1px 2px rgba(23, 24, 79, 0.06);
    }

    .inventaris-bmn-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 0.75rem;
        color: var(--muted, #5c6570);
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .inventaris-bmn-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        background: #e8f6ee;
        color: #0f7a3a;
        padding: 0.2rem 0.65rem;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .inventaris-bmn-row {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 0.85rem;
        min-width: 0;
    }

    .inventaris-bmn-copy {
        min-width: 0;
        max-width: 46rem;
    }

    .inventaris-bmn-kicker {
        color: var(--navy, #17184f);
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        line-height: 1.4;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }

    .inventaris-bmn-summary {
        margin-top: 0.55rem;
        color: var(--ink, #1c2430);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .inventaris-bmn-host {
        margin-top: 0.45rem;
        color: var(--muted, #5c6570);
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .inventaris-bmn-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        width: 100%;
        min-height: 2.75rem;
        border-radius: 0.5rem;
        background: var(--green, #0f9f6e);
        padding: 0.7rem 1rem;
        color: #fff;
        font-size: 0.9375rem;
        font-weight: 700;
        line-height: 1.2;
        text-align: center;
        text-decoration: none;
    }

    .inventaris-bmn-cta:hover {
        background: #0c8a5e;
    }

    .inventaris-bmn-cta:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 3px;
    }

    .inventaris-bmn-icon {
        width: 0.95rem;
        height: 0.95rem;
        flex-shrink: 0;
    }

    @media (min-width: 769px) {
        .inventaris-bmn-row {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .inventaris-bmn-cta {
            width: auto;
            flex-shrink: 0;
        }
    }
</style>
