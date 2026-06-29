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
