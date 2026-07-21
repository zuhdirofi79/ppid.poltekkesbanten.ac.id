<div x-data="backToTop" class="fixed bottom-5 right-4 z-40">
    <button
        type="button"
        x-cloak
        x-show="visible"
        x-transition
        @click="go()"
        class="flex h-12 w-12 items-center justify-center rounded-full bg-green text-white shadow-lg hover:bg-green-light"
        aria-label="Kembali ke atas"
    >
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="m6 14 6-6 6 6" />
        </svg>
    </button>
</div>
