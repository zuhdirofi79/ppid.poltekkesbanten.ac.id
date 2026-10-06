{{-- Landing page: /layanan/daftar-registrasi-pemohon — kartu dokumen DOCX, tanpa iframe --}}
<!-- PPID-DAFTAR-REGISTRASI-PEMOHON-DEPLOY-20261006-A -->
@php
    $registerDocumentUrl = 'https://ppid.poltekkesbanten.ac.id/docs/DAFTAR%20REGISTER%20PERMOHONAN.docx';
    $registerDownloadName = 'DAFTAR REGISTER PERMOHONAN.docx';
@endphp

<div class="ppid-request-page">
    <div class="mx-auto max-w-7xl px-5 pb-8 pt-8 sm:px-6 sm:pb-12 sm:pt-9 lg:px-8 lg:pb-16 lg:pt-10">
        <section id="daftar-register-permohonan-informasi" class="ppid-request-receipt" aria-labelledby="daftar-register-heading">
            <div class="ppid-request-section-intro">
                <h2 id="daftar-register-heading" class="ppid-request-section-title">Daftar Register Permohonan Informasi</h2>
                <p class="ppid-request-section-subtext">
                    Dokumen ini digunakan sebagai instrumen administrasi dan dokumentasi pelayanan informasi publik untuk mencatat permohonan informasi yang masuk.
                </p>
            </div>

            <article class="ppid-request-receipt-card" aria-labelledby="daftar-register-doc-title">
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
                    <h3 id="daftar-register-doc-title" class="ppid-request-receipt-title">Daftar Register Permohonan Informasi Publik</h3>
                    <p class="ppid-request-receipt-note">
                        Dokumen administrasi pencatatan permohonan informasi publik PPID Poltekkes Kemenkes Banten.
                    </p>
                </div>

                <div class="ppid-request-receipt-actions">
                    <a
                        href="{{ $registerDocumentUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="ppid-request-form-button"
                        aria-label="Buka Daftar Register Permohonan Informasi Publik"
                    >
                        Buka Dokumen
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M14 5h5v5" />
                            <path d="M10 14 19 5" />
                            <path d="M19 13v6a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6" />
                        </svg>
                    </a>
                    <a
                        href="{{ $registerDocumentUrl }}"
                        class="ppid-request-secondary-action"
                        download="{{ $registerDownloadName }}"
                        aria-label="Unduh Daftar Register Permohonan Informasi Publik format DOCX"
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
    </div>
</div>
