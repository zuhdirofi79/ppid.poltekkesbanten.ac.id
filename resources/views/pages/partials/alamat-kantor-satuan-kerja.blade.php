{{-- Template khusus: /halaman/alamat-kantor-satuan-kerja --}}
<!-- PPID-ALAMAT-KANTOR-SATUAN-KERJA-DEPLOY-20260923-A -->
@php
    $campuses = [
        [
            'name' => 'Direktorat / Kampus Serang',
            'lines' => [
                'Jl. Syekh Moh. Nawawi Albantani No.12,',
                'Banjaragung, Kec. Cipocok Jaya,',
                'Kota Serang, Banten 42122',
            ],
            'note' => null,
        ],
        [
            'name' => 'Kampus Tangerang',
            'lines' => [
                'Jl. Dr. Sitanala (Komplek SPK), RT.002/RW.003,',
                'Karang Sari, Kec. Neglasari,',
                'Kota Tangerang, Banten 15121',
            ],
            'note' => null,
        ],
        [
            'name' => 'Kampus Rangkasbitung',
            'lines' => [
                'Jl. Raya Rangkasbitung Pandeglang No.421,',
                'Kaduagung Tengah, Kec. Cibadak,',
                'Kabupaten Lebak, Banten 42317',
            ],
            'note' => null,
        ],
    ];
@endphp

<main class="ppid-alamat-kantor-page">
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

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($campuses as $campus)
                <article class="rounded-xl border border-line bg-white p-5 shadow-sm">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-navy/5 text-navy" aria-hidden="true">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75">
                                <path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11Z" />
                                <circle cx="12" cy="10" r="2.5" />
                            </svg>
                        </span>
                        <div>
                            <h2 class="font-heading text-base font-bold text-navy">{{ $campus['name'] }}</h2>
                            <address class="mt-2 not-italic text-sm leading-relaxed text-ink">
                                @foreach ($campus['lines'] as $line)
                                    <span class="block">{{ $line }}</span>
                                @endforeach
                            </address>
                            @if (! empty($campus['note']))
                                <p class="mt-2 text-sm font-medium text-muted">({{ $campus['note'] }})</p>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</main>
