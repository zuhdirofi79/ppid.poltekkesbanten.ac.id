<article class="ppid-leader-card overflow-hidden rounded-xl border border-line bg-white shadow-sm {{ $featured ? 'lg:grid lg:grid-cols-2' : '' }}">
    <div class="bg-surface">
        @if ($url = $profile->photoUrl())
            <img
                src="{{ $url }}"
                alt="{{ $profile->photoAlt() }}"
                class="ppid-leader-photo"
                width="1600"
                height="900"
            >
        @else
            <div class="flex min-h-48 items-center justify-center bg-navy text-3xl font-bold text-white" aria-hidden="true">
                {{ $profile->initials() }}
            </div>
        @endif
    </div>
    <div class="p-5 sm:p-6">
        <h2 class="font-heading text-xl font-bold leading-snug text-navy sm:text-2xl">{{ $profile->displayName() }}</h2>
        <p class="mt-2 text-sm font-semibold uppercase tracking-wide text-green">{{ $profile->position }}</p>
        @if ($profile->short_summary)
            <p class="ppid-leader-copy mt-4 text-sm text-muted sm:text-base">{{ $profile->short_summary }}</p>
        @endif

        <details class="ppid-leader-detail mt-5">
            <summary
                class="inline-flex rounded-md bg-navy px-4 py-2 text-sm font-semibold text-white"
                aria-controls="profil-lengkap-{{ $profile->id }}"
            >
                Lihat Profil Lengkap
            </summary>
            <div id="profil-lengkap-{{ $profile->id }}" class="mt-5 space-y-5">
                @if ($profile->detailParagraphs() !== [])
                    <section>
                        <h3 class="font-heading text-lg font-bold text-navy">Tentang</h3>
                        @foreach ($profile->detailParagraphs() as $paragraph)
                            <p class="ppid-leader-copy mt-2 text-sm text-muted sm:text-base">{{ $paragraph }}</p>
                        @endforeach
                    </section>
                @endif

                <section>
                    <h3 class="font-heading text-lg font-bold text-navy">Riwayat Pendidikan</h3>
                    @if ($profile->educationItems() === [])
                        <p class="mt-2 text-sm text-muted">Data pendidikan belum dilengkapi.</p>
                    @else
                        <ul class="ppid-leader-copy mt-2 list-disc space-y-1 pl-5 text-sm text-muted sm:text-base">
                            @foreach ($profile->educationItems() as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section>
                    <h3 class="font-heading text-lg font-bold text-navy">Riwayat Pekerjaan</h3>
                    @if ($profile->workItems() === [])
                        <p class="mt-2 text-sm text-muted">Data riwayat pekerjaan belum dilengkapi.</p>
                    @else
                        <ul class="ppid-leader-copy mt-2 list-disc space-y-1 pl-5 text-sm text-muted sm:text-base">
                            @foreach ($profile->workItems() as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </details>
    </div>
</article>
