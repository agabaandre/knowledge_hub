@php
    $preloaderEnabled = (bool) (settings()->preloader_enabled ?? true);
    $preloaderText = trim((string) (settings()->preloader_text ?? 'Loading'));
    if ($preloaderText === '') {
        $preloaderText = 'Loading';
    }
    $preloaderMinSeconds = (int) (settings()->preloader_min_seconds ?? 3);
    $preloaderMinSeconds = max(0, min(30, $preloaderMinSeconds));
@endphp
@if($preloaderEnabled)
<div class="khub-content-preloader" id="khub-content-preloader" aria-live="polite" aria-busy="true" role="status">
    <div class="khub-content-preloader__inner">
        <div class="khub-content-preloader__spinner" aria-hidden="true">
            <span class="khub-content-preloader__ring"></span>
        </div>
        <p class="khub-content-preloader__label">{{ $preloaderText }}</p>
    </div>
</div>
<script>window.KHUB_PRELOADER_CONFIG={enabled:true,minMs:{{ $preloaderMinSeconds * 1000 }},text:@json($preloaderText)};</script>
<script>
// Safety fallback: if external preloader JS fails to load, never leave overlay stuck.
(function () {
    var minMs = {{ $preloaderMinSeconds * 1000 }};
    var maxMs = Math.max(minMs + 12000, 15000);
    window.setTimeout(function () {
        var el = document.getElementById('khub-content-preloader');
        if (!el) return;
        el.classList.add('is-hidden');
        el.setAttribute('aria-busy', 'false');
        if (document.body) document.body.classList.remove('khub-preloader-active');
        window.setTimeout(function () {
            if (el && el.parentNode) el.parentNode.removeChild(el);
        }, 450);
    }, maxMs);
})();
</script>
@else
<script>window.KHUB_PRELOADER_CONFIG={enabled:false,minMs:0,text:'Loading'};</script>
@endif
