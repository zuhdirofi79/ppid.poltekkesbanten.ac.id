{{-- Data profil berasal dari tabel leadership_profiles. Blade hanya tampilan. --}}
<!-- PPID-PROFIL-PIMPINAN-DEPLOY-20261002-A -->
<style>
    .ppid-leader-photo {
        display: block;
        width: 100%;
        height: auto;
        max-width: 100%;
        object-fit: contain;
        background: #f8fafc;
    }

    .ppid-leader-card,
    .ppid-leader-page {
        overflow-x: clip;
    }

    .ppid-leader-grid {
        display: grid;
        gap: 1.5rem;
        grid-template-columns: minmax(0, 1fr);
    }

    @media (min-width: 768px) {
        .ppid-leader-grid {
            grid-template-columns: repeat(auto-fit, minmax(22rem, 1fr));
        }
    }

    .ppid-leader-detail summary {
        cursor: pointer;
        list-style: none;
    }

    .ppid-leader-detail summary::-webkit-details-marker {
        display: none;
    }

    .ppid-leader-copy {
        max-width: 65ch;
        line-height: 1.7;
    }

    @media (prefers-reduced-motion: reduce) {
        .ppid-leader-detail summary {
            transition: none;
        }
    }
</style>
@php
    $profiles = $profiles ?? collect();
    $featured = $profiles->firstWhere('is_featured', true) ?? $profiles->first();
    $others = $featured
        ? $profiles->reject(fn ($profile) => $profile->id === $featured->id)->values()
        : $profiles;
@endphp

<main class="ppid-leader-page">
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
            <p class="mt-3 max-w-[52rem] text-base leading-relaxed text-muted sm:text-[1.0625rem]">
                {{ $excerpt ?: 'Profil pimpinan Poltekkes Kemenkes Banten beserta riwayat pendidikan dan riwayat pekerjaan.' }}
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl space-y-8 px-4 py-10 sm:px-6 lg:px-8">
        @if ($profiles->isEmpty())
            <p class="rounded-xl border border-line bg-white p-6 text-muted">Data profil pimpinan sedang diperbarui oleh Admin PPID.</p>
        @else
            @if ($featured)
                @include('pages.partials.leadership-profile-card', ['profile' => $featured, 'featured' => true])
            @endif

            @if ($others->isNotEmpty())
                <div class="ppid-leader-grid">
                    @foreach ($others as $profile)
                        @include('pages.partials.leadership-profile-card', ['profile' => $profile, 'featured' => false])
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</main>
