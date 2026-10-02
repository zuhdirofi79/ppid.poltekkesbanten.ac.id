@if ($publicAttachments->isNotEmpty())
    <section class="mx-auto max-w-3xl px-4 pb-12">
        <h2 class="font-heading text-xl font-bold text-navy">Data dukung</h2>
        <ul class="mt-3 space-y-2 text-sm">
            @foreach ($publicAttachments as $attachment)
                @if ($attachment->media?->publicUrl())
                    <li>
                        <a href="{{ $attachment->media->publicUrl() }}" class="text-navy underline">{{ $attachment->display_title ?: $attachment->media->original_name }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </section>
@endif
