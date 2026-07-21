@extends('layouts.app')

@section('title', ($q ? 'Hasil pencarian: '.$q : 'Pencarian').' — '.config('ppid.name'))
@section('meta_description', 'Cari berita, dokumen, pengumuman, dan halaman PPID Poltekkes Kemenkes Banten.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-3xl px-4 py-10">
        <h1 class="font-heading text-2xl font-bold text-navy">Pencarian</h1>
        <form method="GET" action="{{ route('search') }}" class="mt-4 flex gap-2" role="search">
            <label class="sr-only" for="q">Kata kunci</label>
            <input id="q" name="q" value="{{ $q }}" type="search" required placeholder="Cari informasi publik..." class="w-full rounded-md border border-line px-3 py-2.5 text-sm">
            <button class="rounded-md bg-navy px-4 py-2.5 text-sm font-semibold text-white">Cari</button>
        </form>
    </div>
</div>

<div class="mx-auto max-w-3xl space-y-8 px-4 py-10">
    @if ($q === '')
        <p class="text-sm text-muted">Masukkan kata kunci untuk mencari konten.</p>
    @else
        @php $total = $posts->count() + $documents->count() + $pages->count() + $announcements->count() + $faqs->count(); @endphp
        <p class="text-sm text-muted">Ditemukan {{ $total }} hasil untuk “{{ $q }}”.</p>

        @foreach ([
            ['label' => 'Berita', 'items' => $posts, 'route' => 'posts.show', 'field' => 'title'],
            ['label' => 'Dokumen', 'items' => $documents, 'route' => 'documents.show', 'field' => 'title'],
            ['label' => 'Halaman', 'items' => $pages, 'route' => 'pages.show', 'field' => 'title'],
            ['label' => 'Pengumuman', 'items' => $announcements, 'route' => 'announcements.show', 'field' => 'title'],
        ] as $group)
            @if ($group['items']->isNotEmpty())
                <section>
                    <h2 class="font-heading mb-3 text-lg font-bold text-navy">{{ $group['label'] }}</h2>
                    <ul class="space-y-2">
                        @foreach ($group['items'] as $item)
                            <li>
                                <a href="{{ route($group['route'], $item->slug) }}" class="font-medium text-green hover:underline">{{ $item->{$group['field']} }}</a>
                                @if (! empty($item->excerpt))
                                    <p class="text-sm text-muted">{{ \Illuminate\Support\Str::limit($item->excerpt, 140) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        @endforeach

        @if ($faqs->isNotEmpty())
            <section>
                <h2 class="font-heading mb-3 text-lg font-bold text-navy">FAQ</h2>
                <ul class="space-y-2">
                    @foreach ($faqs as $faq)
                        <li>
                            <a href="{{ route('faqs.index') }}#{{ $faq->slug }}" class="font-medium text-green hover:underline">{{ $faq->question }}</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if ($total === 0)
            <p class="rounded-xl border border-dashed border-line bg-surface p-6 text-sm text-muted">Tidak ada hasil. Coba kata kunci lain.</p>
        @endif
    @endif
</div>
@endsection
