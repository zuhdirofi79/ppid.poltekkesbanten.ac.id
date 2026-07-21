@props(['posts' => collect()])

<section class="bg-white py-14" aria-labelledby="berita-heading">
    <div class="mx-auto max-w-7xl px-4">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-green">Informasi terkini</p>
                <h2 id="berita-heading" class="font-heading mt-1 text-2xl font-bold text-navy sm:text-3xl">Berita Terbaru</h2>
            </div>
            <a href="{{ route('posts.index') }}" class="text-sm font-semibold text-green hover:underline">Semua berita</a>
        </div>

        @if ($posts->isEmpty())
            <div class="rounded-xl border border-dashed border-line bg-surface p-8 text-center text-sm text-muted">
                Belum ada berita dipublikasikan. Admin dapat menambahkannya melalui panel admin.
            </div>
        @else
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($posts->take(3) as $post)
                    <article class="flex flex-col border-b border-line pb-5 md:border-0 md:pb-0">
                        <div class="mb-3 flex flex-wrap items-center gap-2 text-xs text-muted">
                            <time datetime="{{ $post->published_at?->toDateString() }}">
                                {{ $post->published_at?->locale('id')->translatedFormat('d F Y') }}
                            </time>
                            @if (str_starts_with((string) $post->title, 'CONTOH'))
                                <span class="rounded bg-gold/20 px-2 py-0.5 font-semibold text-navy">CONTOH</span>
                            @endif
                        </div>
                        <h3 class="font-heading text-lg font-bold text-navy">
                            <a href="{{ route('posts.show', $post->slug) }}" class="hover:text-green">
                                {{ $post->title }}
                            </a>
                        </h3>
                        <p class="mt-2 flex-1 text-sm leading-relaxed text-muted">{{ $post->excerpt }}</p>
                        <a href="{{ route('posts.show', $post->slug) }}" class="mt-4 text-sm font-semibold text-green hover:underline">
                            Baca selengkapnya
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
