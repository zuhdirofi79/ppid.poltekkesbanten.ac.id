@props(['units' => collect()])

<section class="bg-surface py-14" aria-labelledby="unit-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-green">Organisasi</p>
            <h2 id="unit-heading" class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">PPID Pelaksana</h2>
            <p class="mt-3 text-muted">
                Unit pelaksana informasi publik di lingkungan Poltekkes Kemenkes Banten.
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($units as $unit)
                @php
                    $href = filled($unit['url'] ?? null) ? $unit['url'] : '#unit-'.$unit['slug'];
                    $external = filled($unit['url'] ?? null) && str_starts_with($unit['url'], 'http');
                @endphp
                <a
                    id="unit-{{ $unit['slug'] }}"
                    href="{{ $href }}"
                    @if ($external) target="_blank" rel="noopener noreferrer" @endif
                    class="group rounded-xl border border-line bg-white p-5 transition hover:border-green hover:shadow-md"
                >
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-navy text-sm font-bold text-white">
                        {{ strtoupper(mb_substr($unit['name'], 0, 1)) }}
                    </div>
                    <h3 class="font-heading text-base font-bold text-navy group-hover:text-green">{{ $unit['name'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted">{{ $unit['description'] }}</p>
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-green">
                        {{ ! empty($unit['is_active']) ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>
