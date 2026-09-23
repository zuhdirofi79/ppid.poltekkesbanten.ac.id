{{-- Landing page: /layanan/alur-permohonan-informasi — infografis alur, tanpa placeholder kembali ke beranda --}}
<!-- PPID-ALUR-PERMOHONAN-INFORMASI-DEPLOY-20260923-A -->
<style>
    .ppid-alur-chart {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        object-fit: contain;
    }

    @media (max-width: 1023px) {
        .ppid-alur-chart {
            width: 1100px;
            max-width: none;
        }
    }

    .ppid-alur-actions a:focus-visible {
        outline: 2px solid #17184f;
        outline-offset: 2px;
    }
</style>
@php
    $imageRelativePath = 'images/ppid/alur-permohonan-informasi.png';
    $imageExists = is_file(public_path($imageRelativePath));
    $imageUrl = $imageExists
        ? asset($imageRelativePath)
        : 'https://srv1863-files.hstgr.io/c8b544f47f79c21d/files/public_html/images/ppid/alur-permohonan-informasi.png';
    $downloadName = 'alur-permohonan-informasi-ppid-poltekkes-kemenkes-banten.png';
    $imageAlt = 'Infografis Alur Permohonan Informasi PPID Poltekkes Kemenkes Banten';
@endphp

<div class="ppid-alur-page">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-6" aria-labelledby="alur-infografis-heading">
            <h2 id="alur-infografis-heading" class="font-heading text-lg font-bold text-navy sm:text-xl">
                Alur Permohonan Informasi
            </h2>
            <p class="mt-2 max-w-3xl text-sm leading-relaxed text-ink sm:text-base">
                Pelajari tahapan permohonan informasi publik dari pengajuan hingga jawaban. Infografis berikut menjelaskan alur permohonan informasi di PPID Poltekkes Kemenkes Banten secara singkat dan mudah dipahami.
            </p>

            <div class="mt-5 overflow-x-auto rounded-xl border border-line bg-white shadow-sm">
                <a
                    href="{{ $imageUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Buka infografis Alur Permohonan Informasi dalam ukuran penuh"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $imageAlt }}"
                        width="1672"
                        height="941"
                        class="ppid-alur-chart h-auto w-full object-contain"
                        loading="lazy"
                        decoding="async"
                    >
                </a>
            </div>
            <p class="mt-3 text-sm text-muted">
                Pada layar kecil, geser gambar ke samping agar empat tahapan tetap terbaca.
            </p>

            <div class="ppid-alur-actions mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a
                    href="{{ $imageUrl }}"
                    download="{{ $downloadName }}"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-center text-sm font-semibold text-navy hover:border-green hover:text-green"
                >
                    Unduh Gambar
                </a>
                <a
                    href="{{ $imageUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-navy px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-navy-dark"
                >
                    Buka Gambar Penuh
                </a>
                <a
                    href="{{ route('services.show', ['slug' => 'permohonan-informasi']) }}"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md bg-green px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-green-light"
                >
                    Permohonan Informasi
                </a>
                <a
                    href="{{ route('contact') }}"
                    class="font-heading inline-flex min-h-11 items-center justify-center rounded-md border border-line bg-white px-5 py-2.5 text-center text-sm font-semibold text-navy hover:border-green hover:text-green"
                >
                    Hubungi PPID
                </a>
            </div>
        </section>
    </div>
</div>
