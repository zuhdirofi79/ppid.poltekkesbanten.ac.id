@php
    $maklumatImage = asset('docs/'.rawurlencode('MAKLUMAT PELAYANAN.png'));
@endphp

<div
    class="ppid-maklumat-popup"
    x-data="{
        open: false,
        imageFailed: false,
        lastFocus: null,
        scrollY: 0,
        storageKey: 'ppid_maklumat_seen_v1',
        init() {
            try {
                if (window.sessionStorage.getItem(this.storageKey) === '1') {
                    return;
                }
            } catch (error) {}
            this.lastFocus = document.activeElement;
            this.open = true;
            this.lockScroll();
            this.$nextTick(() => this.$refs.closeButton?.focus());
        },
        close() {
            if (! this.open) {
                return;
            }
            this.open = false;
            try {
                window.sessionStorage.setItem(this.storageKey, '1');
            } catch (error) {}
            this.unlockScroll();
            this.$nextTick(() => {
                if (this.lastFocus && typeof this.lastFocus.focus === 'function') {
                    this.lastFocus.focus();
                }
            });
        },
        lockScroll() {
            this.scrollY = window.scrollY || 0;
            document.body.style.position = 'fixed';
            document.body.style.top = '-' + this.scrollY + 'px';
            document.body.style.left = '0';
            document.body.style.right = '0';
            document.body.style.width = '100%';
        },
        unlockScroll() {
            const y = this.scrollY || 0;
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.left = '';
            document.body.style.right = '';
            document.body.style.width = '';
            window.scrollTo(0, y);
        },
        trap(event) {
            if (! this.open || event.key !== 'Tab') {
                return;
            }
            const nodes = [...this.$refs.panel.querySelectorAll('a, button')].filter((node) => ! node.disabled);
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
    }"
    x-cloak
    x-show="open"
    x-transition:enter="ppid-maklumat-popup-enter"
    x-transition:enter-start="ppid-maklumat-popup-from"
    x-transition:enter-end="ppid-maklumat-popup-to"
    x-transition:leave="ppid-maklumat-popup-leave"
    x-transition:leave-start="ppid-maklumat-popup-to"
    x-transition:leave-end="ppid-maklumat-popup-from"
    x-on:keydown.escape.window="close()"
    x-on:keydown="trap($event)"
>
    <div class="ppid-maklumat-popup-backdrop" x-on:click="close()"></div>
    <div
        class="ppid-maklumat-popup-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ppid-maklumat-popup-title"
        x-ref="panel"
    >
        <div class="ppid-maklumat-popup-header">
            <div>
                <p class="ppid-maklumat-popup-kicker">PPID Poltekkes Kemenkes Banten</p>
                <h2 id="ppid-maklumat-popup-title">Maklumat Pelayanan</h2>
            </div>
            <button type="button" class="ppid-maklumat-popup-close" x-ref="closeButton" x-on:click="close()" aria-label="Tutup Maklumat Pelayanan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M6 6l12 12M18 6 6 18" />
                </svg>
            </button>
        </div>

        <div class="ppid-maklumat-popup-body">
            <img
                class="ppid-maklumat-popup-image"
                src="{{ $maklumatImage }}"
                alt="Maklumat Pelayanan PPID Poltekkes Kemenkes Banten"
                width="1200"
                height="1600"
                x-on:error="imageFailed = true"
            >
            <p class="ppid-maklumat-popup-fallback" x-cloak x-show="imageFailed">
                Gambar tidak dapat dimuat. Anda tetap dapat membuka berkas resmi melalui tautan di bawah.
            </p>
        </div>

        <div class="ppid-maklumat-popup-actions">
            <a href="{{ $maklumatImage }}" target="_blank" rel="noopener noreferrer">Lihat Maklumat Ukuran Penuh</a>
            <button type="button" x-on:click="close()">Saya Mengerti</button>
        </div>
    </div>
</div>

<style>
    .ppid-maklumat-popup {
        position: fixed;
        inset: 0;
        z-index: 90;
        display: grid;
        place-items: center;
        padding: 1rem;
        padding-bottom: max(1rem, env(safe-area-inset-bottom));
    }

    .ppid-maklumat-popup-backdrop {
        position: absolute;
        inset: 0;
        background: rgb(16 17 59 / 0.64);
        backdrop-filter: blur(4px);
    }

    .ppid-maklumat-popup-dialog {
        position: relative;
        display: flex;
        width: min(68rem, 92vw);
        max-height: 90vh;
        max-height: 90dvh;
        flex-direction: column;
        overflow: hidden;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 24px 60px rgb(15 23 42 / 0.28);
        animation: ppid-maklumat-popup-in 240ms ease-out;
    }

    @keyframes ppid-maklumat-popup-in {
        from {
            opacity: 0;
            transform: translateY(12px) scale(0.98);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .ppid-maklumat-popup-header,
    .ppid-maklumat-popup-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.9rem 1rem;
        background: #fff;
    }

    .ppid-maklumat-popup-header {
        border-bottom: 1px solid var(--border);
    }

    .ppid-maklumat-popup-kicker {
        margin: 0;
        color: var(--green);
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .ppid-maklumat-popup-header h2 {
        margin: 0.15rem 0 0;
        color: var(--navy);
        font-family: var(--font-heading);
        font-size: 1.25rem;
        font-weight: 700;
        line-height: 1.3;
    }

    .ppid-maklumat-popup-close {
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

    .ppid-maklumat-popup-close svg {
        width: 1.1rem;
        height: 1.1rem;
    }

    .ppid-maklumat-popup-close:focus-visible,
    .ppid-maklumat-popup-actions a:focus-visible,
    .ppid-maklumat-popup-actions button:focus-visible {
        outline: 3px solid var(--gold);
        outline-offset: 2px;
    }

    .ppid-maklumat-popup-body {
        min-height: 0;
        overflow: auto;
        padding: 1rem;
        background: #f8fafc;
    }

    .ppid-maklumat-popup-image {
        display: block;
        width: 100%;
        height: auto;
        max-height: none;
        object-fit: contain;
        background: #fff;
    }

    .ppid-maklumat-popup-fallback {
        margin: 0.75rem 0 0;
        color: var(--navy);
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .ppid-maklumat-popup-actions {
        border-top: 1px solid var(--border);
    }

    .ppid-maklumat-popup-actions a,
    .ppid-maklumat-popup-actions button {
        display: inline-flex;
        min-height: 2.75rem;
        align-items: center;
        justify-content: center;
        padding: 0.65rem 1rem;
        border-radius: 0.6rem;
        font-family: var(--font-heading);
        font-size: 0.9375rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    .ppid-maklumat-popup-actions a {
        border: 1px solid var(--border);
        background: #fff;
        color: var(--navy);
    }

    .ppid-maklumat-popup-actions button {
        border: 0;
        background: var(--navy);
        color: #fff;
    }

    .ppid-maklumat-popup-enter {
        transition: opacity 240ms ease-out, transform 240ms ease-out;
    }

    .ppid-maklumat-popup-leave {
        transition: opacity 180ms ease-out, transform 180ms ease-out;
    }

    .ppid-maklumat-popup-from {
        opacity: 0;
        transform: translateY(12px) scale(0.98);
    }

    .ppid-maklumat-popup-to {
        opacity: 1;
        transform: none;
    }

    @media (max-width: 639px) {
        .ppid-maklumat-popup {
            padding: 0.75rem;
        }

        .ppid-maklumat-popup-dialog {
            width: min(100%, 94vw);
            border-radius: 14px;
        }

        .ppid-maklumat-popup-actions {
            flex-direction: column-reverse;
            align-items: stretch;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ppid-maklumat-popup-enter,
        .ppid-maklumat-popup-leave,
        .ppid-maklumat-popup-dialog {
            transition: none;
            animation: none;
        }
    }
</style>
