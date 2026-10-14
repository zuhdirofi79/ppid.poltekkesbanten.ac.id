@props(['statistics' => collect()])

<section class="bg-white pb-14 pt-6" aria-labelledby="statistik-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-green">Transparansi</p>
            <h2 id="statistik-heading" class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">Statistik PPID</h2>
            <p class="mt-3 text-sm text-muted">
                Ringkasan layanan informasi publik. Angka bertanda CONTOH akan diganti data aktual dari sistem.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($statistics as $stat)
                <div class="rounded-xl border border-line bg-surface/50 p-6 text-center">
                    <p class="font-heading text-3xl font-bold text-navy sm:text-4xl">
                        {{ number_format((int) $stat['value'], 0, ',', '.') }}{{ $stat['suffix'] ?? '' }}
                    </p>
                    <p class="mt-2 text-sm font-semibold text-ink">{{ $stat['label'] }}</p>
                    @if (! empty($stat['note']))
                        <p class="mt-1 text-xs text-muted">{{ $stat['note'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
