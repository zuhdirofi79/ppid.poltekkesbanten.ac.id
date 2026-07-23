{{-- Template khusus: /halaman/tugas-fungsi-wewenang — daftar Tugas + blok Fungsi (Wewenang ditunda sampai konten tersedia) --}}
<!-- PPID-TUGAS-FUNGSI-DEPLOY-20260723-A -->
@php
    $tugasItems = [
        'PPID Poltekkes Kemenkes Banten bertugas menerima, memeriksa, dan menindaklanjuti pengaduan masyarakat, permintaan layanan informasi, serta sengketa informasi publik.',
        'PPID Poltekkes Kemenkes Banten bertugas sebagai produsen dan pengendali pembuatan konten atau berita tentang penyelenggaraan dan pengembangan pendidikan di Poltekkes Kemenkes Banten, yang meliputi pendidikan Diploma Tiga, Sarjana Terapan, dan Pendidikan Profesi Ners.',
        'PPID Poltekkes Kemenkes Banten bertugas sebagai produsen dan pengendali pembuatan konten atau berita tentang pengembangan Tri Dharma Perguruan Tinggi melalui media sosial Poltekkes Kemenkes Banten.',
        'PPID Poltekkes Kemenkes Banten bertugas melaksanakan pembinaan, pengawasan, dan evaluasi terhadap pelaksanaan pengelolaan informasi publik.',
        'PPID Poltekkes Kemenkes Banten bertugas menyusun laporan semester dan tahunan pengelolaan informasi.',
        'PPID Poltekkes Kemenkes Banten bertugas melakukan penyusunan dan pemutakhiran Daftar Informasi Publik.',
    ];

    $fungsiText = 'PPID Poltekkes Kemenkes Banten berfungsi menjalankan Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik serta menetapkan standar operasional untuk mengelola layanan informasi publik.';

    // Siap diisi nanti tanpa mengubah desain utama — kosong = bagian tidak ditampilkan.
    $wewenangItems = [];
@endphp
<main class="ppid-duty-page">
    <section class="border-b border-line bg-surface">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-3 text-sm text-muted" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2">
                    @foreach ($breadcrumbs ?? [] as $index => $crumb)
                        @if ($index > 0)
                            <li aria-hidden="true">/</li>
                        @endif
                        <li @if ($loop->last) class="text-navy" aria-current="page" @endif>
                            @if (! empty($crumb['url']) && ! $loop->last)
                                <a href="{{ $crumb['url'] }}" class="hover:text-green">{{ $crumb['label'] }}</a>
                            @else
                                {{ $crumb['label'] }}
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>

            @if (! empty($label))
                <p class="text-sm font-semibold uppercase tracking-wider text-green">{{ $label }}</p>
            @endif

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem] lg:text-[2.5rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-[1100px] px-5 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
        <article class="ppid-duty-content mx-auto max-w-[960px]">
            <section class="ppid-duty-section" aria-labelledby="tugas-ppid">
                <h2 id="tugas-ppid" class="ppid-duty-heading">Tugas PPID</h2>
                <ol class="ppid-duty-list">
                    @foreach ($tugasItems as $index => $item)
                        <li class="ppid-duty-item">
                            <span class="ppid-duty-number" aria-hidden="true">{{ $index + 1 }}</span>
                            <p class="ppid-duty-text">{{ $item }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>

            <section class="ppid-function-section" aria-labelledby="fungsi-ppid">
                <h2 id="fungsi-ppid" class="ppid-duty-heading">Fungsi PPID</h2>
                <div class="ppid-function-box">
                    <p class="ppid-function-text">{{ $fungsiText }}</p>
                </div>
            </section>

            @if (count($wewenangItems) > 0)
                <section class="ppid-authority-section" aria-labelledby="wewenang-ppid">
                    <h2 id="wewenang-ppid" class="ppid-duty-heading">Wewenang PPID</h2>
                    <ol class="ppid-duty-list">
                        @foreach ($wewenangItems as $index => $item)
                            <li class="ppid-duty-item">
                                <span class="ppid-duty-number" aria-hidden="true">{{ $index + 1 }}</span>
                                <p class="ppid-duty-text">{{ $item }}</p>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endif
        </article>
    </div>
</main>
