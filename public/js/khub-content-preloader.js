/**
 * Helpdesk-style content preloader — scoped to main content; nav/footer stay visible.
 */
(function () {
    var MIN_VISIBLE_MS = 450;
    var shownAt = Date.now();

    function hidePreloader() {
        var el = document.getElementById('khub-content-preloader');
        if (!el || el.classList.contains('is-hidden')) {
            return;
        }

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

    if (document.readyState === 'complete') {
        hidePreloader();
    } else {
        window.addEventListener('load', hidePreloader);
    }
})();
