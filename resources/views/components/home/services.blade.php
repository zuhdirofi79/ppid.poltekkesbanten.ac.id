@props(['services' => collect()])

@php
    $hints = [
        'permohonan-informasi' => 'Ajukan permintaan informasi',
        'pengajuan-keberatan' => 'Keberatan atas layanan informasi',
        'pengaduan-penyalahgunaan-wewenang' => 'Laporkan dugaan penyalahgunaan',
        'keluhan-pelayanan' => 'Sampaikan keluhan layanan',
        'surat-keterangan' => 'Akses informasi surat keterangan',
        'alur-permohonan-informasi' => 'Lihat tahapan pengajuan',
        'alur-pengajuan-keberatan' => 'Lihat tahapan keberatan',
        'alur-pengajuan-sengketa' => 'Tata cara pengaduan',
    ];

    $icons = [
        'permohonan-informasi' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M8 13h4"/><path d="M15.2 12.2 18.8 8.6l1.8 1.8-3.6 3.6-2 .4z"/>',
        'pengajuan-keberatan' => '<rect x="7" y="3" width="10" height="4" rx="1"/><rect x="5" y="5" width="14" height="16" rx="2"/><path d="M9 12h6M9 16h4"/>',
        'pengaduan-penyalahgunaan-wewenang' => '<path d="M12 3 5 6v6c0 4.2 2.9 7.2 7 8.5 4.1-1.3 7-4.3 7-8.5V6z"/><path d="M12 8.5v4"/><path d="M12 15.5h.01"/>',
        'keluhan-pelayanan' => '<path d="M7 17.2 4.5 20.5l4.4-1.2A8.2 8.2 0 1 0 7 17.2z"/><path d="M12 9v4"/><path d="M12 16h.01"/>',
        'surat-keterangan' => '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h5"/><circle cx="15.5" cy="16.2" r="2.1"/>',
        'alur-permohonan-informasi' => '<rect x="3" y="4" width="6.5" height="4.5" rx="1"/><rect x="14.5" y="4" width="6.5" height="4.5" rx="1"/><rect x="8.75" y="15.5" width="6.5" height="4.5" rx="1"/><path d="M9.5 6.2h5M18 8.5v2.2a2 2 0 0 1-2 2H8a2 2 0 0 0-2 2v1M12 13.7v1.8"/>',
        'alur-pengajuan-keberatan' => '<rect x="3" y="4" width="6.5" height="4.5" rx="1"/><rect x="14.5" y="4" width="6.5" height="4.5" rx="1"/><rect x="8.75" y="15.5" width="6.5" height="4.5" rx="1"/><path d="M9.5 6.2h5M18 8.5v2.2a2 2 0 0 1-2 2H8a2 2 0 0 0-2 2v1M12 13.7v1.8"/>',
        'alur-pengajuan-sengketa' => '<path d="M12 3 5 6v6c0 4.2 2.9 7.2 7 8.5 4.1-1.3 7-4.3 7-8.5V6z"/><path d="M9 12h6M12 9v6"/>',
    ];

    $fallbackIcon = '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>';
@endphp

<section class="ppid-home-services" aria-labelledby="layanan-heading">
    <div class="ppid-home-services-wrap">
        <div class="ppid-home-services-intro">
            <p class="ppid-home-services-kicker">Layanan</p>
            <h2 id="layanan-heading">Layanan Informasi Publik</h2>
            <p>Pilih layanan sesuai kebutuhan Anda.</p>
        </div>

        <div class="ppid-home-service-grid">
            @foreach ($services as $service)
                @php
                    $slug = (string) ($service['slug'] ?? '');
                    $isExternal = filled($service['external_url'] ?? null);
                    $href = $isExternal
                        ? $service['external_url']
                        : route('services.show', ['slug' => $slug]);
                    $hint = $hints[$slug] ?? null;
                @endphp
                <a
                    href="{{ $href }}"
                    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                    class="ppid-home-service-card"
                >
                    <span class="ppid-home-service-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            {!! $icons[$slug] ?? $fallbackIcon !!}
                        </svg>
                    </span>
                    <h3>{{ $service['title'] }}</h3>
                    @if ($hint)
                        <p>{{ $hint }}</p>
                    @endif
                    <span class="ppid-home-service-arrow" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </span>
                    @if ($isExternal)
                        <span class="sr-only">(membuka tab baru)</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>

<style>
    .ppid-home-services {
        background: #fff;
        padding: 3.25rem 0 1.25rem;
    }

    .ppid-home-services-wrap {
        width: min(100% - 2rem, 72rem);
        margin-inline: auto;
    }

    .ppid-home-services-intro {
        max-width: 36rem;
        margin: 0 auto 2.5rem;
        text-align: center;
    }

    .ppid-home-services-kicker {
        margin: 0;
        color: var(--green);
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .ppid-home-services-intro h2 {
        margin: 0.45rem 0 0;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.25;
    }

    .ppid-home-services-intro > p:last-child {
        margin: 0.75rem 0 0;
        color: var(--muted);
        font-size: 1rem;
        line-height: 1.5;
    }

    .ppid-home-service-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.15rem;
    }

    .ppid-home-service-card {
        display: flex;
        min-height: 13.5rem;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.55rem;
        padding: 1.5rem 1.25rem 1.35rem;
        border: 1px solid var(--border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
        color: var(--navy);
        text-align: center;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 200ms ease-out, color 200ms ease-out, border-color 200ms ease-out, box-shadow 200ms ease-out, transform 200ms ease-out;
    }

    .ppid-home-service-card h3 {
        margin: 0;
        max-width: 16rem;
        color: inherit;
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.3;
    }

    .ppid-home-service-card p {
        margin: 0;
        max-width: 16rem;
        color: var(--muted);
        font-size: 0.875rem;
        line-height: 1.4;
    }

    .ppid-home-service-icon {
        display: flex;
        width: 5.25rem;
        height: 5.25rem;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: color-mix(in srgb, var(--green) 14%, #fff);
        color: var(--navy);
    }

    .ppid-home-service-icon svg {
        width: 3.35rem;
        height: 3.35rem;
    }

    .ppid-home-service-arrow {
        display: inline-flex;
        margin-top: 0.15rem;
        color: var(--green);
    }

    .ppid-home-service-arrow svg {
        width: 1.05rem;
        height: 1.05rem;
    }

    .ppid-home-service-card:hover,
    .ppid-home-service-card:focus-visible {
        border-color: var(--navy);
        background: var(--navy);
        color: #fff;
        box-shadow: 0 12px 28px rgb(23 24 79 / 0.18);
        transform: translateY(-2px);
    }

    .ppid-home-service-card:hover p,
    .ppid-home-service-card:focus-visible p,
    .ppid-home-service-card:hover .ppid-home-service-arrow,
    .ppid-home-service-card:focus-visible .ppid-home-service-arrow {
        color: rgb(255 255 255 / 0.86);
    }

    .ppid-home-service-card:hover .ppid-home-service-icon,
    .ppid-home-service-card:focus-visible .ppid-home-service-icon {
        background: rgb(255 255 255 / 0.14);
        color: #fff;
    }

    .ppid-home-service-card:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 3px;
    }

    @media (min-width: 640px) {
        .ppid-home-service-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.25rem;
        }

        .ppid-home-service-card {
            min-height: 15rem;
            padding: 1.75rem 1.4rem;
        }

        .ppid-home-service-icon {
            width: 6rem;
            height: 6rem;
        }

        .ppid-home-service-icon svg {
            width: 3.9rem;
            height: 3.9rem;
        }

        .ppid-home-service-card h3 {
            font-size: 1.15rem;
        }
    }

    @media (min-width: 1024px) {
        .ppid-home-services-intro h2 {
            font-size: 1.875rem;
        }

        .ppid-home-service-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .ppid-home-service-card {
            min-height: 15.5rem;
            padding: 1.85rem 1.5rem;
        }

        .ppid-home-service-icon {
            width: 6.5rem;
            height: 6.5rem;
        }

        .ppid-home-service-icon svg {
            width: 4.35rem;
            height: 4.35rem;
        }

        .ppid-home-service-card h3 {
            font-size: 1.2rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ppid-home-service-card:hover,
        .ppid-home-service-card:focus-visible {
            transform: none;
        }
    }
</style>
