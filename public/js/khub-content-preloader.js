/**
 * Helpdesk-style content preloader — fixed band between header and footer.
 */
(function () {
    var MIN_VISIBLE_MS = 1200;
    var shownAt = window.__khubPreloaderShownAt || Date.now();
    window.__khubPreloaderShownAt = shownAt;

    function syncChromeOffsets() {
        var content = document.getElementById('content') || document.getElementById('khub-page-content');
        var footer = document.querySelector('footer.footer, footer');
        var root = document.documentElement;

        if (content) {
            var top = content.getBoundingClientRect().top;
            root.style.setProperty('--khub-chrome-top', Math.max(0, Math.round(top)) + 'px');
        } else {
            var header = document.getElementById('header') || document.getElementById('navigation') || document.querySelector('.header');
            var topFallback = header ? header.getBoundingClientRect().bottom : 96;
            root.style.setProperty('--khub-chrome-top', Math.max(0, Math.round(topFallback)) + 'px');
        }

        if (footer) {
            root.style.setProperty('--khub-chrome-footer', footer.offsetHeight + 'px');
        }
    }

    function hidePreloader() {
        var el = document.getElementById('khub-content-preloader');
        if (!el || el.classList.contains('is-hidden')) {
            return;
        }

        syncChromeOffsets();

        var wait = Math.max(0, MIN_VISIBLE_MS - (Date.now() - shownAt));
        window.setTimeout(function () {
            if (!el || el.classList.contains('is-hidden')) {
                return;
            }

            el.classList.add('is-hidden');
            el.setAttribute('aria-busy', 'false');

            var remove = function () {
                if (el.parentNode) {
                    el.parentNode.removeChild(el);
                }
            };

            el.addEventListener('transitionend', remove, { once: true });
            window.setTimeout(remove, 450);
        }, wait);
    }

    function init() {
        var el = document.getElementById('khub-content-preloader');
        if (!el) {
            return;
        }

        syncChromeOffsets();
        window.requestAnimationFrame(syncChromeOffsets);
        window.addEventListener('resize', syncChromeOffsets, { passive: true });

        if (document.readyState === 'complete') {
            hidePreloader();
        } else {
            window.addEventListener('load', hidePreloader);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
