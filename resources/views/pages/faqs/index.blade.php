@extends('layouts.app')
@section('title', 'FAQ — '.config('ppid.name'))
@section('meta_description', 'Pertanyaan yang sering diajukan terkait layanan PPID Poltekkes Kemenkes Banten.')

@section('content')
<div class="border-b border-line bg-surface">
    <div class="mx-auto max-w-3xl px-4 py-8">
        <h1 class="font-heading text-2xl font-bold text-navy">FAQ</h1>
    </div>
</div>
<div class="mx-auto max-w-3xl space-y-3 px-4 py-8" x-data="{ open: null }">
    @forelse ($faqs as $index => $faq)
        <div id="{{ $faq->slug }}" class="rounded-xl border border-line bg-white">
            <button
                type="button"
                class="font-heading flex w-full items-center justify-between gap-3 px-4 py-3 text-left font-semibold text-navy"
                @click="open = open === {{ $index }} ? null : {{ $index }}"
                :aria-expanded="(open === {{ $index }}).toString()"
            >
                <span>{{ $faq->question }}</span>
                <span aria-hidden="true">+</span>
            </button>
            <div x-cloak x-show="open === {{ $index }}" x-transition class="border-t border-line px-4 py-3 text-sm text-muted">
                {!! nl2br(e($faq->answer)) !!}
            </div>
        </div>
    @empty
        <p class="text-muted">Belum ada FAQ.</p>
    @endforelse
</div>
@endsection
