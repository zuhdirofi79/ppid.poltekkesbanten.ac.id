@php
    $heroPath = public_path(config('ppid.hero'));
    $heroExists = is_file($heroPath);
@endphp

<section class="hero-banner flex items-center" aria-labelledby="hero-heading">
    @if ($heroExists)
        <img
            class="hero-banner__media"
            src="{{ asset(config('ppid.hero')) }}"
            alt="{{ config('ppid.hero_alt') }}"
            width="1920"
            height="887"
            fetchpriority="high"
        >
    @else
        <div class="hero-banner__fallback" role="img" aria-label="{{ config('ppid.hero_alt') }}"></div>
    @endif
    <div class="hero-banner__overlay" aria-hidden="true"></div>

    <div class="relative mx-auto w-full max-w-7xl px-4 py-16 sm:py-20">
        <div class="max-w-3xl text-white">
            <h1 id="hero-heading" class="font-heading text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                PPID POLTEKKES KEMENKES BANTEN
            </h1>
            <p class="mt-4 max-w-2xl text-base text-white/90 sm:text-lg">
                Layanan informasi publik yang cepat, tepat, transparan, akuntabel, dan mudah diakses.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ route('services.show', ['slug' => 'permohonan-informasi']) }}"
                    class="font-heading inline-flex items-center rounded-md bg-green px-5 py-3 text-sm font-semibold text-white hover:bg-green-light"
                >
                    Permohonan Informasi
                </a>
                <a
                    href="{{ route('information.index') }}"
                    class="font-heading inline-flex items-center rounded-md border border-white/70 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur hover:bg-white/20"
                >
                    Lihat Informasi Publik
                </a>
            </div>
        </div>
    </div>
</section>
