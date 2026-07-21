@props(['services' => collect()])

<section class="bg-white py-14" aria-labelledby="layanan-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mx-auto mb-10 max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-green">Layanan</p>
            <h2 id="layanan-heading" class="font-heading mt-2 text-2xl font-bold text-navy sm:text-3xl">
                Layanan Informasi Publik
            </h2>
            <p class="mt-3 text-muted">
                Akses layanan PPID Poltekkes Kemenkes Banten untuk permohonan, keberatan, pengaduan, dan informasi terkait.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                @php
                    $isExternal = filled($service['external_url'] ?? null);
                    $href = $isExternal
                        ? $service['external_url']
                        : route('services.show', ['slug' => $service['slug']]);
                @endphp
                <a
                    href="{{ $href }}"
                    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                    class="group flex flex-col rounded-xl border border-line bg-surface/40 p-5 transition hover:border-green hover:bg-white hover:shadow-md"
                >
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <h3 class="font-heading text-base font-bold text-navy group-hover:text-green">
                            {{ $service['title'] }}
                        </h3>
                        @if ($isExternal)
                            <span class="shrink-0 rounded bg-navy/5 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-navy">
                                Eksternal
                            </span>
                        @endif
                    </div>
                    <p class="flex-1 text-sm leading-relaxed text-muted">{{ $service['excerpt'] }}</p>
                    <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-green">
                        Buka layanan
                        <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                        @if ($isExternal)
                            <span class="sr-only">(membuka tab baru)</span>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
