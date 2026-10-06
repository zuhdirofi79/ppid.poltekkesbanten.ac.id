{{-- Landing page: /layanan/permohonan-informasi — CTA Google Form, tanpa form internal --}}
<!-- PPID-PERMOHONAN-INFORMASI-DEPLOY-20260907-A -->
@php
    $formUrl = $formUrl ?? null;
    $ppidEmail = $ppidEmail ?? 'humas@poltekkesbanten.ac.id';
    $requirements = [
        'Bukti identitas diri warga negara Indonesia yang sah yang dapat membuktikan Pemohon sebagai warga negara Indonesia (KTP) atau bukti pengesahan badan hukum yang diterbitkan oleh kementerian yang membidangi urusan hukum dan hak asasi manusia dalam hal Pemohon merupakan badan hukum Indonesia;',
        'Selain syarat sebagaimana dimaksud pada angka 1 (satu), juga dilengkapi dengan surat kuasa dan fotokopi Kartu Tanda Penduduk pemberi kuasa dalam hal Pemohon mewakili orang perseorangan, kelompok orang, atau badan hukum Indonesia;',
        'NPWP (Jika ada);',
        'Alamat lengkap;',
        'Alamat email;',
        'Nomor telepon yang dapat dihubungi.',
    ];
    $receiptDocumentUrl = 'https://ppid.poltekkesbanten.ac.id/docs/TANDA%20TERIMA%20PERMOHONAN%20INFORMASI.docx';
    $receiptDownloadName = 'TANDA TERIMA PERMOHONAN INFORMASI.docx';
@endphp

<div class="ppid-request-page">
    <div class="mx-auto max-w-7xl px-5 pb-8 pt-8 sm:px-6 sm:pb-12 sm:pt-9 lg:px-8 lg:pb-16 lg:pt-10">
        @if ($formUrl)
            <!-- PPID-PERMOHONAN-FORM-CTA-DEPLOY-20260907-A -->
            <section class="ppid-request-form-cta" aria-labelledby="request-form-cta-heading">
                <div class="ppid-request-form-cta-inner">
                    <div class="ppid-request-form-cta-copy">
                        <p class="ppid-request-form-eyebrow">Formulir Online</p>
                        <h2 id="request-form-cta-heading" class="ppid-request-form-title">
                            Ajukan Permohonan Informasi Publik
                        </h2>
                        <p class="ppid-request-form-description">
                            Silakan isi formulir resmi permohonan informasi publik melalui tautan berikut.
                        </p>
                    </div>
                    <div class="ppid-request-form-action">
                        <a
                            href="{{ $formUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ppid-request-form-button"
                        >
                            Isi Form Permohonan Informasi Publik
                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M14 5h5v5" />
                                <path d="M10 14 19 5" />
                                <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                            </svg>
                        </a>
                        <p class="ppid-request-form-note">Formulir akan dibuka pada tab baru.</p>
                    </div>
                </div>
            </section>
        @endif

        <!-- PPID-TANDA-TERIMA-PERMOHONAN-DEPLOY-20261006-A -->
        <section id="tanda-terima-permohonan-informasi" class="ppid-request-receipt" aria-labelledby="tanda-terima-heading">
            <div class="ppid-request-section-intro">
                <h2 id="tanda-terima-heading" class="ppid-request-section-title">Tanda Terima Permohonan Informasi</h2>
                <p class="ppid-request-section-subtext">
                    Formulir tanda terima digunakan sebagai bukti penerimaan permohonan informasi publik oleh PPID Poltekkes Kemenkes Banten.
                </p>
            </div>

            <article class="ppid-request-receipt-card" aria-labelledby="tanda-terima-doc-title">
                <div class="ppid-request-receipt-icon" aria-hidden="true">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z" />
                        <path d="M14 3v5h5" />
                        <path d="M8 13h8M8 17h5" />
                    </svg>
                </div>

                <div class="ppid-request-receipt-body">
                    <p class="ppid-request-receipt-meta">
                        <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                        <span>DOCX / Dokumen</span>
                    </p>
                    <h3 id="tanda-terima-doc-title" class="ppid-request-receipt-title">Tanda Terima Permohonan Informasi Publik</h3>
                    <p class="ppid-request-receipt-note">
                        Dokumen dapat dibuka atau diunduh dalam format Microsoft Word (DOCX).
                    </p>
                </div>

                <div class="ppid-request-receipt-actions">
                    <a
                        href="{{ $receiptDocumentUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-request-form-button"
                        aria-label="Buka dokumen Tanda Terima Permohonan Informasi Publik"
                    >
                        Buka Dokumen
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 5h5v5" />
                            <path d="M10 14 19 5" />
                            <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                        </svg>
                    </a>
                    <a
                        href="{{ $receiptDocumentUrl }}"
                        class="ppid-request-secondary-action"
                        download="{{ $receiptDownloadName }}"
                        aria-label="Unduh dokumen Tanda Terima Permohonan Informasi Publik format DOCX"
                    >
                        Unduh DOCX
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 3v12" />
                            <path d="m7 11 5 5 5-5" />
                            <path d="M5 21h14" />
                        </svg>
                    </a>
                </div>
            </article>
        </section>

        <section class="ppid-service-hours" aria-labelledby="service-hours-heading">
            <div class="ppid-request-section-intro">
                <h2 id="service-hours-heading" class="ppid-request-section-title">Waktu Pelayanan Informasi</h2>
                <p class="ppid-request-section-subtext">Hari kerja layanan PPID Poltekkes Kemenkes Banten.</p>
            </div>

            <div class="ppid-service-hours-grid">
                <article class="ppid-service-hour-card">
                    <div class="ppid-service-hour-icon" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M8 3v4M16 3v4M3 11h18" />
                        </svg>
                    </div>
                    <h3 class="ppid-service-hour-day">Senin – Kamis</h3>
                    <p class="ppid-service-hour-time">09.00 – 15.00 WIB</p>
                    <p class="ppid-service-hour-break">Istirahat: 12.00 – 13.00 WIB</p>
                </article>

                <article class="ppid-service-hour-card">
                    <div class="ppid-service-hour-icon" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7v5l3 2" />
                        </svg>
                    </div>
                    <h3 class="ppid-service-hour-day">Jumat</h3>
                    <p class="ppid-service-hour-time">09.00 – 15.00 WIB</p>
                    <p class="ppid-service-hour-break">Istirahat: 11.00 – 13.00 WIB</p>
                </article>
            </div>

            <p class="ppid-service-hours-note">
                Pelayanan dilaksanakan pada hari kerja dan dapat menyesuaikan dengan hari libur nasional atau kebijakan institusi.
            </p>
        </section>

        <section class="ppid-service-description" aria-labelledby="service-description-heading">
            <h2 id="service-description-heading" class="ppid-request-section-title">Pelayanan Informasi</h2>
            <p class="ppid-service-description-text">
                Setiap Badan Publik mempunyai kewajiban untuk membuka akses atas Informasi Publik yang berkaitan dengan Badan Publik tersebut untuk masyarakat luas. Lingkup Badan Publik dalam Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik meliputi lembaga eksekutif, yudikatif, legislatif, serta penyelenggara negara lainnya yang mendapatkan dana dari Anggaran Pendapatan dan Belanja Negara (APBN)/Anggaran Pendapatan dan Belanja Daerah (APBD) dan mencakup pula organisasi nonpemerintah, baik yang berbadan hukum maupun yang tidak berbadan hukum, seperti lembaga swadaya masyarakat, perkumpulan, serta organisasi lainnya yang mengelola atau menggunakan dana yang sebagian atau seluruhnya bersumber dari APBN/APBD, sumbangan masyarakat, dan/atau luar negeri.
            </p>
        </section>

        <aside class="ppid-contact-box" aria-labelledby="ppid-email-heading">
            <p class="ppid-contact-lead">
                Bagi masyarakat yang ingin mendapatkan informasi terkait Poltekkes Kemenkes Banten, dapat mengirimkan permintaan melalui:
            </p>
            <p id="ppid-email-heading" class="ppid-contact-label">Email PPID Poltekkes Kemenkes Banten</p>
            <a class="ppid-contact-email" href="mailto:{{ $ppidEmail }}">
                <span class="ppid-contact-mail-icon" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path d="m3 7 9 6 9-6" />
                    </svg>
                </span>
                {{ $ppidEmail }}
            </a>
        </aside>

        <section class="ppid-requirements" aria-labelledby="requirements-heading">
            <div class="ppid-request-section-intro">
                <h2 id="requirements-heading" class="ppid-request-section-title">Persyaratan Permohonan Informasi Publik</h2>
                <p class="ppid-request-section-subtext">
                    Siapkan dokumen dan data berikut sebelum mengajukan permohonan informasi.
                </p>
            </div>

            <ol class="ppid-requirement-list">
                @foreach ($requirements as $index => $item)
                    <li class="ppid-requirement-item">
                        <span class="ppid-requirement-number" aria-hidden="true">{{ $index + 1 }}</span>
                        <p class="ppid-requirement-text">{{ $item }}</p>
                    </li>
                @endforeach
            </ol>
        </section>

        @if ($formUrl)
            <section class="ppid-request-final-cta" aria-labelledby="final-cta-heading">
                <h2 id="final-cta-heading" class="ppid-request-section-title">Sudah Menyiapkan Persyaratan?</h2>
                <p class="ppid-request-final-text">
                    Silakan lanjutkan pengajuan permohonan informasi melalui formulir resmi PPID Poltekkes Kemenkes Banten.
                </p>
                <div class="ppid-request-final-actions">
                    <a
                        href="{{ $formUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-request-primary-action"
                    >
                        Ajukan Permohonan Sekarang
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 5h5v5" />
                            <path d="M10 14 19 5" />
                            <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}" class="ppid-request-secondary-action">
                        Hubungi PPID
                    </a>
                </div>
            </section>
        @endif
    </div>
</div>
