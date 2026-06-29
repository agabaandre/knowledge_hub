/**
 * Helpdesk-style content preloader: centered in the viewport band between header and footer.
 */
(function () {
    var MIN_MS = 600;
    var shownAt = Date.now();
    var preloader = null;

    function findChrome() {
        return {
            header: document.getElementById('header')
                || document.querySelector('header.header')
                || document.querySelector('#main-wrapper .header')
                || document.querySelector('.header'),
            footer: document.querySelector('footer.footer')
                || document.querySelector('footer')
                || document.querySelector('.footer'),
            secondary: document.getElementById('khub-secondary-nav')
                || document.querySelector('.secondary-nav'),
            search: document.querySelector('.custom-bg')
        };
    }

    function positionPreloader() {
        if (!preloader) {
            return;
        }

        var chrome = findChrome();
        var top = 0;

        if (chrome.header) {
            top = Math.max(top, chrome.header.getBoundingClientRect().bottom);
        }
        if (chrome.secondary && chrome.secondary.offsetParent !== null) {
            top = Math.max(top, chrome.secondary.getBoundingClientRect().bottom);
        }
        if (chrome.search && chrome.search.offsetParent !== null) {
            var searchRect = chrome.search.getBoundingClientRect();
            if (searchRect.height > 0) {
                top = Math.max(top, searchRect.bottom);
            }
        }

        var footerHeight = chrome.footer ? chrome.footer.offsetHeight : 72;

        preloader.style.top = Math.max(0, Math.round(top)) + 'px';
        preloader.style.bottom = Math.max(48, footerHeight) + 'px';
    }

    function hidePreloader() {
        if (!preloader || preloader.classList.contains('khub-content-preloader--out')) {
            return;
        }

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
        positionPreloader();
        window.addEventListener('resize', positionPreloader);
        window.addEventListener('scroll', positionPreloader, { passive: true });

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
