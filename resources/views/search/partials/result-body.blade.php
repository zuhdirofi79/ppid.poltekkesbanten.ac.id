<div class="ppid-search-row-main">
    <p class="ppid-search-row-title">{{ $item['title'] }}</p>
    @if (! empty($item['description']))
        <p class="ppid-search-row-desc">{{ $item['description'] }}</p>
    @endif
    <p class="ppid-search-row-meta">
        @if (! empty($item['metadata']))
            <span>{{ $item['metadata'] }}</span>
        @endif
        @if (! empty($item['badge']))
            <span class="ppid-search-badge {{ empty($item['available']) ? 'is-missing' : 'is-available' }}">
                {{ $item['badge'] }}
            </span>
        @endif
    </p>
</div>
@if (! empty($item['url']) && ! empty($item['available']))
    <span class="ppid-search-row-action">
        {{ $item['type'] === 'document' ? 'Lihat Dokumen' : 'Buka' }}
        <span aria-hidden="true">→</span>
    </span>
@endif
