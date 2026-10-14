@php
    use App\Support\AkreditasiSertifikat;

    $akreditasiGroups = AkreditasiSertifikat::groups();
@endphp

<section class="ppid-berkala-section" aria-labelledby="{{ $headingId }}" x-data="{
    url: null,
    label: '',
    filename: '',
    lastFocus: null,
    scrollY: 0,
    openPreview(trigger) {
        const nextUrl = trigger.getAttribute('href');
        const allowed = [...this.$root.querySelectorAll('a.ppid-berkala-link')].map((node) => node.getAttribute('href'));
        if (! allowed.includes(nextUrl)) {
            return;
        }
        this.lastFocus = trigger;
        this.url = nextUrl;
        this.label = trigger.getAttribute('data-label') || '';
        this.filename = trigger.getAttribute('data-filename') || '';
        this.scrollY = window.scrollY || 0;
        document.body.style.position = 'fixed';
        document.body.style.top = '-' + this.scrollY + 'px';
        document.body.style.left = '0';
        document.body.style.right = '0';
        document.body.style.width = '100%';
        this.$nextTick(() => this.$refs.closeButton?.focus());
    },
    closePreview() {
        if (! this.url) {
            return;
        }
        this.url = null;
        this.label = '';
        this.filename = '';
        const y = this.scrollY || 0;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.left = '';
        document.body.style.right = '';
        document.body.style.width = '';
        window.scrollTo(0, y);
        this.$nextTick(() => this.lastFocus?.focus());
    },
    trap(event) {
        if (! this.url || event.key !== 'Tab') {
            return;
        }
        const nodes = [...this.$refs.dialog.querySelectorAll('a, button')].filter((node) => ! node.disabled);
        if (nodes.length === 0) {
            return;
        }
        const first = nodes[0];
        const last = nodes[nodes.length - 1];
        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (! event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    },
}">
    <h2 id="{{ $headingId }}" class="ppid-berkala-section-title">{{ AkreditasiSertifikat::SECTION }}</h2>

    @foreach ($akreditasiGroups as $group)
        <div class="ppid-akreditasi-group">
            <h3 class="ppid-akreditasi-group-title">{{ $group['title'] }}</h3>
            <ul class="ppid-berkala-list">
                @foreach ($group['documents'] as $document)
                    <li class="ppid-berkala-item is-available">
                        <div class="ppid-berkala-item-main">
                            <span class="ppid-berkala-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5">
                                    <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"/>
                                    <path d="M14 3v5h5"/>
                                </svg>
                            </span>
                            <div class="ppid-berkala-item-body">
                                <p class="ppid-berkala-item-title">{{ $document['label'] }}</p>
                                <p class="ppid-berkala-item-meta">
                                    <span class="ppid-berkala-badge is-available">TERSEDIA</span>
                                    <span>PDF / Dokumen</span>
                                </p>
                            </div>
                        </div>
                        <div class="ppid-berkala-item-action">
                            <a
                                href="{{ $document['url'] }}"
                                class="ppid-berkala-link"
                                data-label="{{ $document['label'] }}"
                                data-filename="{{ $document['filename'] }}"
                                x-on:click.prevent="openPreview($event.currentTarget)"
                            >
                                Lihat Dokumen
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <div
        class="ppid-akreditasi-preview"
        x-cloak
        x-show="url"
        x-on:keydown.escape.window="closePreview()"
        x-on:keydown="trap($event)"
    >
        <div class="ppid-akreditasi-preview-backdrop" x-on:click="closePreview()"></div>
        <div
            class="ppid-akreditasi-preview-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="ppid-akreditasi-preview-title"
            x-ref="dialog"
            x-show="url"
            x-transition:enter="ppid-akreditasi-preview-enter"
            x-transition:enter-start="ppid-akreditasi-preview-from"
            x-transition:enter-end="ppid-akreditasi-preview-to"
            x-transition:leave="ppid-akreditasi-preview-leave"
            x-transition:leave-start="ppid-akreditasi-preview-to"
            x-transition:leave-end="ppid-akreditasi-preview-from"
        >
            <div class="ppid-akreditasi-preview-header">
                <h2 id="ppid-akreditasi-preview-title" x-text="label || 'Pratinjau dokumen'"></h2>
                <button type="button" class="ppid-akreditasi-preview-close" x-ref="closeButton" x-on:click="closePreview()" aria-label="Tutup pratinjau dokumen">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M6 6l12 12M18 6 6 18" />
                    </svg>
                </button>
            </div>
            <div class="ppid-akreditasi-preview-body">
                <template x-if="url">
                    <iframe
                        class="ppid-akreditasi-preview-frame"
                        :src="url + '#view=FitH&toolbar=1&navpanes=0'"
                        :title="'Pratinjau ' + label"
                    ></iframe>
                </template>
                <p class="ppid-akreditasi-preview-fallback">
                    Apabila PDF tidak tampil di jendela ini, buka berkas langsung melalui tautan di bawah.
                </p>
            </div>
            <div class="ppid-akreditasi-preview-actions">
                <a :href="url || '#'" target="_blank" rel="noopener noreferrer">Buka PDF di Tab Baru</a>
                <a :href="url || '#'" :download="filename || null">Unduh PDF</a>
                <button type="button" x-on:click="closePreview()">Tutup</button>
            </div>
        </div>
    </div>
</section>

<style>
    .ppid-akreditasi-group + .ppid-akreditasi-group {
        margin-top: 1.25rem;
    }

    .ppid-akreditasi-group-title {
        margin: 0.35rem 0 0.15rem;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .ppid-akreditasi-preview {
        position: fixed;
        inset: 0;
        z-index: 85;
        display: grid;
        place-items: center;
        padding: 1rem;
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
    }

    .ppid-akreditasi-preview-backdrop {
        position: absolute;
        inset: 0;
        background: rgb(16 17 59 / 0.62);
        backdrop-filter: blur(4px);
    }

    .ppid-akreditasi-preview-dialog {
        position: relative;
        display: flex;
        width: min(72rem, 92vw);
        max-height: 90vh;
        max-height: 90dvh;
        flex-direction: column;
        overflow: hidden;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 24px 60px rgb(15 23 42 / 0.28);
    }

    .ppid-akreditasi-preview-header,
    .ppid-akreditasi-preview-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1rem;
        background: #fff;
    }

    .ppid-akreditasi-preview-header {
        border-bottom: 1px solid var(--border);
    }

    .ppid-akreditasi-preview-header h2 {
        margin: 0;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .ppid-akreditasi-preview-close {
        display: inline-flex;
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: #fff;
        color: var(--navy);
        cursor: pointer;
    }

    .ppid-akreditasi-preview-close svg {
        width: 1.1rem;
        height: 1.1rem;
    }

    .ppid-akreditasi-preview-body {
        min-height: 0;
        overflow: auto;
        padding: 0.75rem;
        background: #f8fafc;
    }

    .ppid-akreditasi-preview-frame {
        display: block;
        width: 100%;
        height: min(68dvh, 720px);
        min-height: 280px;
        border: 0;
        background: #fff;
    }

    .ppid-akreditasi-preview-fallback {
        margin: 0.65rem 0 0;
        color: var(--navy);
        font-size: 0.8125rem;
        line-height: 1.5;
    }

    .ppid-akreditasi-preview-actions {
        flex-wrap: wrap;
        border-top: 1px solid var(--border);
    }

    .ppid-akreditasi-preview-actions a,
    .ppid-akreditasi-preview-actions button {
        display: inline-flex;
        min-height: 2.75rem;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 0.9rem;
        border-radius: 0.6rem;
        font-family: var(--font-heading);
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .ppid-akreditasi-preview-actions a {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--navy);
    }

    .ppid-akreditasi-preview-actions button {
        border: 0;
        background: var(--navy);
        color: #fff;
    }

    .ppid-akreditasi-preview-close:focus-visible,
    .ppid-akreditasi-preview-actions a:focus-visible,
    .ppid-akreditasi-preview-actions button:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    .ppid-akreditasi-preview-enter {
        transition: opacity 240ms ease-out, transform 240ms ease-out;
    }

    .ppid-akreditasi-preview-leave {
        transition: opacity 180ms ease-out, transform 180ms ease-out;
    }

    .ppid-akreditasi-preview-from {
        opacity: 0;
        transform: translateY(12px) scale(0.98);
    }

    .ppid-akreditasi-preview-to {
        opacity: 1;
        transform: none;
    }

    @media (max-width: 639px) {
        .ppid-akreditasi-preview {
            padding: 0.5rem;
        }

        .ppid-akreditasi-preview-dialog {
            width: min(100%, 96vw);
            max-height: 92vh;
            max-height: 92dvh;
            border-radius: 14px;
        }

        .ppid-akreditasi-preview-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .ppid-akreditasi-preview-frame {
            height: 52dvh;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ppid-akreditasi-preview-enter,
        .ppid-akreditasi-preview-leave {
            transition: none;
        }
    }
</style>
