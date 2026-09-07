{{-- Global search overlay — trigger tetap tautan /cari agar tanpa JS tetap berfungsi --}}
<!-- PPID-GLOBAL-SEARCH-DEPLOY-20260907-A -->
<div
    class="contents"
    x-data="globalSearch({
        suggestionsUrl: '{{ route('search.suggestions') }}',
        searchPageUrl: '{{ route('search') }}',
    })"
>
    <a
        href="{{ route('search') }}"
        x-ref="searchTrigger"
        @click.prevent="openPanel()"
        class="inline-flex items-center gap-1 rounded px-1.5 py-0.5 hover:text-gold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold"
        aria-label="Cari informasi PPID"
        :aria-expanded="open.toString()"
        aria-controls="ppid-global-search"
        aria-haspopup="dialog"
    >
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.5-3.5"></path>
        </svg>
        <span class="hidden sm:inline">Cari</span>
    </a>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            x-transition.opacity.duration.150ms
            class="ppid-search-overlay"
            @keydown.escape.window="closePanel()"
        >
            <div class="ppid-search-backdrop" @click="closePanel()" aria-hidden="true"></div>
            <div
                id="ppid-global-search"
                class="ppid-search-panel"
                role="dialog"
                aria-modal="true"
                aria-labelledby="ppid-global-search-title"
                @click.stop
            >
                <h2 id="ppid-global-search-title" class="sr-only">Cari informasi PPID</h2>
                <form method="GET" action="{{ route('search') }}" class="ppid-search-panel-form" role="search" @submit="goToResults($event)">
                    <span class="ppid-search-panel-icon" aria-hidden="true">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.5-3.5"></path>
                        </svg>
                    </span>
                    <label class="sr-only" for="ppid-global-search-input">Kata kunci pencarian</label>
                    <input
                        id="ppid-global-search-input"
                        x-ref="searchInput"
                        type="search"
                        name="q"
                        x-model="q"
                        @input="onInput()"
                        maxlength="120"
                        autocomplete="off"
                        placeholder="Cari informasi, layanan, atau dokumen PPID..."
                        class="ppid-search-panel-input"
                    >
                    <button type="button" class="ppid-search-panel-close" @click="closePanel()" aria-label="Tutup pencarian">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </form>

                <div class="ppid-search-panel-body">
                    <p x-show="q.trim().length < 2" class="ppid-search-panel-hint">
                        Cari berdasarkan nama dokumen, layanan, tahun, atau kata kunci. Contoh: “LAKIN”, “RSB”, “Akreditasi”, “Keberatan”.
                    </p>
                    <p x-show="loading" class="ppid-search-panel-hint" x-cloak>Mencari...</p>
                    <p x-show="!loading && q.trim().length >= 2 && results.length === 0" class="ppid-search-panel-hint" x-cloak>
                        Tidak ada hasil untuk “<span x-text="q.trim()"></span>”.
                    </p>

                    <template x-for="(group, gIndex) in groupedResults" :key="group.label">
                        <section class="ppid-search-suggest-group">
                            <h3 class="ppid-search-suggest-label" x-text="group.label"></h3>
                            <ul>
                                <template x-for="(item, index) in group.items" :key="item.type + item.title + index">
                                    <li>
                                        <a
                                            x-show="item.available && item.url"
                                            :href="item.url"
                                            class="ppid-search-suggest-item"
                                            :target="item.external ? '_blank' : null"
                                            :rel="item.external ? 'noopener noreferrer' : null"
                                        >
                                            <span class="ppid-search-suggest-title" x-text="item.title"></span>
                                            <span class="ppid-search-suggest-meta" x-text="item.badge || item.metadata"></span>
                                        </a>
                                        <div
                                            x-show="!item.available || !item.url"
                                            class="ppid-search-suggest-item is-static"
                                        >
                                            <span class="ppid-search-suggest-title" x-text="item.title"></span>
                                            <span class="ppid-search-suggest-meta" x-text="item.badge || 'Dokumen belum tersedia'"></span>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </section>
                    </template>
                </div>

                <a
                    x-show="q.trim().length >= 2"
                    :href="searchPageUrl + '?q=' + encodeURIComponent(q.trim())"
                    class="ppid-search-panel-footer"
                    x-cloak
                >
                    Lihat semua hasil untuk “<span x-text="q.trim()"></span>” →
                </a>
            </div>
        </div>
    </template>
</div>
