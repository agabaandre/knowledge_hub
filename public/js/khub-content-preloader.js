/**
 * Content-area preloader (helpdesk-style): centered below header, above footer.
 * Respects admin min display time from window.KHUB_PRELOADER_CONFIG.
 */
(function () {
    var config = window.KHUB_PRELOADER_CONFIG || { enabled: true, minMs: 3000 };
    if (!config.enabled) {
        return;
    }

    var shownAt = Date.now();
    var hideTimer = null;

    function measureChrome() {
        var header = document.getElementById('header') || document.querySelector('.header, #main-wrapper > .header');
        var secondary = document.getElementById('khub-secondary-nav') || document.querySelector('.secondary-nav');
        var footer = document.querySelector('footer.footer, footer.skin-dark-footer, .footer.pt-5, footer');
        var top = 0;

        if (header) {
            top += header.getBoundingClientRect().height;
        }
        if (secondary) {
            top += secondary.getBoundingClientRect().height;
        }

        var footerHeight = footer ? footer.getBoundingClientRect().height : 72;
        document.documentElement.style.setProperty('--khub-chrome-top', Math.round(top) + 'px');
        document.documentElement.style.setProperty('--khub-chrome-footer', Math.round(footerHeight) + 'px');
    }

    function hidePreloader() {
        var el = document.getElementById('khub-content-preloader');
        if (!el || el.classList.contains('is-hidden')) {
            return;
        }

        el.classList.add('is-hidden');
        el.setAttribute('aria-busy', 'false');

        var shell = document.getElementById('khub-page-content') || document.getElementById('content');
        if (shell) {
            shell.classList.remove('khub-content-loading');
        }

        window.setTimeout(function () {
            if (el && el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 400);
    }

    function scheduleHide() {
        var minMs = parseInt(config.minMs, 10);
        if (isNaN(minMs) || minMs < 0) {
            minMs = 3000;
        }
        var wait = Math.max(0, minMs - (Date.now() - shownAt));

        if (hideTimer) {
            clearTimeout(hideTimer);
        }
        hideTimer = window.setTimeout(hidePreloader, wait);
    }

    function onReady() {
        measureChrome();
        var shell = document.getElementById('khub-page-content') || document.getElementById('content');
        if (shell) {
            shell.classList.add('khub-content-loading');
        }
        window.addEventListener('resize', measureChrome);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
    } else {
        onReady();
    }

    if (document.readyState === 'complete') {
        scheduleHide();
    } else {
        window.addEventListener('load', scheduleHide);
    }
})();
