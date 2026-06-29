/**
 * Content-area preloader: fixed band below header, centered once (no shift on load).
 */
(function () {
    var config = window.KHUB_PRELOADER_CONFIG || { enabled: true, minMs: 3000 };
    if (!config.enabled) {
        return;
    }

    var shownAt = Date.now();
    var hideTimer = null;
    var topLocked = false;

    function chromeBottomPx() {
        var content = document.getElementById('content') || document.getElementById('khub-page-content');
        if (content) {
            var contentTop = content.getBoundingClientRect().top;
            if (contentTop > 0) {
                return Math.ceil(contentTop);
            }
        }

        var bottom = 0;
        var selectors = [
            '#header',
            '.header',
            '.custom-bg',
            '#khub-secondary-nav',
            '.secondary-nav',
            '#root > .clearfix',
            '.front-container > .clearfix'
        ];

        selectors.forEach(function (sel) {
            document.querySelectorAll(sel).forEach(function (node) {
                if (!node || !node.getBoundingClientRect) {
                    return;
                }
                var rect = node.getBoundingClientRect();
                if (rect.height > 0 && rect.bottom > bottom) {
                    bottom = rect.bottom;
                }
            });
        });

        return Math.max(0, Math.ceil(bottom));
    }

    function lockPreloaderTopOnce() {
        if (topLocked) {
            return;
        }
        topLocked = true;
        document.documentElement.style.setProperty('--khub-preloader-top', chromeBottomPx() + 'px');
    }

    function hidePreloader() {
        var el = document.getElementById('khub-content-preloader');
        if (!el || el.classList.contains('is-hidden')) {
            return;
        }

        el.classList.add('is-hidden');
        el.setAttribute('aria-busy', 'false');
        document.body.classList.remove('khub-preloader-active');

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

    function initPreloader() {
        document.body.classList.add('khub-preloader-active');

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                lockPreloaderTopOnce();
            });
        });
    }

    function onDomReady() {
        initPreloader();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onDomReady);
    } else {
        onDomReady();
    }

    if (document.readyState === 'complete') {
        lockPreloaderTopOnce();
        scheduleHide();
    } else {
        window.addEventListener('load', function () {
            lockPreloaderTopOnce();
            scheduleHide();
        });
    }
})();
