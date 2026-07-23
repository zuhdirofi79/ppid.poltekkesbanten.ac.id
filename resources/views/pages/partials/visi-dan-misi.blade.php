{{-- Template khusus: /halaman/visi-dan-misi — blok Visi + daftar Misi bernomor --}}
<!-- PPID-VISI-MISI-DEPLOY-20260723-A -->
<main class="ppid-vision-mission-page">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem] lg:text-[2.5rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                    {{ $excerpt }}
                </p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-[1100px] px-5 py-10 sm:px-6 sm:py-12 lg:px-8 lg:py-14">
        <article class="ppid-vision-mission-content mx-auto max-w-[960px]">
            <section class="ppid-vision-section" aria-labelledby="visi-heading">
                <h2 id="visi-heading" class="ppid-vision-heading">Visi</h2>
                <blockquote class="ppid-vision-box">
                    <p class="ppid-vision-text">
                        Terwujudnya pelayanan informasi yang transparan, akuntabel, dan prima demi terselenggaranya pendidikan tinggi yang menghasilkan tenaga kesehatan berbasis kearifan lokal dan diakui internasional sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.
                    </p>
                </blockquote>
            </section>

            <section class="ppid-mission-section" aria-labelledby="misi-heading">
                <h2 id="misi-heading" class="ppid-mission-heading">Misi</h2>
                <ol class="ppid-mission-list">
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">1</span>
                        <p class="ppid-mission-text">Menyediakan informasi publik yang akurat dan dapat dipertanggungjawabkan;</p>
                    </li>
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">2</span>
                        <p class="ppid-mission-text">Membangun dan mengembangkan sistem penyediaan dan layanan informasi berbasis digital;</p>
                    </li>
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">3</span>
                        <p class="ppid-mission-text">Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi;</p>
                    </li>
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">4</span>
                        <p class="ppid-mission-text">Memanfaatkan teknologi informasi dalam memberikan layanan informasi publik kepada masyarakat dan mendukung pengelolaan keterbukaan informasi publik;</p>
                    </li>
                    <li class="ppid-mission-item">
                        <span class="ppid-mission-number" aria-hidden="true">5</span>
                        <p class="ppid-mission-text">Meningkatkan pengelolaan informasi dan dokumentasi secara baik, efisien, mudah diakses, dan bersifat desentralisasi.</p>
                    </li>
                </ol>
            </section>
        </article>
    </div>
</main>
