{{-- Template khusus: /halaman/visi-dan-misi — chrome tetap, isi dari body CMS --}}
<!-- PPID-VISI-MISI-DEPLOY-20260723-A -->
@php
    $storedBody = trim((string) ($body ?? ''));
    $visionBody = \App\Support\VisiDanMisi::isLegacyBody($storedBody)
        ? \App\Support\VisiDanMisi::bodyHtml()
        : $storedBody;
@endphp
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
            {!! $visionBody !!}
        </article>
    </div>
</main>
