import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteHeader', () => ({
        mobileOpen: false,
        openDesktop: null,
        openMobile: null,
        scrolled: false,

        init() {
            this.onScroll();
            window.addEventListener('scroll', () => this.onScroll(), { passive: true });
        },

        onScroll() {
            this.scrolled = window.scrollY > 8;
        },

        toggleDesktop(key) {
            this.openDesktop = this.openDesktop === key ? null : key;
        },

        closeDesktop() {
            this.openDesktop = null;
        },

        toggleMobile(key) {
            this.openMobile = this.openMobile === key ? null : key;
        },

        closeMobileMenu() {
            this.mobileOpen = false;
            this.openMobile = null;
        },
    }));

    Alpine.data('accessibilityPanel', () => ({
        open: false,
        hidden: false,

        init() {
            this.hidden = sessionStorage.getItem('a11y-hidden') === '1';

            const font = localStorage.getItem('a11y-font');
            const contrast = localStorage.getItem('a11y-contrast') === '1';
            const gray = localStorage.getItem('a11y-grayscale') === '1';

            if (font) {
                document.documentElement.style.setProperty('--font-size-base', font);
            }
            document.documentElement.classList.toggle('a11y-high-contrast', contrast);
            document.documentElement.classList.toggle('a11y-grayscale', gray);
        },

        dismiss() {
            this.open = false;
            this.hidden = true;
            sessionStorage.setItem('a11y-hidden', '1');
        },

        setFont(size) {
            document.documentElement.style.setProperty('--font-size-base', size);
            localStorage.setItem('a11y-font', size);
        },

        toggleContrast() {
            document.documentElement.classList.toggle('a11y-high-contrast');
            localStorage.setItem(
                'a11y-contrast',
                document.documentElement.classList.contains('a11y-high-contrast') ? '1' : '0',
            );
        },

        toggleGrayscale() {
            document.documentElement.classList.toggle('a11y-grayscale');
            localStorage.setItem(
                'a11y-grayscale',
                document.documentElement.classList.contains('a11y-grayscale') ? '1' : '0',
            );
        },

        reset() {
            document.documentElement.style.setProperty('--font-size-base', '100%');
            document.documentElement.classList.remove('a11y-high-contrast', 'a11y-grayscale');
            localStorage.removeItem('a11y-font');
            localStorage.removeItem('a11y-contrast');
            localStorage.removeItem('a11y-grayscale');
            sessionStorage.removeItem('a11y-hidden');
            this.hidden = false;
        },
    }));

    Alpine.data('backToTop', () => ({
        visible: false,

        init() {
            window.addEventListener(
                'scroll',
                () => {
                    this.visible = window.scrollY > 400;
                },
                { passive: true },
            );
        },

        go() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
    }));
});

Alpine.start();
