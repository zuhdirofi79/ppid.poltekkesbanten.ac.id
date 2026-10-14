@props(['services' => collect()])

@php
    $featured = $services->where('is_featured', true)->take(4);
@endphp

<section class="border-y border-line bg-navy py-8 text-white" aria-labelledby="shortcut-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 id="shortcut-heading" class="font-heading text-xl font-bold sm:text-2xl">Shortcut Formulir & Layanan</h2>
                <p class="mt-1 text-sm text-white/75">Akses cepat ke formulir yang paling sering digunakan.</p>
            </div>
            <a href="{{ route('services.show', ['slug' => 'alur-permohonan-informasi']) }}" class="text-sm font-semibold text-gold hover:underline">
                Lihat alur permohonan
            </a>
        </div>

        <ul class="mt-4 flex flex-wrap gap-2">
            @foreach ($featured as $service)
                @php
                    $isExternal = filled($service['external_url'] ?? null);
                    $href = $isExternal
                        ? $service['external_url']
                        : route('services.show', ['slug' => $service['slug']]);
                @endphp
                <li>
                    <a
                        href="{{ $href }}"
                        @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                        class="inline-flex items-center rounded-full border border-white/20 px-4 py-2 text-sm font-semibold transition hover:border-gold hover:bg-white/10 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold"
                        style="min-height:2.75rem"
                    >
                        {{ $service['title'] }}
                        @if ($isExternal)
                            <span class="sr-only">(membuka tab baru)</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
