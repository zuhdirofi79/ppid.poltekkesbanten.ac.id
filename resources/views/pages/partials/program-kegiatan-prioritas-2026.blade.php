{{-- Template: /halaman/program-kegiatan-prioritas-2026 — konten dari CMS Page.body (editable admin) --}}
<!-- PPID-PROGRAM-KEGIATAN-PRIORITAS-2026-DEPLOY-20260923-A -->
<style>
    .ppid-priority-content > section + section {
        margin-top: 1.25rem;
    }

    .ppid-priority-content > section:first-child {
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        background: #f7f8fa;
        padding: 1.25rem 1.35rem;
    }

    .ppid-priority-content > section:not(:first-child) {
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 1px 2px rgba(23, 24, 79, 0.04);
    }

    .ppid-priority-content h2 {
        color: #17184f;
        font-size: 1.125rem;
        font-weight: 700;
        line-height: 1.4;
        margin: 0 0 0.85rem;
    }

    .ppid-priority-content p,
    .ppid-priority-content li {
        color: #25273a;
        font-size: 0.9375rem;
        line-height: 1.65;
    }

    .ppid-priority-content p + p {
        margin-top: 0.65rem;
    }

    .ppid-priority-content ul {
        margin: 0.75rem 0 0;
        padding-left: 1.25rem;
        list-style: disc;
    }

    .ppid-priority-content li + li {
        margin-top: 0.35rem;
    }

    .ppid-priority-content strong {
        color: #17184f;
    }
</style>

<main class="ppid-priority-page">
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

            <h1 class="font-heading mt-1 text-[1.75rem] font-bold leading-tight text-navy sm:text-[2.125rem]">
                {{ $title }}
            </h1>

            @if (! empty($excerpt))
                <p class="mt-3 max-w-3xl text-base leading-relaxed text-muted">{{ $excerpt }}</p>
            @endif
        </div>
    </section>

    <div class="mx-auto max-w-[1100px] px-4 py-10 sm:px-6 lg:px-8">
        <article class="ppid-priority-content cms-page">
            {!! $body !!}
        </article>
    </div>
</main>
