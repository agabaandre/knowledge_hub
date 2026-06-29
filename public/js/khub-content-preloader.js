/**
 * Content preloader — single fixed band below header/nav (helpdesk-style).
 * Position is set once; no scroll/search remeasure (avoids jump while loading).
 */
(function () {
    var MIN_MS = 600;
    var shownAt = Date.now();
    var preloader = null;
    var positioned = false;

    function findHeaderBottom() {
        var top = 0;
        var header = document.getElementById('header')
            || document.querySelector('header.header')
            || document.querySelector('.header');
        var secondary = document.getElementById('khub-secondary-nav')
            || document.querySelector('.secondary-nav');

        if (header) {
            top = Math.max(top, header.getBoundingClientRect().bottom);
        }
        if (secondary && secondary.offsetParent !== null) {
            top = Math.max(top, secondary.getBoundingClientRect().bottom);
        }

        return Math.max(0, Math.round(top));
    }

    function findFooterHeight() {
        var footer = document.querySelector('footer.footer') || document.querySelector('footer');
        return footer ? Math.max(48, footer.offsetHeight) : 72;
    }

    function positionPreloader(force) {
        if (!preloader || (positioned && !force)) {
            return;
        }

        preloader.style.transition = 'none';
        preloader.style.top = findHeaderBottom() + 'px';
        preloader.style.bottom = findFooterHeight() + 'px';

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                if (preloader) {
                    preloader.style.transition = '';
                }
            });
        });

        positioned = true;
    }

    function hidePreloader() {
        if (!preloader || preloader.classList.contains('khub-content-preloader--out')) {
            return;
        }

        positionPreloader(true);

        var wait = Math.max(0, MIN_MS - (Date.now() - shownAt));
        window.setTimeout(function () {
            if (!preloader) {
                return;
            }
            preloader.classList.add('khub-content-preloader--out');
            preloader.setAttribute('aria-busy', 'false');

            var remove = function () {
                if (preloader && preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
                preloader = null;
            };

            preloader.addEventListener('transitionend', remove, { once: true });
            window.setTimeout(remove, 450);
        }, wait);
    }

    function init() {
        preloader = document.getElementById('khub-content-preloader');
        if (!preloader) {
            return;
        }

        shownAt = Date.now();
        positionPreloader(true);

        window.addEventListener('resize', function () {
            if (preloader && !preloader.classList.contains('khub-content-preloader--out')) {
                positionPreloader(true);
            }
        });

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
