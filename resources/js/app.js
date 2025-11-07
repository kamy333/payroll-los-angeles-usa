import Alpine from 'alpinejs';

window.Alpine = Alpine;

const resolveTheme = (mode) => {
    if (mode === 'system') {
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    return mode;
};

const applyTheme = (mode) => {
    const resolved = resolveTheme(mode);
    document.documentElement.dataset.theme = resolved;
    document.documentElement.dataset.mode = mode;
    document.cookie = `theme=${mode}; path=/; max-age=31536000; samesite=Lax`;
};

const persistTheme = async (mode) => {
    try {
        await fetch('/theme', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ theme: mode }),
            credentials: 'same-origin',
        });
    } catch (error) {
        console.warn('Unable to persist theme preference', error);
    }
};

const initialThemePreference = () => {
    const stored = localStorage.getItem('theme-preference');
    if (stored) {
        return stored;
    }

    if (window.APP_META?.theme) {
        return window.APP_META.theme;
    }

    return 'system';
};

Alpine.store('theme', {
    mode: initialThemePreference(),
    set(mode) {
        this.mode = mode;
        localStorage.setItem('theme-preference', mode);
        applyTheme(mode);
        persistTheme(mode);
    },
});

Alpine.data('appShell', ({ meta, translations }) => ({
    translations,
    locale: meta.currentLocale,
    init() {
        applyTheme(Alpine.store('theme').mode);

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (Alpine.store('theme').mode === 'system') {
                applyTheme('system');
            }
        });

        window.addEventListener('storage', (event) => {
            if (event.key === 'theme-preference' && event.newValue) {
                Alpine.store('theme').set(event.newValue);
            }
        });
    },
}));

const lockBodyScroll = (shouldLock) => {
    document.body.classList.toggle('overflow-hidden', shouldLock);
};

Alpine.data('sidebar', () => ({
    open: false,
    isMobile: window.innerWidth < 1024,
    init() {
        this.syncToViewport();
        window.addEventListener('resize', () => {
            this.isMobile = window.innerWidth < 1024;
            this.syncToViewport();
        });

        this.$watch('open', (value) => {
            if (this.isMobile) {
                lockBodyScroll(value);
                if (value) {
                    this.$nextTick(() => {
                        const closeButton = this.$refs.closeButton;
                        closeButton?.focus();
                    });
                } else {
                    lockBodyScroll(false);
                    this.$refs.toggleButton?.focus();
                }
            }
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && this.open && this.isMobile) {
                this.close();
            }
        });

        document.addEventListener('focusin', (event) => {
            if (!this.open || !this.isMobile) {
                return;
            }

            const drawer = this.$refs.drawer;
            if (drawer && !drawer.contains(event.target)) {
                event.preventDefault();
                drawer.focus({ preventScroll: true });
            }
        });
    },
    syncToViewport() {
        if (this.isMobile) {
            this.open = false;
            lockBodyScroll(false);
        } else {
            this.open = true;
            lockBodyScroll(false);
        }
    },
    toggle() {
        if (this.isMobile) {
            this.open = !this.open;
        }
    },
    close() {
        if (this.isMobile) {
            this.open = false;
        }
    },
    openDrawer() {
        if (this.isMobile) {
            this.open = true;
        }
    },
    backdropClick() {
        if (this.isMobile) {
            this.close();
        }
    },
}));

Alpine.data('themeSelect', () => ({
    value: Alpine.store('theme').mode,
    init() {
        this.$watch(() => Alpine.store('theme').mode, (mode) => {
            this.value = mode;
        });
    },
    change() {
        Alpine.store('theme').set(this.value);
    },
}));

Alpine.start();
