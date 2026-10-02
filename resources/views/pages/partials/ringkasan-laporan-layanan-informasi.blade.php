{{-- Template: /halaman/ringkasan-laporan-layanan-informasi --}}
<!-- PPID-RINGKASAN-LAPORAN-LAYANAN-INFORMASI-DEPLOY-20261002-A -->
@php
    $report = $report ?? [];
    $summary = $report['summary'] ?? null;
    $selectedYear = $report['selectedYear'] ?? null;
    $availableYears = $report['availableYears'] ?? [];
    $unavailable = (bool) ($report['unavailable'] ?? true);
    $charts = $report['charts'] ?? [];
    $pdfUrl = route('pages.service-summary.pdf', array_filter([
        'tahun' => $selectedYear,
    ]));
@endphp

<main class="ppid-laporan-page">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-3xl lg:text-[2.5rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <form method="get" action="{{ route('pages.show', ['slug' => \App\Support\ServiceSummaryReport::SLUG]) }}" class="min-w-0">
                <label for="tahun-laporan" class="mb-1 block text-sm font-semibold text-navy">Pilih Tahun Laporan</label>
                <select id="tahun-laporan" name="tahun" class="ppid-laporan-select" onchange="this.form.submit()">
                    @if ($selectedYear && ! in_array((int) $selectedYear, array_map('intval', $availableYears), true))
                        <option value="{{ $selectedYear }}" selected>{{ $selectedYear }}</option>
                    @endif
                    @forelse ($availableYears as $year)
                        <option value="{{ $year }}" @selected((int) $selectedYear === (int) $year)>{{ $year }}</option>
                    @empty
                        @if (! $selectedYear)
                            <option value="" selected>Belum ada tahun</option>
                        @endif
                    @endforelse
                </select>
            </form>

            @if ($summary)
                <a href="{{ $pdfUrl }}" class="ppid-laporan-button">Unduh Laporan PDF</a>
            @endif
        </div>

        @if ($unavailable || ! $summary)
            <p class="ppid-laporan-empty" role="status">{{ \App\Support\ServiceSummaryReport::UNAVAILABLE }}</p>
        @else
            <p class="mb-4 text-sm text-muted">Identitas pemohon tidak ditampilkan.</p>

            <section aria-label="Ringkasan angka layanan informasi tahun {{ $summary->year }}">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    @foreach ([
                        ['Permohonan Diterima', 'total_received', 'Tahun '.$summary->year],
                        ['Dikabulkan Seluruhnya', 'fulfilled_full', null],
                        ['Dikabulkan Sebagian', 'fulfilled_partial', null],
                        ['Ditolak', 'rejected', null],
                    ] as [$cardLabel, $attribute, $extra])
                        <article class="ppid-laporan-card">
                            <h2 class="ppid-laporan-card-label">{{ $cardLabel }}</h2>
                            <p class="ppid-laporan-card-value">{{ $summary->countText($attribute) }}</p>
                            @if ($caption = $summary->countCaption($attribute))
                                <p class="ppid-laporan-card-caption">{{ $caption }}</p>
                            @elseif ($extra && $summary->getAttribute($attribute) !== null)
                                <p class="ppid-laporan-card-caption">{{ $extra }}</p>
                            @endif
                        </article>
                    @endforeach

                    <article class="ppid-laporan-card">
                        <h2 class="ppid-laporan-card-label">Rata-rata Waktu Respons</h2>
                        <p class="ppid-laporan-card-value">{{ $summary->responseDaysText() }}</p>
                        @if ($dayCaption = $summary->responseDaysCaption())
                            <p class="ppid-laporan-card-caption">{{ $dayCaption }}</p>
                        @endif
                    </article>
                </div>
            </section>

            <section class="ppid-laporan-panel" aria-labelledby="status-pemenuhan-title">
                <h2 id="status-pemenuhan-title" class="ppid-laporan-panel-title">Status Pemenuhan Permohonan Informasi</h2>
                <ul class="ppid-laporan-fallback">
                    <li>Dikabulkan seluruhnya: {{ $summary->countText('fulfilled_full') }}</li>
                    <li>Dikabulkan sebagian: {{ $summary->countText('fulfilled_partial') }}</li>
                    <li>Ditolak: {{ $summary->countText('rejected') }}</li>
                </ul>
                @if (! empty($charts['status']['segments']))
                    <div class="ppid-laporan-chart">
                        <canvas id="chart-status" role="img" aria-label="{{ $charts['status']['aria'] }}"></canvas>
                    </div>
                @endif
            </section>

            <section class="ppid-laporan-panel" aria-labelledby="jumlah-permohonan-title">
                <h2 id="jumlah-permohonan-title" class="ppid-laporan-panel-title">Jumlah Permohonan Informasi Publik</h2>
                <p class="text-sm leading-relaxed text-ink">
                    Jumlah permohonan informasi publik yang diterima pada tahun {{ $summary->year }}:
                    <strong>{{ $summary->countText('total_received') }}</strong>
                </p>
                @if (($report['comparison'] ?? collect())->isNotEmpty())
                    <ul class="ppid-laporan-fallback">
                        @foreach ($report['comparison'] as $row)
                            <li>{{ $row->year }}: {{ $row->countText('total_received') }} permohonan diterima</li>
                        @endforeach
                    </ul>
                    <div class="ppid-laporan-chart">
                        <canvas id="chart-volume" role="img" aria-label="{{ $charts['volume']['aria'] }}"></canvas>
                    </div>
                @endif
            </section>

            <section class="ppid-laporan-panel" aria-labelledby="waktu-pemenuhan-title">
                <h2 id="waktu-pemenuhan-title" class="ppid-laporan-panel-title">Waktu Pemenuhan Permohonan Informasi</h2>
                <p class="ppid-laporan-metric">
                    Rata-rata penyelesaian
                    <span>{{ $summary->average_response_days === null ? 'Data belum tersedia' : $summary->responseDaysText().' Hari Kerja' }}</span>
                </p>
                <p class="mt-3 text-sm leading-relaxed text-ink">
                    <span class="font-semibold text-navy">Keterangan:</span>
                    {{ trim((string) $summary->response_time_description) !== '' ? $summary->response_time_description : 'Data belum tersedia' }}
                </p>
            </section>

            <section class="ppid-laporan-panel" aria-labelledby="alasan-penolakan-title">
                <h2 id="alasan-penolakan-title" class="ppid-laporan-panel-title">Alasan Penolakan Permohonan Informasi</h2>
                @if ($summary->rejectionReasonLines() === [] && $summary->rejectionBreakdown() === [])
                    <p class="text-sm text-muted">Data belum tersedia</p>
                @endif
                @if ($summary->rejectionReasonLines() !== [])
                    <ul class="ppid-laporan-reasons">
                        @foreach ($summary->rejectionReasonLines() as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                @endif
                @if ($summary->rejectionBreakdown() !== [])
                    <ul class="ppid-laporan-fallback">
                        @foreach ($summary->rejectionBreakdown() as $row)
                            <li>{{ $row['reason'] }}: {{ $row['count'] }}</li>
                        @endforeach
                    </ul>
                    <div class="ppid-laporan-chart ppid-laporan-chart-wide">
                        <canvas id="chart-reasons" role="img" aria-label="{{ $charts['reasons']['aria'] }}"></canvas>
                    </div>
                @endif
            </section>

            @if (trim((string) $summary->notes) !== '')
                <section class="ppid-laporan-panel">
                    <h2 class="ppid-laporan-panel-title">Catatan</h2>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-ink">{{ $summary->notes }}</p>
                </section>
            @endif

            <script id="service-summary-chart-data" type="application/json">
                {!! \Illuminate\Support\Js::from($charts) !!}
            </script>
            @push('scripts')
                @vite('resources/js/service-summary-charts.js')
            @endpush
        @endif
    </div>
</main>

<style>
    .ppid-laporan-page { background: #fff; }
    .ppid-laporan-select,
    .ppid-laporan-button {
        min-height: 2.75rem;
        border-radius: 0.5rem;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
    }
    .ppid-laporan-select {
        min-width: 9rem;
        border: 2px solid var(--navy);
        background: #fff;
        color: var(--navy);
        padding: 0.4rem 0.8rem;
    }
    .ppid-laporan-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 1.05rem;
        background: var(--navy);
        color: #fff;
        text-decoration: none;
    }
    .ppid-laporan-button:hover { background: var(--navy-dark, #10113b); }
    .ppid-laporan-empty,
    .ppid-laporan-card,
    .ppid-laporan-panel {
        border: 1px solid var(--border);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 1px 2px rgb(15 23 42 / 0.04);
    }
    .ppid-laporan-empty {
        padding: 1.25rem;
        text-align: center;
        color: var(--muted);
    }
    .ppid-laporan-card { min-width: 0; padding: 1rem 1.05rem 1.1rem; }
    .ppid-laporan-card-label,
    .ppid-laporan-panel-title {
        margin: 0;
        font-family: var(--font-heading);
        font-weight: 700;
        color: var(--navy);
    }
    .ppid-laporan-card-label { font-size: 0.82rem; line-height: 1.35; }
    .ppid-laporan-card-value {
        margin: 0.75rem 0 0;
        font-family: var(--font-heading);
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1.15;
        color: var(--navy);
        overflow-wrap: anywhere;
    }
    .ppid-laporan-card-caption { margin: 0.35rem 0 0; font-size: 0.8rem; color: var(--muted); }
    .ppid-laporan-panel { margin-top: 1.25rem; padding: 1.15rem 1.2rem 1.3rem; }
    .ppid-laporan-panel-title { font-size: 1.2rem; }
    .ppid-laporan-fallback,
    .ppid-laporan-reasons {
        margin: 0.85rem 0 0;
        padding-left: 1.15rem;
        color: var(--ink, #1f2937);
        font-size: 0.95rem;
        line-height: 1.6;
    }
    .ppid-laporan-chart { position: relative; height: 18rem; margin-top: 1rem; max-width: 100%; }
    .ppid-laporan-chart-wide { height: 16rem; }
    .ppid-laporan-metric { margin: 0.8rem 0 0; color: var(--muted); }
    .ppid-laporan-metric span {
        display: block;
        margin-top: 0.25rem;
        font-family: var(--font-heading);
        font-size: 1.7rem;
        font-weight: 700;
        color: var(--navy);
    }
    @media (max-width: 639px) {
        .ppid-laporan-button,
        .ppid-laporan-select { width: 100%; }
    }
</style>
