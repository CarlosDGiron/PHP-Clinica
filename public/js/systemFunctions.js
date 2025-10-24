(function () {
    "use strict";

    // Botón volver al principio (protegido por disponibilidad de jQuery)
    if (window.jQuery) {
        const $ = window.jQuery;
        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').fadeIn('slow');
            } else {
                $('.back-to-top').fadeOut('slow');
            }
        });
        $('.back-to-top').click(function () {
            $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
            return false;
        });
    }


    // Alternador de barra lateral (protegido por disponibilidad de jQuery)
    if (window.jQuery) {
        const $ = window.jQuery;
        $('.sidebar-toggler').click(function (e) {
            e.preventDefault();
            $('.sidebar, .content').toggleClass('open');
            $('.sidebar').addClass('toggling');
            setTimeout(() => $('.sidebar').removeClass('toggling'), 420);
            return false;
        });
    }

    // Alternador nativo (sin jQuery) con animación
    document.addEventListener('click', function (e) {
        const btn = e.target && e.target.closest ? e.target.closest('.sidebar-toggler') : null;
        if (!btn) return;
        e.preventDefault();
        const sidebar = document.querySelector('.sidebar');
        const content = document.querySelector('.content');
        if (sidebar) {
            sidebar.classList.toggle('open');
            sidebar.classList.add('toggling');
            setTimeout(() => sidebar.classList.remove('toggling'), 420);
        }
        if (content) content.classList.toggle('open');
    });

    // =======================
    // Tema claro / oscuro
    // =======================
    function getStoredTheme() {
        try {
            const t = localStorage.getItem('theme');
            return (t === 'light' || t === 'dark') ? t : null;
        } catch (_) { return null; }
    }

    function setStoredTheme(t) {
        try { localStorage.setItem('theme', t); } catch (_) {}
    }

    function getPreferredTheme() {
        const saved = getStoredTheme();
        if (saved) return saved;
        return 'dark'; // por defecto oscuro
    }

    function applyTheme(theme) {
        const root = document.documentElement;
        root.classList.remove('theme-dark', 'theme-light');
        root.classList.add(theme === 'dark' ? 'theme-dark' : 'theme-light');
        const icon = document.getElementById('themeToggleIcon');
        if (icon) {
            icon.classList.remove('bi-sun', 'bi-moon-stars');
            icon.classList.add(theme === 'dark' ? 'bi-sun' : 'bi-moon-stars');
            icon.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a claro' : 'Cambiar a oscuro');
        }
    }

    // Aplica de inmediato el tema preferido
    applyTheme(getPreferredTheme());

    // Listener nativo y delegado a nivel documento (no requiere jQuery ni que el botón exista ya)
    document.addEventListener('click', function (e) {
        const btn = e.target && e.target.closest ? e.target.closest('#themeToggle') : null;
        if (!btn) return;
        e.preventDefault();
        const isDark = document.documentElement.classList.contains('theme-dark');
        const next = isDark ? 'light' : 'dark';
        setStoredTheme(next);
        applyTheme(next);
    });

    // Neon Cards (auto-aplicar): eliminado en revert

})();
