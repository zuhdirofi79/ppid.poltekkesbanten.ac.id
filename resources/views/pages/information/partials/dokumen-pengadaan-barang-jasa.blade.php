{{-- PPID-DOKUMEN-PBJ-DEPLOY-20261006-B --}}
<section class="dokumen-pbj" id="surat-perjanjian-pengadaan-barang-jasa" aria-labelledby="dokumen-pbj-title">
    <h2 id="dokumen-pbj-title" class="dokumen-pbj-title">{{ $access['title'] }}</h2>
    <p class="dokumen-pbj-desc">{{ $access['description'] }}</p>

    <article class="dokumen-pbj-card">
        <div class="dokumen-pbj-meta">
            <span class="dokumen-pbj-badge">TERSEDIA</span>
            <span class="dokumen-pbj-type">{{ $access['format_label'] }}</span>
        </div>
        <p class="dokumen-pbj-kicker">{{ $access['document_label'] }}</p>

        <figure class="dokumen-pbj-figure">
            <a class="dokumen-pbj-image-link"
               href="{{ $access['image_url'] }}"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="{{ $access['open_aria'] }}">
                <img
                    class="dokumen-pbj-image"
                    src="{{ $access['image_url'] }}"
                    alt="{{ $access['image_alt'] }}"
                    loading="lazy"
                >
            </a>
        </figure>

        <div class="dokumen-pbj-actions">
            <a class="dokumen-pbj-cta"
               href="{{ $access['image_url'] }}"
               target="_blank"
               rel="noopener noreferrer">{{ $access['open_label'] }}</a>
            <a class="dokumen-pbj-download"
               href="{{ $access['image_url'] }}"
               download="{{ $access['download_name'] }}">{{ $access['download_label'] }}</a>
        </div>
    </article>
</section>

<style>
    .dokumen-pbj {
        margin-bottom: 2rem;
        max-width: 100%;
    }

    .dokumen-pbj-title {
        color: var(--navy, #17184f);
        font-family: var(--font-heading, Georgia, serif);
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 700;
        line-height: 1.25;
        overflow-wrap: anywhere;
    }

    .dokumen-pbj-desc {
        margin-top: 0.75rem;
        max-width: 46rem;
        color: var(--muted, #5c6570);
        font-size: 0.95rem;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .dokumen-pbj-card {
        margin-top: 1.25rem;
        max-width: 100%;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.75rem;
        background: #fff;
        padding: 1.25rem;
    }

    .dokumen-pbj-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 0.75rem;
    }

    .dokumen-pbj-badge {
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

    .dokumen-pbj-type {
        color: var(--muted, #5c6570);
        font-size: 0.8125rem;
        font-weight: 600;
    }

    .dokumen-pbj-kicker {
        margin-top: 0.85rem;
        color: var(--navy, #17184f);
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        line-height: 1.4;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }

    .dokumen-pbj-figure {
        margin: 1rem 0 0;
        max-width: 100%;
    }

    .dokumen-pbj-image-link {
        display: block;
        max-width: 100%;
        border-radius: 0.5rem;
    }

    .dokumen-pbj-image-link:focus-visible,
    .dokumen-pbj-cta:focus-visible,
    .dokumen-pbj-download:focus-visible {
        outline: 2px solid var(--navy, #17184f);
        outline-offset: 3px;
    }

    .dokumen-pbj-image {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
        border: 1px solid var(--line, #e6e8ee);
        border-radius: 0.5rem;
        background: #fff;
    }

    .dokumen-pbj-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
        max-width: 100%;
    }

    .dokumen-pbj-cta,
    .dokumen-pbj-download {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 2.75rem;
        border-radius: 0.5rem;
        padding: 0.7rem 1rem;
        font-size: 0.9375rem;
        font-weight: 700;
        line-height: 1.2;
        text-align: center;
        text-decoration: none;
    }

    .dokumen-pbj-cta {
        background: var(--green, #0f9f6e);
        color: #fff;
    }

    .dokumen-pbj-cta:hover {
        background: #0c8a5e;
    }

    .dokumen-pbj-download {
        border: 1px solid var(--line, #e6e8ee);
        background: #fff;
        color: var(--navy, #17184f);
    }

    .dokumen-pbj-download:hover {
        border-color: var(--navy, #17184f);
    }

    @media (min-width: 640px) {
        .dokumen-pbj-actions {
            flex-direction: row;
            flex-wrap: wrap;
        }

        .dokumen-pbj-cta,
        .dokumen-pbj-download {
            width: auto;
        }
    }
</style>
