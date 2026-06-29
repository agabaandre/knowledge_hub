/**
 * Helpdesk-style content preloader — fixed band between header and footer.
 */
(function () {
    var MIN_VISIBLE_MS = 1200;

    function syncChromeOffsets() {
        if (typeof window.khubSyncPreloaderChrome === 'function') {
            window.khubSyncPreloaderChrome();
        }
    }

    function syncFooterOffset() {
        var footer = document.querySelector('footer.footer, footer');
        if (footer) {
            document.documentElement.style.setProperty('--khub-chrome-footer', footer.offsetHeight + 'px');
        }
    }

    function hidePreloader() {
        var el = document.getElementById('khub-content-preloader');
        if (!el || el.classList.contains('is-hidden')) {
            return;
        }

        syncChromeOffsets();

        var shownAt = window.__khubPreloaderShownAt || Date.now();
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

        syncFooterOffset();
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
