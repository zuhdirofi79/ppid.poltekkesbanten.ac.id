<div
    class="fixed bottom-5 left-4 z-40"
    x-data="accessibilityPanel"
    x-show="!hidden"
    x-cloak
>
    <button
        type="button"
        class="flex h-12 w-12 items-center justify-center rounded-full bg-navy text-white shadow-lg ring-2 ring-white/40 hover:bg-navy-dark focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold"
        @click="open = !open"
        :aria-expanded="open.toString()"
        aria-controls="a11y-panel"
        aria-label="Pengaturan aksesibilitas"
    >
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4Zm-1 5h2l1.2 4H18a1 1 0 1 1 0 2h-3.2l-.5 1.6 2.6 3.2a1 1 0 1 1-1.6 1.2L12 16.2l-3.3 3.8a1 1 0 1 1-1.6-1.2l2.6-3.2L9.2 13H6a1 1 0 1 1 0-2h3.8L11 7Z"/>
        </svg>
    </button>

    <div
        id="a11y-panel"
        x-show="open"
        x-transition
        class="absolute bottom-14 left-0 w-72 max-w-[calc(100vw-2rem)] rounded-xl border border-line bg-white p-4 text-ink shadow-xl"
        role="dialog"
        aria-modal="true"
        aria-label="Panel aksesibilitas"
        @keydown.escape.window="open = false"
    >
        <div class="mb-3 flex items-center justify-between gap-2">
            <p class="font-heading text-sm font-bold text-navy">Aksesibilitas</p>
            <div class="flex items-center gap-2">
                <button type="button" class="text-xs text-muted hover:text-navy" @click="dismiss()" title="Sembunyikan tombol hingga sesi berikutnya">
                    Sembunyikan
                </button>
                <button type="button" class="text-muted hover:text-navy" @click="open = false" aria-label="Tutup panel">✕</button>
            </div>
        </div>

        <div class="space-y-3 text-sm">
            <div>
                <p class="mb-2 font-medium" id="a11y-font-label">Ukuran teks</p>
                <div class="flex gap-2" role="group" aria-labelledby="a11y-font-label">
                    <button type="button" class="min-h-10 flex-1 rounded border border-line px-2 py-2 hover:border-green" @click="setFont('90%')">A−</button>
                    <button type="button" class="min-h-10 flex-1 rounded border border-line px-2 py-2 hover:border-green" @click="setFont('100%')">A</button>
                    <button type="button" class="min-h-10 flex-1 rounded border border-line px-2 py-2 hover:border-green" @click="setFont('115%')">A+</button>
                </div>
            </div>
            <button type="button" class="min-h-10 w-full rounded border border-line px-3 py-2 text-left hover:border-green" @click="toggleContrast()">
                Kontras tinggi
            </button>
            <button type="button" class="min-h-10 w-full rounded border border-line px-3 py-2 text-left hover:border-green" @click="toggleGrayscale()">
                Skala abu-abu
            </button>
            <button type="button" class="min-h-10 w-full rounded bg-surface px-3 py-2 text-left font-medium text-navy hover:bg-line" @click="reset()">
                Reset pengaturan
            </button>
            <p class="text-xs text-muted">Preferensi disimpan di perangkat Anda (localStorage). Zoom browser tetap berfungsi.</p>
        </div>
    </div>
</div>
