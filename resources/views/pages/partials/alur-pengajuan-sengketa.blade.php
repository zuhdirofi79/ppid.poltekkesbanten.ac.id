{{-- Landing page: /layanan/alur-pengajuan-sengketa --}}
<!-- PPID-PENGADUAN-WEWENANG-LAYANAN-20261002 -->
@php
    $complaintFormUrl = $complaintFormUrl ?? null;
    $complaintTypes = [
        ['Penyalahgunaan wewenang atau jabatan', 'M4.75 4A2.75 2.75 0 0 0 2 6.75v6.5A2.75 2.75 0 0 0 4.75 16h6.5A2.75 2.75 0 0 0 14 13.25v-6.5A2.75 2.75 0 0 0 11.25 4h-6.5Zm1.5 3.5a.75.75 0 0 1 .75-.75h2a.75.75 0 0 1 0 1.5h-2a.75.75 0 0 1-.75-.75Zm0 3a.75.75 0 0 1 .75-.75h4.5a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75Z'],
        ['Pelanggaran disiplin atau kode etik', 'M10 2.5 3.5 5.2v4.1c0 3.4 2.5 6.5 6.5 7.7 4-1.2 6.5-4.3 6.5-7.7V5.2L10 2.5Zm0 5.2a.75.75 0 0 1 .75.75v3.1l1.8 1.05a.75.75 0 1 1-.75 1.3l-2.2-1.28a.75.75 0 0 1-.35-.65V8.45A.75.75 0 0 1 10 7.7Z'],
        ['Dugaan korupsi, kolusi, dan nepotisme', 'M10 2a.75.75 0 0 1 .75.75V4h2.5a.75.75 0 0 1 0 1.5h-6.5a.75.75 0 0 1 0-1.5H9.25v-1.25A.75.75 0 0 1 10 2Zm-4.5 5h9A1.5 1.5 0 0 1 16 8.5v6A1.5 1.5 0 0 1 14.5 16h-9A1.5 1.5 0 0 1 4 14.5v-6A1.5 1.5 0 0 1 5.5 7Z'],
        ['Gratifikasi yang tidak sesuai ketentuan', 'M4 6.5A2.5 2.5 0 0 1 6.5 4h7A2.5 2.5 0 0 1 16 6.5V8h.5A1.5 1.5 0 0 1 18 9.5v5A1.5 1.5 0 0 1 16.5 16h-13A1.5 1.5 0 0 1 2 14.5v-5A1.5 1.5 0 0 1 3.5 8H4V6.5Zm1.5 0V8h9V6.5a1 1 0 0 0-1-1h-7a1 1 0 0 0-1 1Z'],
        ['Konflik kepentingan', 'M7 4.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5Zm6 1a2 2 0 1 1 0 4 2 2 0 0 1 0-4ZM3.5 15.2c.4-1.8 2-3.2 4.1-3.2 2 0 3.6 1.4 4 3.2.1.4-.2.8-.6.8H4.1c-.4 0-.7-.4-.6-.8Zm8.2-.4c-.2-1.3-.3-2.2.2-3 .6.2 1.1.5 1.5.9 1 .9 1.5 2.1 1.6 3.3 0 .3-.3.6-.6.6h-2.2c-.2 0-.4-.2-.5-.4v-.4Z'],
        ['Pungutan liar', 'M10 2.75a.75.75 0 0 1 .75.75v1.1a4.25 4.25 0 0 1 2.9 5.55.75.75 0 1 1-1.42-.48 2.75 2.75 0 0 0-2.23-3.42V9.5h.25a.75.75 0 0 1 0 1.5h-.25v.35a2.75 2.75 0 0 0 2.23 3.42.75.75 0 1 1-1.42.48 4.25 4.25 0 0 1-2.9-5.55V5.5H8.5a.75.75 0 0 1 0-1.5h.75V3.5A.75.75 0 0 1 10 2.75Z'],
        ['Penyimpangan dalam pelayanan publik', 'M4 4.5A1.5 1.5 0 0 1 5.5 3h9A1.5 1.5 0 0 1 16 4.5v11a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 4 15.5v-11Zm2.25 2a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Zm0 3a.75.75 0 0 0 0 1.5h7.5a.75.75 0 0 0 0-1.5h-7.5Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Z'],
        ['Pelanggaran terhadap ketentuan atau peraturan yang berlaku', 'M6 2.75A1.75 1.75 0 0 0 4.25 4.5v11c0 .97.78 1.75 1.75 1.75h8A1.75 1.75 0 0 0 15.75 15.5v-8.1a.75.75 0 0 0-.22-.53l-3.9-3.9a.75.75 0 0 0-.53-.22H6Zm5.25 1.56 2.44 2.44H11.25V4.31Z'],
    ];
    $requirements = [
        'Identitas pelapor, apabila diperlukan sesuai mekanisme kanal pengaduan.',
        'Identitas atau keterangan pihak yang dilaporkan.',
        'Uraian kejadian atau dugaan pelanggaran.',
        'Waktu dan tempat kejadian.',
        'Bukti atau dokumen pendukung, apabila tersedia.',
        'Informasi lain yang dapat membantu proses pemeriksaan atau klarifikasi.',
    ];
    $steps = [
        'Pengaduan Diterima',
        'Verifikasi Awal',
        'Klarifikasi / Penelaahan',
        'Tindak Lanjut Sesuai Kewenangan',
        'Penyampaian Hasil / Status Pengaduan',
    ];
@endphp

<div class="ppid-pengaduan-page">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <section aria-labelledby="jenis-pengaduan-title">
            <h2 id="jenis-pengaduan-title" class="ppid-pengaduan-title">Jenis Pengaduan</h2>
            <p class="ppid-pengaduan-lead">Pengaduan dapat disampaikan antara lain terkait:</p>
            <ul class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($complaintTypes as [$label, $icon])
                    <li class="ppid-pengaduan-chip">
                        <svg class="h-5 w-5 shrink-0 text-green" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="{{ $icon }}" />
                        </svg>
                        <span>{{ $label }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section class="ppid-pengaduan-panel" aria-labelledby="tata-cara-title">
            <h2 id="tata-cara-title" class="ppid-pengaduan-title">Tata Cara Penyampaian Pengaduan</h2>
            <p class="ppid-pengaduan-lead">
                Pengaduan disampaikan melalui kanal pengaduan resmi dengan mencantumkan informasi yang memadai agar dapat dilakukan verifikasi dan tindak lanjut.
            </p>
            <h3 class="mt-6 font-heading text-base font-bold text-navy">Pengaduan sekurang-kurangnya memuat:</h3>
            <ol class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach ($requirements as $index => $requirement)
                    <li class="ppid-pengaduan-check">
                        <span class="ppid-pengaduan-index">{{ $index + 1 }}</span>
                        <span>{{ $requirement }}</span>
                    </li>
                @endforeach
            </ol>
            <p class="ppid-pengaduan-callout">Pelapor diharapkan menyampaikan informasi secara jelas, benar, dan dapat dipertanggungjawabkan.</p>
        </section>

        <section class="ppid-pengaduan-panel" aria-labelledby="kanal-pengaduan-title">
            <h2 id="kanal-pengaduan-title" class="ppid-pengaduan-title">Kanal Pengaduan</h2>
            <p class="ppid-pengaduan-lead">Pengaduan dapat disampaikan melalui kanal resmi yang tersedia, antara lain:</p>
            <div class="mt-5 grid gap-4 lg:grid-cols-3">
                <article class="ppid-pengaduan-channel">
                    <h3 class="font-heading text-base font-bold text-navy">Whistleblowing System (WBS) Kementerian Kesehatan</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink">Kanal pelaporan dugaan pelanggaran melalui Whistleblowing System yang berlaku di lingkungan Kementerian Kesehatan.</p>
                    <a href="https://wbs.kemkes.go.id/" class="ppid-pengaduan-button" target="_blank" rel="noopener noreferrer">
                        Buka WBS Kemenkes
                        <span class="sr-only"> (membuka tab baru)</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.22 14.78a.75.75 0 0 1 0-1.06L12.94 6H8.75a.75.75 0 0 1 0-1.5h6.5a.75.75 0 0 1 .75.75v6.5a.75.75 0 0 1-1.5 0V7.06l-7.72 7.72a.75.75 0 0 1-1.06 0Z"/></svg>
                    </a>
                </article>
                <article class="ppid-pengaduan-channel">
                    <h3 class="font-heading text-base font-bold text-navy">SP4N-LAPOR!</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink">Sistem Pengelolaan Pengaduan Pelayanan Publik Nasional.</p>
                    <a href="https://www.lapor.go.id/" class="ppid-pengaduan-button" target="_blank" rel="noopener noreferrer">
                        Buka SP4N-LAPOR!
                        <span class="sr-only"> (membuka tab baru)</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.22 14.78a.75.75 0 0 1 0-1.06L12.94 6H8.75a.75.75 0 0 1 0-1.5h6.5a.75.75 0 0 1 .75.75v6.5a.75.75 0 0 1-1.5 0V7.06l-7.72 7.72a.75.75 0 0 1-1.06 0Z"/></svg>
                    </a>
                </article>
                <article class="ppid-pengaduan-channel">
                    <h3 class="font-heading text-base font-bold text-navy">Pengaduan Poltekkes Kemenkes Banten</h3>
                    <p class="mt-2 flex-1 text-sm leading-relaxed text-ink">Sampaikan pengaduan melalui kanal/formulir resmi Poltekkes Kemenkes Banten.</p>
                    @if ($complaintFormUrl)
                        <a href="{{ $complaintFormUrl }}" class="ppid-pengaduan-button" target="_blank" rel="noopener noreferrer">
                            Buka Form Pengaduan
                            <span class="sr-only"> (membuka tab baru)</span>
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.22 14.78a.75.75 0 0 1 0-1.06L12.94 6H8.75a.75.75 0 0 1 0-1.5h6.5a.75.75 0 0 1 .75.75v6.5a.75.75 0 0 1-1.5 0V7.06l-7.72 7.72a.75.75 0 0 1-1.06 0Z"/></svg>
                        </a>
                    @else
                        <button type="button" class="ppid-pengaduan-button ppid-pengaduan-button-disabled" disabled>
                            Form Pengaduan Sedang Disiapkan
                        </button>
                    @endif
                </article>
            </div>
            <aside class="ppid-pengaduan-note">
                <p>Pengaduan juga dapat disampaikan melalui surat tertulis atau layanan pengaduan langsung sesuai mekanisme yang berlaku.</p>
            </aside>
        </section>

        <section class="ppid-pengaduan-panel" aria-labelledby="proses-pengaduan-title">
            <h2 id="proses-pengaduan-title" class="ppid-pengaduan-title">Proses Penanganan Pengaduan</h2>
            <p class="ppid-pengaduan-lead">Setiap pengaduan yang diterima akan melalui tahapan:</p>
            <ol class="ppid-pengaduan-steps">
                @foreach ($steps as $index => $step)
                    <li>
                        <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $step }}</strong>
                    </li>
                @endforeach
            </ol>
            <p class="mt-5 text-sm leading-relaxed text-ink">Pengaduan yang berada di luar kewenangan Poltekkes Kemenkes Banten dapat diteruskan atau diarahkan kepada instansi yang berwenang sesuai ketentuan yang berlaku.</p>
        </section>

        <section class="ppid-pengaduan-security" aria-labelledby="kerahasiaan-title">
            <svg class="h-8 w-8 shrink-0 text-green" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                <path d="M12 3 5 6v6c0 4.2 2.9 7.4 7 8.5 4.1-1.1 7-4.3 7-8.5V6l-7-3Z" />
                <path d="M9.5 12.2 11.2 14l3.3-3.6" />
            </svg>
            <div>
                <h2 id="kerahasiaan-title" class="ppid-pengaduan-title">Kerahasiaan Pelapor</h2>
                <p class="mt-2 text-sm leading-relaxed text-ink sm:text-base">Informasi mengenai identitas pelapor dan materi pengaduan dikelola sesuai dengan ketentuan peraturan perundang-undangan dan mekanisme pada masing-masing kanal pengaduan. Masyarakat diharapkan menggunakan kanal resmi agar pengaduan dapat tercatat dan ditindaklanjuti secara tepat.</p>
            </div>
        </section>

        <section class="ppid-pengaduan-close" aria-labelledby="komitmen-title">
            <h2 id="komitmen-title">Komitmen Poltekkes Kemenkes Banten</h2>
            <p>Poltekkes Kemenkes Banten berkomitmen menindaklanjuti setiap pengaduan secara profesional, objektif, transparan, dan akuntabel sebagai bagian dari upaya mewujudkan pemerintahan yang bersih dan pelayanan publik yang berintegritas.</p>
        </section>
    </div>
</div>

<style>
    .ppid-pengaduan-page { background: #fff; overflow-x: hidden; }
    .ppid-pengaduan-title {
        margin: 0;
        font-family: var(--font-heading);
        font-size: 1.35rem;
        font-weight: 700;
        line-height: 1.3;
        color: var(--navy);
    }
    .ppid-pengaduan-lead { margin: 0.75rem 0 0; max-width: 48rem; color: var(--ink, #1f2937); line-height: 1.7; }
    .ppid-pengaduan-panel {
        margin-top: 1.5rem;
        padding: 1.15rem 1.1rem 1.3rem;
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    .ppid-pengaduan-chip,
    .ppid-pengaduan-check,
    .ppid-pengaduan-channel {
        display: flex;
        gap: 0.75rem;
        min-width: 0;
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        background: var(--surface, #f7f8fb);
        padding: 0.9rem 0.95rem;
        color: var(--navy);
        line-height: 1.45;
    }
    .ppid-pengaduan-chip { align-items: flex-start; font-size: 0.92rem; font-weight: 600; }
    .ppid-pengaduan-check { align-items: flex-start; background: #fff; font-size: 0.95rem; color: var(--ink, #1f2937); }
    .ppid-pengaduan-index {
        display: inline-flex;
        height: 1.75rem;
        width: 1.75rem;
        flex: none;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        background: var(--navy);
        color: #fff;
        font-family: var(--font-heading);
        font-size: 0.8rem;
        font-weight: 700;
    }
    .ppid-pengaduan-callout {
        margin: 1rem 0 0;
        border-left: 4px solid var(--green, #008d70);
        border-radius: 0.5rem;
        background: #f3faf8;
        padding: 0.85rem 1rem;
        color: var(--navy);
        font-weight: 600;
        line-height: 1.6;
    }
    .ppid-pengaduan-channel { flex-direction: column; background: #fff; }
    .ppid-pengaduan-button {
        display: inline-flex;
        min-height: 2.75rem;
        width: 100%;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        margin-top: 1rem;
        border-radius: 0.6rem;
        background: var(--navy);
        color: #fff;
        padding: 0.55rem 0.9rem;
        font-family: var(--font-heading);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
    }
    .ppid-pengaduan-button:hover { background: var(--navy-dark, #10113b); }
    .ppid-pengaduan-button:focus-visible,
    .ppid-pengaduan-page a:focus-visible {
        outline: 2px solid var(--gold, #d7ae62);
        outline-offset: 2px;
    }
    .ppid-pengaduan-button-disabled {
        border: 1px dashed var(--border);
        background: var(--surface, #f7f8fb);
        color: var(--muted, #64748b);
        cursor: not-allowed;
    }
    .ppid-pengaduan-note {
        margin-top: 1rem;
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        background: var(--surface, #f7f8fb);
        padding: 0.95rem 1rem;
        color: var(--ink, #1f2937);
        line-height: 1.65;
    }
    .ppid-pengaduan-steps {
        display: grid;
        gap: 0.75rem;
        margin: 1rem 0 0;
        padding: 0;
        list-style: none;
    }
    .ppid-pengaduan-steps li {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        background: #fff;
        padding: 0.85rem 0.9rem;
    }
    .ppid-pengaduan-steps span {
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--green, #008d70);
    }
    .ppid-pengaduan-steps strong {
        color: var(--navy);
        font-weight: 600;
        line-height: 1.4;
    }
    .ppid-pengaduan-security,
    .ppid-pengaduan-close {
        display: flex;
        gap: 0.9rem;
        margin-top: 1.5rem;
        border-radius: 1rem;
        padding: 1.15rem 1.1rem;
    }
    .ppid-pengaduan-security {
        align-items: flex-start;
        border: 1px solid var(--border);
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    .ppid-pengaduan-close {
        flex-direction: column;
        background: var(--navy);
        color: #fff;
    }
    .ppid-pengaduan-close h2 { margin: 0; font-family: var(--font-heading); font-size: 1.35rem; font-weight: 700; }
    .ppid-pengaduan-close p { margin: 0.7rem 0 0; max-width: 46rem; line-height: 1.7; color: rgb(255 255 255 / 0.92); }
    @media (min-width: 1024px) {
        .ppid-pengaduan-steps { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        .ppid-pengaduan-steps li { flex-direction: column; min-height: 8.5rem; }
    }
</style>
