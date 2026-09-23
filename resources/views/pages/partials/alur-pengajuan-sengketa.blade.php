{{-- Landing page: /layanan/alur-pengajuan-sengketa — infografis existing, tanpa gambar baru --}}
<!-- PPID-ALUR-PENGAJUAN-SENGKETA-DEPLOY-20260923-A -->
<style>
    .ppid-alur-sengketa-chart {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    @media (max-width: 1023px) {
        .ppid-alur-sengketa-chart {
            width: 1100px;
            max-width: none;
        }
    }

    .ppid-alur-sengketa-actions a:focus-visible {
        outline: 2px solid #17184f;
        outline-offset: 2px;
    }
</style>
@php
    $imageRelativePath = 'images/ppid/alur-pengajuan-permohonan-penyelesaian-sengketa-ke-komisi-informasi.png';
    $imageExists = is_file(public_path($imageRelativePath));
    $imageUrl = $imageExists
        ? asset($imageRelativePath)
        : 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-pengajuan-permohonan-penyelesaian-sengketa-ke-komisi-informasi.png';
    $imageAlt = 'Alur dan Tata Cara Pengajuan Sengketa Informasi Publik PPID Poltekkes Kemenkes Banten';
    $komisiUrl = 'https://komisiinformasi.go.id/read/14/03/2023/Mekanisme-Pendaftaran-Sengketa-Informasi-Publik';
@endphp

<div class="ppid-alur-sengketa-page">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-6" aria-labelledby="alur-sengketa-heading">
            <h2 id="alur-sengketa-heading" class="font-heading text-lg font-bold text-navy sm:text-xl">
                Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi
            </h2>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-ink sm:text-base">
                Pemohon informasi publik dapat mengajukan penyelesaian sengketa informasi apabila keberatan atas layanan informasi belum memperoleh penyelesaian sesuai ketentuan yang berlaku.
            </p>

            <div class="mt-5 overflow-x-auto rounded-xl border border-line bg-white shadow-sm">
                <a
                    href="{{ $imageUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Buka infografis Alur Pengajuan Sengketa Informasi dalam ukuran penuh"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $imageAlt }}"
                        width="1491"
                        height="1055"
                        class="ppid-alur-sengketa-chart h-auto w-full object-contain"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            </div>
            <p class="mt-3 text-sm text-muted">
                Pada layar kecil, geser gambar ke samping agar empat tahapan tetap terbaca.
            </p>

            <section class="mt-8 rounded-xl border border-line bg-surface p-4 sm:p-5" aria-labelledby="informasi-penting-heading">
                <h2 id="informasi-penting-heading" class="font-heading text-lg font-bold text-navy">
                    Informasi Penting
                </h2>
                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-ink">
                    <li>Pengajuan sengketa dilakukan setelah pemohon menempuh proses keberatan kepada Atasan PPID terlebih dahulu.</li>
                    <li>Permohonan penyelesaian sengketa informasi dapat diajukan kepada Komisi Informasi sesuai ketentuan yang berlaku.</li>
                    <li>Pemohon wajib menyiapkan dokumen pendukung seperti identitas, bukti permohonan informasi, bukti keberatan, dan tanggapan PPID.</li>
                </ol>
            </section>

            <div class="ppid-alur-sengketa-actions mt-6">
                <a
                    href="{{ $komisiUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-navy-dark"
                >
                    Mekanisme Pendaftaran Sengketa Informasi Publik
                </a>
            </div>
        </section>
    </div>
</div>
