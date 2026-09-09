import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('siteHeader', () => ({
        mobileOpen: false,
        openDesktop: null,
        openMobile: null,
        scrolled: false,
        bellOpen: false,
        profileOpen: false,

        init() {
            this.onScroll();
            window.addEventListener('scroll', () => this.onScroll(), { passive: true });
            window.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    this.closeAdminMenus();
                }
            });
        },

        toggleBell() {
            this.bellOpen = !this.bellOpen;
            this.profileOpen = false;
            this.openDesktop = null;
        },

        toggleProfile() {
            this.profileOpen = !this.profileOpen;
            this.bellOpen = false;
            this.openDesktop = null;
        },

        closeAdminMenus() {
            this.bellOpen = false;
            this.profileOpen = false;
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

    Alpine.data('globalSearch', (config = {}) => ({
        open: false,
        q: '',
        loading: false,
        results: [],
        timer: null,
        suggestionsUrl: config.suggestionsUrl || '/pencarian/saran',
        searchPageUrl: config.searchPageUrl || '/cari',

        get groupedResults() {
            const groups = [];
            const index = {};
            this.results.forEach((item) => {
                const label = item.group || 'Lainnya';
                if (index[label] === undefined) {
                    index[label] = groups.length;
                    groups.push({ label, items: [] });
                }
                groups[index[label]].items.push(item);
            });
            return groups;
        },

        openPanel() {
            this.open = true;
            document.body.classList.add('ppid-search-lock');
            this.$nextTick(() => this.$refs.searchInput?.focus());
        },

        closePanel() {
            this.open = false;
            document.body.classList.remove('ppid-search-lock');
            this.$nextTick(() => this.$refs.searchTrigger?.focus());
        },

        onInput() {
            clearTimeout(this.timer);
            const value = this.q.trim();
            if (value.length < 2) {
                this.results = [];
                this.loading = false;
                return;
            }
            this.loading = true;
            this.timer = setTimeout(() => this.fetchSuggestions(), 300);
        },

        async fetchSuggestions() {
            const value = this.q.trim();
            if (value.length < 2) {
                this.loading = false;
                return;
            }
            try {
                const url = `${this.suggestionsUrl}?q=${encodeURIComponent(value)}`;
                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                if (!response.ok) {
                    this.results = [];
                    return;
                }
                const data = await response.json();
                this.results = Array.isArray(data.results) ? data.results : [];
            } catch {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        goToResults(event) {
            const value = this.q.trim();
            if (value.length < 2) {
                event.preventDefault();
                return;
            }
            event.preventDefault();
            window.location.assign(`${this.searchPageUrl}?q=${encodeURIComponent(value)}`);
        },
    }));
});

Alpine.start();
