{{-- Landing page: /layanan/pengajuan-keberatan — CTA Google Form, tanpa form internal --}}
<!-- PPID-PENGAJUAN-KEBERATAN-DEPLOY-20260907-A -->
@php
    $formUrl = $formUrl ?? null;
    $functions = [
        'Mengajukan protes resmi jika permohonan informasi ditolak.',
        'Melaporkan informasi yang tidak ditanggapi atau tidak dipenuhi sesuai ketentuan waktu.',
        'Menyampaikan ketidakpuasan terhadap tanggapan atau keputusan Pejabat Pengelola Informasi dan Dokumentasi (PPID).',
    ];
    $reasons = [
        'Permohonan informasi ditolak tanpa alasan yang jelas.',
        'Informasi yang diminta tidak disediakan atau tidak diberikan.',
        'Tanggapan melewati batas waktu yang ditentukan berdasarkan ketentuan yang berlaku.',
        'Biaya layanan informasi yang dikenakan tidak wajar.',
    ];
@endphp

<div class="ppid-objection-page">
    <div class="mx-auto max-w-7xl px-5 pb-8 pt-8 sm:px-6 sm:pb-12 sm:pt-9 lg:px-8 lg:pb-16 lg:pt-10">
        @if ($formUrl)
            <section class="ppid-objection-cta" aria-labelledby="objection-cta-heading">
                <div class="ppid-objection-cta-inner">
                    <div class="ppid-objection-cta-copy">
                        <p class="ppid-objection-eyebrow">Formulir Online</p>
                        <h2 id="objection-cta-heading" class="ppid-objection-title">
                            Ajukan Keberatan Layanan Informasi Publik
                        </h2>
                        <p class="ppid-objection-description">
                            Sampaikan keberatan secara resmi melalui Formulir Pengajuan Keberatan PPID Poltekkes Kemenkes Banten.
                        </p>
                    </div>
                    <div class="ppid-objection-cta-action">
                        <a
                            href="{{ $formUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ppid-objection-button"
                        >
                            Isi Form Pengajuan Keberatan
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M14 5h5v5" />
                                <path d="M10 14 19 5" />
                                <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                            </svg>
                        </a>
                        <p class="ppid-objection-note">Formulir akan dibuka pada tab baru.</p>
                    </div>
                </div>
            </section>
        @endif

        <section class="ppid-objection-section" aria-labelledby="objection-intro-heading">
            <h2 id="objection-intro-heading" class="ppid-objection-section-title">
                Formulir Pengajuan Keberatan PPID Poltekkes Kemenkes Banten
            </h2>
            <p class="ppid-objection-intro">
                Formulir Pengajuan Keberatan PPID Poltekkes Kemenkes Banten adalah dokumen resmi yang digunakan oleh pemohon informasi publik untuk menyampaikan keberatan atas layanan informasi yang diberikan.
            </p>
        </section>

        <section class="ppid-objection-functions" aria-labelledby="objection-functions-heading">
            <h2 id="objection-functions-heading" class="ppid-objection-section-title">Fungsi Utama</h2>
            <ul class="ppid-objection-function-list">
                @foreach ($functions as $item)
                    <li class="ppid-objection-function-item">
                        <span class="ppid-objection-function-icon" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M20 6 9 17l-5-5" />
                            </svg>
                        </span>
                        <p class="ppid-objection-function-text">{{ $item }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="ppid-objection-reasons" aria-labelledby="objection-reasons-heading">
            <h2 id="objection-reasons-heading" class="ppid-objection-section-title">Alasan Pengajuan Keberatan</h2>
            <ul class="ppid-objection-reason-list">
                @foreach ($reasons as $index => $item)
                    <li class="ppid-objection-reason-item">
                        <span class="ppid-objection-reason-number" aria-hidden="true">{{ $index + 1 }}</span>
                        <p class="ppid-objection-reason-text">{{ $item }}</p>
                    </li>
                @endforeach
            </ul>
        </section>

        <aside class="ppid-objection-info">
            <span class="ppid-objection-info-icon" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                    <circle cx="12" cy="12" r="9" />
                    <path d="M12 11v5" />
                    <path d="M12 8h.01" />
                </svg>
            </span>
            <p class="ppid-objection-info-text">
                Keberatan diajukan apabila pemohon informasi merasa layanan informasi publik belum diberikan sebagaimana mestinya.
            </p>
        </aside>

        @if ($formUrl)
            <section class="ppid-objection-final-cta" aria-labelledby="objection-final-heading">
                <h2 id="objection-final-heading" class="ppid-objection-section-title">Ingin Mengajukan Keberatan?</h2>
                <p class="ppid-objection-final-text">
                    Silakan gunakan formulir resmi Pengajuan Keberatan PPID Poltekkes Kemenkes Banten.
                </p>
                <div class="ppid-objection-final-actions">
                    <a
                        href="{{ $formUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-objection-button"
                    >
                        Isi Form Pengajuan Keberatan
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 5h5v5" />
                            <path d="M10 14 19 5" />
                            <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}" class="ppid-objection-secondary">
                        Hubungi PPID
                    </a>
                </div>
            </section>
        @endif
    </div>
</div>
