{{-- Landing page: /layanan/alur-pengajuan-keberatan — infografis existing, tanpa gambar baru --}}
<!-- PPID-ALUR-PENGAJUAN-KEBERATAN-DEPLOY-20260923-A -->
<style>
    .ppid-alur-keberatan-chart {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    @media (max-width: 1023px) {
        .ppid-alur-keberatan-chart {
            width: 1100px;
            max-width: none;
        }
    }

    .ppid-alur-keberatan-actions a:focus-visible {
        outline: 2px solid #17184f;
        outline-offset: 2px;
    }
</style>
@php
    $imageRelativePath = 'images/ppid/alur-pengajuan-keberatan.png';
    $imageExists = is_file(public_path($imageRelativePath));
    $imageUrl = $imageExists
        ? asset($imageRelativePath)
        : 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-pengajuan-keberatan.png';
    $imageAlt = 'Infografis Alur Pengajuan Keberatan PPID Poltekkes Kemenkes Banten';
    $sopUrl = 'https://poltekkesbanten.ac.id/wp-content/uploads/2025/07/SOP-Penanganan-Sengketa-Informasi-Publik.pdf';
@endphp

<div class="ppid-alur-keberatan-page">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-6" aria-labelledby="alur-keberatan-heading">
            <h2 id="alur-keberatan-heading" class="font-heading text-lg font-bold text-navy sm:text-xl">
                Alur Pengajuan Keberatan
            </h2>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-ink sm:text-base">
                Pelajari alur dan tata cara pengajuan keberatan atas layanan informasi publik secara jelas, ringkas, dan mudah dipahami.
            </p>

            <div class="mt-5 overflow-x-auto rounded-xl border border-line bg-white shadow-sm">
                <a
                    href="{{ $imageUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Buka infografis Alur Pengajuan Keberatan dalam ukuran penuh"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $imageAlt }}"
                        width="1672"
                        height="941"
                        class="ppid-alur-keberatan-chart h-auto w-full object-contain"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            </div>
            <p class="mt-3 text-sm text-muted">
                Pada layar kecil, geser gambar ke samping agar empat tahapan tetap terbaca.
            </p>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <article class="rounded-xl border border-line bg-surface p-4" aria-labelledby="waktu-pengajuan-heading">
                    <h3 id="waktu-pengajuan-heading" class="font-heading text-base font-bold text-navy">
                        Kapan Waktu Pengajuan
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink">
                        Keberatan diajukan secara tertulis kepada Atasan PPID paling lambat 10 (sepuluh) hari kerja sejak permohonan informasi teregistrasi atau saat tanggapan/pemberitahuan tidak dipenuhi.
                    </p>
                </article>

                <article class="rounded-xl border border-line bg-surface p-4" aria-labelledby="syarat-dokumen-heading">
                    <h3 id="syarat-dokumen-heading" class="font-heading text-base font-bold text-navy">
                        Syarat Dokumen
                    </h3>
                    <ol class="mt-2 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-ink">
                        <li>Mengisi Formulir Pendaftaran atau Pengajuan Keberatan secara lengkap.</li>
                        <li>Melampirkan salinan/fotokopi identitas diri (KTP) pemohon.</li>
                        <li>Menyertakan alasan pengajuan keberatan secara jelas.</li>
                    </ol>
                </article>

                <article class="rounded-xl border border-line bg-surface p-4" aria-labelledby="proses-penanganan-heading">
                    <h3 id="proses-penanganan-heading" class="font-heading text-base font-bold text-navy">
                        Proses Penanganan
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink">
                        Atasan PPID akan memberikan tanggapan atau keputusan tertulis atas keberatan yang diajukan sesuai dengan Standar Operasional Prosedur (SOP) penanganan sengketa informasi publik yang berlaku.
                    </p>
                </article>
            </div>

            <section class="ppid-alur-keberatan-actions mt-8 rounded-xl border border-line bg-surface p-4 sm:p-5" aria-labelledby="ajukan-keberatan-heading">
                <h2 id="ajukan-keberatan-heading" class="font-heading text-lg font-bold text-navy">
                    Ajukan Keberatan
                </h2>
                <p class="mt-2 max-w-3xl text-sm leading-relaxed text-muted">
                    Gunakan formulir resmi untuk mengajukan keberatan, atau baca SOP penanganan sengketa informasi publik.
                </p>
                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a
                        href="{{ route('services.show', ['slug' => 'pengajuan-keberatan']) }}"
                        class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-navy-dark"
                    >
                        Isi Form Pengajuan Keberatan
                    </a>
                    <a
                        href="{{ $sopUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-center text-sm font-semibold text-navy hover:border-green hover:text-green"
                    >
                        Lihat SOP Penanganan Sengketa
                    </a>
                </div>
            </section>
        </section>
    </div>
</div>
