{{-- Template khusus: /halaman/visi-misi-satuan-kerja --}}
<!-- PPID-VISI-MISI-SATUAN-KERJA-DEPLOY-20260923-A -->
@php
    $units = [
        [
            'id' => 'direktorat',
            'title' => 'Direktorat Poltekkes Kemenkes Banten',
            'visi' => 'Menjadi Perguruan Tinggi Kesehatan yang Unggul, Profesional dan Berkarakter pada Tahun 2026.',
            'misi' => [
                'Menyelenggarakan dan mengembangkan pendidikan tinggi kesehatan yang bermutu, profesional, dan berkarakter.',
                'Melaksanakan penelitian bidang kesehatan yang inovatif dan berbasis bukti.',
                'Melaksanakan pengabdian kepada masyarakat secara berkelanjutan.',
                'Mengembangkan tata kelola institusi yang transparan dan akuntabel.',
                'Membangun jejaring kerja sama nasional dan internasional.',
            ],
        ],
        [
            'id' => 'keperawatan',
            'title' => 'Jurusan Keperawatan',
            'visi' => 'Menghasilkan tenaga keperawatan profesional yang berkarakter, terampil, terkini, dan terserap oleh masyarakat pada tahun 2026.',
            'misi' => [
                'Menyelenggarakan pendidikan tinggi untuk menghasilkan SDM profesional.',
                'Mengembangkan penelitian yang berkontribusi pada masyarakat.',
                'Melaksanakan pengabdian kepada masyarakat.',
                'Menyelenggarakan tata kelola institusi yang akuntabel.',
            ],
        ],
        [
            'id' => 'kebidanan',
            'title' => 'Jurusan Kebidanan',
            'visi' => 'Menjadi Perguruan Tinggi Kesehatan yang Unggul, Inovatif dan Berdaya Saing Global Tahun 2044.',
            'misi' => [
                'Pendidikan vokasi dan profesi bermutu.',
                'Pengembangan penelitian terapan.',
                'Pengabdian masyarakat.',
                'Penguatan tata kelola transparan dan akuntabel.',
                'Kerja sama nasional dan internasional.',
            ],
        ],
        [
            'id' => 'tlm',
            'title' => 'Jurusan Teknologi Laboratorium Medis',
            'visi' => 'Menjadi program studi yang unggul dalam bidang pemeriksaan penyakit infeksi pada tahun 2026.',
            'misi' => [
                'Melaksanakan pendidikan vokasi teknologi laboratorium medis.',
                'Mengembangkan penelitian pemeriksaan laboratorium penyakit infeksi.',
                'Melaksanakan pengabdian masyarakat.',
            ],
        ],
    ];
@endphp

<main class="ppid-visi-misi-satuan-kerja-page">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-3xl text-base leading-relaxed text-muted">{{ $excerpt }}</p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-[1100px] space-y-6 px-4 py-10 sm:px-6 lg:px-8">
        @foreach ($units as $index => $unit)
            <section class="rounded-xl border border-line bg-white p-5 shadow-sm sm:p-6" aria-labelledby="visi-misi-{{ $unit['id'] }}">
                <p class="text-sm font-semibold uppercase tracking-wider text-green">
                    {{ chr(65 + $index) }}.
                </p>
                <h2 id="visi-misi-{{ $unit['id'] }}" class="font-heading mt-1 text-xl font-bold text-navy">
                    {{ $unit['title'] }}
                </h2>

                <div class="mt-5">
                    <h3 class="font-heading text-base font-bold text-navy">Visi</h3>
                    <blockquote class="mt-2 rounded-xl border border-line bg-surface px-4 py-3 text-sm leading-relaxed text-ink sm:text-base">
                        “{{ $unit['visi'] }}”
                    </blockquote>
                </div>

                <div class="mt-5">
                    <h3 class="font-heading text-base font-bold text-navy">Misi</h3>
                    <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-ink sm:text-base">
                        @foreach ($unit['misi'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </div>
            </section>
        @endforeach
    </div>
</main>
