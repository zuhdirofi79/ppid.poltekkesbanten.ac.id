@props(['announcements' => collect()])

<section class="bg-surface py-14" aria-labelledby="pengumuman-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-green">Pemberitahuan</p>
                <h2 id="pengumuman-heading" class="font-heading mt-1 text-2xl font-bold text-navy sm:text-3xl">Pengumuman Terbaru</h2>
            </div>
            <a href="{{ route('announcements.index') }}" class="text-sm font-semibold text-green hover:underline">Semua pengumuman</a>
        </div>

        @if ($announcements->isEmpty())
            <div class="rounded-xl border border-dashed border-line bg-white p-8 text-center text-sm text-muted">
                Belum ada pengumuman aktif.
            </div>
        @else
            <ul class="space-y-3">
                @foreach ($announcements->take(4) as $item)
                    <li>
                        <a
                            href="{{ route('announcements.show', $item->slug) }}"
                            class="flex flex-col gap-2 rounded-xl border border-line bg-white p-4 transition hover:border-green sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div class="min-w-0">
                                <div class="mb-1 flex flex-wrap items-center gap-2">
                                    @if (! empty($item->is_pinned))
                                        <span class="rounded bg-navy px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Pinned</span>
                                    @endif
                                    @if (str_starts_with((string) $item->title, 'CONTOH'))
                                        <span class="rounded bg-gold/20 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-navy">CONTOH</span>
                                    @endif
                                    <span class="rounded bg-green/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-green">Terbaru</span>
                                </div>
                                <p class="font-heading font-bold text-navy">{{ $item->title }}</p>
                                <p class="mt-1 text-sm text-muted">{{ $item->excerpt }}</p>
                            </div>
                            <time class="shrink-0 text-xs text-muted" datetime="{{ $item->starts_at?->toDateString() }}">
                                {{ $item->starts_at?->locale('id')->translatedFormat('d M Y') }}
                            </time>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
