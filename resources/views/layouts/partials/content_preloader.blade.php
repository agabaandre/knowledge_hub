@php
    $preloaderLabel = settings()->site_name ?? 'Knowledge Hub';
    $preloaderLabel = \Illuminate\Support\Str::limit(strip_tags($preloaderLabel), 48, '…');
@endphp
<div class="khub-content-preloader" id="khub-content-preloader" aria-live="polite" aria-busy="true" role="status">
    <div class="khub-content-preloader__inner">
        <div class="khub-content-preloader__spinner" aria-hidden="true">
            <span class="khub-content-preloader__ring"></span>
        </div>
        <p class="khub-content-preloader__label">{{ $preloaderLabel }}</p>
    </div>
</div>
<script>
(function () {
    var el = document.getElementById('khub-content-preloader');
    if (!el) return;
    var top = 0;
    var header = document.getElementById('header') || document.querySelector('header.header');
    var secondary = document.getElementById('khub-secondary-nav') || document.querySelector('.secondary-nav');
    if (header) top = Math.max(top, header.getBoundingClientRect().bottom);
    if (secondary && secondary.offsetParent !== null) {
        top = Math.max(top, secondary.getBoundingClientRect().bottom);
    }
    el.style.transition = 'none';
    el.style.top = Math.max(0, Math.round(top)) + 'px';
    requestAnimationFrame(function () { el.style.transition = ''; });
})();
</script>
