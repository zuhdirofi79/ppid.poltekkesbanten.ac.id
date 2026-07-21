@props(['services' => collect()])

@php
    $featured = $services->where('is_featured', true)->take(4);
@endphp

<section class="border-y border-line bg-navy py-10 text-white" aria-labelledby="shortcut-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 id="shortcut-heading" class="font-heading text-xl font-bold sm:text-2xl">Shortcut Formulir & Layanan</h2>
                <p class="mt-1 text-sm text-white/75">Akses cepat layanan yang paling sering digunakan.</p>
            </div>
            <a href="{{ route('services.show', ['slug' => 'alur-permohonan-informasi']) }}" class="text-sm font-semibold text-gold hover:underline">
                Lihat alur permohonan
            </a>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($featured as $service)
                @php
                    $isExternal = filled($service['external_url'] ?? null);
                    $href = $isExternal
                        ? $service['external_url']
                        : route('services.show', ['slug' => $service['slug']]);
                @endphp
                <a
                    href="{{ $href }}"
                    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                    class="rounded-lg border border-white/15 bg-white/5 px-4 py-4 text-sm font-semibold transition hover:border-gold hover:bg-white/10"
                >
                    {{ $service['title'] }}
                    @if ($isExternal)
                        <span class="mt-1 block text-xs font-normal text-white/60">Tautan eksternal</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
