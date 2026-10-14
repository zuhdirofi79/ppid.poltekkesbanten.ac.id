@props(['categories' => collect()])

@php
    $icons = [
        'calendar' => '<path d="M8 3v2M16 3v2M4 9h16M6 5h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" />',
        'alert' => '<path d="M12 9v4m0 4h.01M10.3 4.3 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.3a2 2 0 0 0-3.4 0Z" />',
        'clock' => '<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />',
        'lock' => '<rect x="5" y="11" width="14" height="10" rx="2" /><path d="M8 11V8a4 4 0 0 1 8 0v3" />',
    ];
@endphp

<section class="relative z-10 bg-surface pb-12 pt-8 lg:-mt-14 lg:pt-0" aria-labelledby="kategori-heading">
    <div class="mx-auto max-w-7xl px-4">
        <h2 id="kategori-heading" class="font-heading mb-6 text-center text-2xl font-bold text-navy lg:sr-only">
            Kategori Informasi Publik
        </h2>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($categories as $card)
                <a
                    href="{{ route('information.category', ['slug' => $card['slug']]) }}"
                    class="group block rounded-xl border border-line bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-green hover:shadow-md lg:shadow-md"
                >
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-navy/5 text-navy group-hover:bg-green group-hover:text-white">
                        <svg class="h-6 w-6" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                            {!! $icons[$card['icon'] ?? 'calendar'] ?? $icons['calendar'] !!}
                        </svg>
                    </div>
                    <h3 class="font-heading text-lg font-bold text-navy group-hover:text-green">{{ $card['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-muted" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $card['excerpt'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>
