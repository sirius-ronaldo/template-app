(() => {
    'use strict';

    const root = document.documentElement;
    const storageKey = 'template-ui-theme';
    const supportedThemes = ['dark', 'light'];
    let savedTheme = null;

    try {
        savedTheme = window.localStorage.getItem(storageKey);
    } catch (error) {
        savedTheme = null;
    }

    const defaultTheme = supportedThemes.includes(root.dataset.defaultTheme)
        ? root.dataset.defaultTheme
        : 'dark';
    root.dataset.uiTheme = supportedThemes.includes(savedTheme) ? savedTheme : defaultTheme;

    document.addEventListener('DOMContentLoaded', () => {
        const themeButtons = document.querySelectorAll('[data-theme-toggle]');
        const sidebar = document.querySelector('#app-sidebar');
        const openButton = document.querySelector('[data-sidebar-open]');
        const closeButtons = document.querySelectorAll('[data-sidebar-close]');

        const refreshThemeControls = () => {
            const isDark = root.dataset.uiTheme === 'dark';
            themeButtons.forEach((button) => {
                const label = button.querySelector('[data-theme-label]');
                const icon = button.querySelector('[data-theme-icon]');
                button.setAttribute('aria-label', isDark ? 'Ativar tema claro' : 'Ativar tema escuro');
                if (label) label.textContent = isDark ? 'Tema claro' : 'Tema escuro';
                if (icon) icon.textContent = isDark ? '☀' : '☾';
            });
        };

        themeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                root.dataset.uiTheme = root.dataset.uiTheme === 'dark' ? 'light' : 'dark';
                try {
                    window.localStorage.setItem(storageKey, root.dataset.uiTheme);
                } catch (error) {
                    // A interface continua funcional quando o armazenamento está indisponível.
                }
                refreshThemeControls();
            });
        });
        refreshThemeControls();

        const closeSidebar = (restoreFocus = true) => {
            document.body.classList.remove('ui-nav-open');
            openButton?.setAttribute('aria-expanded', 'false');
            if (restoreFocus) openButton?.focus();
        };

        openButton?.addEventListener('click', () => {
            document.body.classList.add('ui-nav-open');
            openButton.setAttribute('aria-expanded', 'true');
            sidebar?.querySelector('[data-sidebar-close]')?.focus();
        });
        closeButtons.forEach((button) => button.addEventListener('click', () => closeSidebar()));
        sidebar?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => closeSidebar(false));
        });

        document.querySelectorAll('[data-nav-toggle]').forEach((button) => {
            const targetId = button.getAttribute('aria-controls');
            const target = targetId ? document.getElementById(targetId) : null;
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                if (target) target.hidden = expanded;
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && document.body.classList.contains('ui-nav-open')) {
                closeSidebar();
            }
            if (event.key === 'Tab' && document.body.classList.contains('ui-nav-open') && sidebar) {
                const focusable = sidebar.querySelectorAll('a, button:not([disabled]), input:not([disabled])');
                const first = focusable[0];
                const last = focusable[focusable.length - 1];
                if (event.shiftKey && document.activeElement === first) {
                    event.preventDefault();
                    last?.focus();
                } else if (!event.shiftKey && document.activeElement === last) {
                    event.preventDefault();
                    first?.focus();
                }
            }
        });
    });
})();
