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
