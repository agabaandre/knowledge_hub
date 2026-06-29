@php
    $preloaderLabel = settings()->site_name ?? 'Knowledge Hub';
@endphp
<div class="khub-content-preloader" id="khub-content-preloader" role="status" aria-live="polite" aria-busy="true" aria-label="Loading page content">
    <div class="khub-content-preloader__inner">
        <div class="khub-content-preloader__spinner" aria-hidden="true">
            <span class="khub-content-preloader__ring"></span>
        </div>
        <p class="khub-content-preloader__label">{{ $preloaderLabel }}</p>
    </div>
</div>
<script>
(function () {
    if (typeof window.khubSyncPreloaderChrome !== 'function') {
        window.khubSyncPreloaderChrome = function () {
            var root = document.documentElement;
            var content = document.getElementById('content') || document.getElementById('khub-page-content');
            var top = 0;

            if (content) {
                top = content.getBoundingClientRect().top;
            } else {
                ['#header', '#navigation', '.header', '.custom-bg', '#khub-secondary-nav', '.secondary-nav'].forEach(function (sel) {
                    document.querySelectorAll(sel).forEach(function (el) {
                        var rect = el.getBoundingClientRect();
                        if (rect.height > 0) {
                            top = Math.max(top, rect.bottom);
                        }
                    });
                });
            }

            root.style.setProperty('--khub-chrome-top', Math.max(0, Math.round(top)) + 'px');

            var footer = document.querySelector('footer.footer, footer');
            if (footer) {
                root.style.setProperty('--khub-chrome-footer', footer.offsetHeight + 'px');
            }
        };
    }

    window.khubSyncPreloaderChrome();

    var el = document.getElementById('khub-content-preloader');
    if (!el) {
        return;
    }

    requestAnimationFrame(function () {
        window.khubSyncPreloaderChrome();
        requestAnimationFrame(function () {
            window.khubSyncPreloaderChrome();
            el.classList.add('is-ready');
            window.__khubPreloaderShownAt = Date.now();
        });
    });
})();
</script>
