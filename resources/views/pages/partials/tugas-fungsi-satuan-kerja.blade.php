{{-- Template khusus: /halaman/tugas-fungsi-satuan-kerja --}}
<!-- PPID-TUGAS-FUNGSI-SATUAN-KERJA-DEPLOY-20260923-A -->
@php
    $units = [
        [
            'id' => 'direktorat',
            'title' => 'Direktorat Poltekkes Kemenkes Banten',
            'open' => true,
            'tugas' => [
                'Menyelenggarakan pendidikan tinggi vokasi dan profesi bidang kesehatan.',
                'Menghasilkan SDM kesehatan yang kompeten.',
                'Mendukung program nasional bidang kesehatan.',
            ],
            'fungsi' => [
                'Pendidikan dan pengajaran.',
                'Penelitian dan pengabdian masyarakat.',
                'Penjaminan mutu.',
                'Pengelolaan administrasi dan sumber daya.',
            ],
        ],
        [
            'id' => 'keperawatan',
            'title' => 'Jurusan Keperawatan',
            'open' => false,
            'tugas' => [
                'Menyelenggarakan pendidikan tinggi keperawatan untuk menghasilkan SDM profesional berkarakter.',
                'Mengembangkan penelitian dan pengabdian masyarakat bidang keperawatan.',
                'Menyelenggarakan tata kelola jurusan yang akuntabel.',
            ],
            'fungsi' => [
                'Pendidikan dan pengajaran keperawatan.',
                'Penelitian dan pengabdian masyarakat.',
                'Pengelolaan sumber daya jurusan.',
            ],
        ],
        [
            'id' => 'kebidanan',
            'title' => 'Jurusan Kebidanan',
            'open' => false,
            'tugas' => [
                'Menyelenggarakan pendidikan vokasi dan profesi kebidanan yang bermutu.',
                'Mengembangkan penelitian terapan dan pengabdian masyarakat.',
                'Memperkuat kerja sama nasional dan internasional.',
            ],
            'fungsi' => [
                'Pendidikan dan pengajaran kebidanan.',
                'Penelitian dan pengabdian masyarakat.',
                'Penguatan tata kelola transparan dan akuntabel.',
            ],
        ],
        [
            'id' => 'tlm',
            'title' => 'Jurusan Teknologi Laboratorium Medis',
            'open' => false,
            'tugas' => [
                'Melaksanakan pendidikan vokasi teknologi laboratorium medis.',
                'Mengembangkan penelitian pemeriksaan laboratorium penyakit infeksi.',
                'Melaksanakan pengabdian kepada masyarakat.',
            ],
            'fungsi' => [
                'Pendidikan dan pengajaran teknologi laboratorium medis.',
                'Penelitian dan pengembangan layanan laboratorium.',
                'Pengabdian masyarakat dan pengelolaan sumber daya jurusan.',
            ],
        ],
    ];
@endphp

<main class="ppid-tugas-fungsi-satuan-kerja-page">
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

    <div class="mx-auto max-w-[1100px] space-y-4 px-4 py-10 sm:px-6 lg:px-8">
        @foreach ($units as $unit)
            <details
                class="group rounded-xl border border-line bg-white shadow-sm open:shadow-md"
                @if ($unit['open']) open @endif
            >
                <summary class="font-heading flex cursor-pointer list-none items-center justify-between gap-3 px-5 py-4 text-base font-bold text-navy marker:content-none sm:text-lg">
                    <span>{{ $unit['title'] }}</span>
                    <svg class="h-5 w-5 shrink-0 text-muted transition group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M5.25 7.5 10 12.25 14.75 7.5" />
                    </svg>
                </summary>
                <div class="border-t border-line px-5 py-5">
                    <h3 class="font-heading text-sm font-bold uppercase tracking-wide text-green">Tugas Pokok</h3>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-ink sm:text-base">
                        @foreach ($unit['tugas'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>

                    <h3 class="font-heading mt-6 text-sm font-bold uppercase tracking-wide text-green">Fungsi</h3>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm leading-relaxed text-ink sm:text-base">
                        @foreach ($unit['fungsi'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </details>
        @endforeach
    </div>
</main>
