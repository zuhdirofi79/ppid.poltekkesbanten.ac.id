@extends('layouts.app')
@section('title', 'Pengumuman — '.config('ppid.name'))
@section('meta_description', 'Pengumuman resmi PPID Poltekkes Kemenkes Banten.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-8">
        <h1 class="font-heading text-2xl font-bold text-navy">Pengumuman</h1>
    </div>
</div>
<div class="mx-auto max-w-3xl space-y-3 px-4 py-8">
    @forelse ($announcements as $item)
        <a href="{{ route('announcements.show', $item->slug) }}" class="block rounded-xl border border-line bg-white p-4 hover:border-green">
            <div class="mb-1 flex flex-wrap gap-2">
                @if ($item->is_pinned)
                    <span class="rounded bg-navy px-2 py-0.5 text-[10px] font-bold uppercase text-white">Pinned</span>
                @endif
            </div>
            <h2 class="font-heading font-bold text-navy">{{ $item->title }}</h2>
            <p class="mt-1 text-sm text-muted">{{ $item->excerpt }}</p>
        </a>
    @empty
        <p class="text-muted">Belum ada pengumuman aktif.</p>
    @endforelse
    <div class="pt-4">{{ $announcements->links() }}</div>
</div>
@endsection
