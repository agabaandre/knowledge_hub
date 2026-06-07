@php
    $settings = settings();
    $bannerImage = '';
    if (!empty($settings->spotlight_banner)) {
        $rawBanner = (string) $settings->spotlight_banner;
        $bannerImage = (strpos($rawBanner, 'http') === 0 || strpos($rawBanner, '//') === 0)
            ? $rawBanner
            : asset(ltrim($rawBanner, '/'));
    }
    $gradientStart = $settings->gradient_start_color ?? '#119A48';
    $gradientEnd = $settings->gradient_end_color ?? '#16c653';
    $overlayOpacityPercent = (int) ($settings->spotlight_overlay_opacity ?? 35);
    $overlayOpacity = max(0, min(100, $overlayOpacityPercent)) / 100;

    $gradientStartRgb = sscanf((string) $gradientStart, '#%02x%02x%02x');
    if (!is_array($gradientStartRgb) || count($gradientStartRgb) !== 3) {
        $gradientStartRgb = [17, 154, 72];
    }
    $gradientEndRgb = sscanf((string) $gradientEnd, '#%02x%02x%02x');
    if (!is_array($gradientEndRgb) || count($gradientEndRgb) !== 3) {
        $gradientEndRgb = [22, 198, 83];
    }
    $gradientStartRgba = 'rgba(' . (int) $gradientStartRgb[0] . ', ' . (int) $gradientStartRgb[1] . ', ' . (int) $gradientStartRgb[2] . ', ' . $overlayOpacity . ')';
    $gradientEndRgba = 'rgba(' . (int) $gradientEndRgb[0] . ', ' . (int) $gradientEndRgb[1] . ', ' . (int) $gradientEndRgb[2] . ', ' . $overlayOpacity . ')';
    $overlayGradient = "linear-gradient(135deg, {$gradientStartRgba} 0%, {$gradientEndRgba} 100%)";
    
    if (!empty($bannerImage)) {
        $bgStyle = "background-image: url('{$bannerImage}'); background-size: cover; background-position: center;";
    } else {
        $bgStyle = "background: linear-gradient(135deg, {$gradientStart} 0%, {$gradientEnd} 100%) !important;";
    }
    
    $primaryColor = $settings->primary_color ?? '#119A48';
@endphp
<style>
    .home-spotlight {
        position: relative;
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
    }
    .home-spotlight::before {
        content: '';
        position: absolute;
        inset: 0;
        background: var(--spotlight-overlay-bg, transparent);
        pointer-events: none;
        z-index: 0;
    }
    .home-spotlight > * {
        position: relative;
        z-index: 1;
    }
    .home-spotlight .search-btn-unified:hover {
        opacity: 0.92;
        filter: brightness(0.96);
    }
</style>
<div class="spotlight home-spotlight px-3 py-3" style="{{ $bgStyle }} --spotlight-overlay-bg: {{ !empty($bannerImage) ? $overlayGradient : 'transparent' }};">
    <h1 class="sr-only notranslate">
        <span data-khub-i18n="ui_body.site_title">{{ \App\Support\UiLocaleLabels::siteTitle() }}</span>@if(\App\Support\UiLocaleLabels::siteTagline() !== '') — <span data-khub-i18n="ui_body.site_tagline">{{ \App\Support\UiLocaleLabels::siteTagline() }}</span>@endif
    </h1>
    <div class="search-container" style="max-width: 1200px; margin: 0 auto; width: 100%; padding: 0 15px; box-sizing: border-box;">
        <form action="{{ url('records/search') }}" class="filters" role="search" aria-label="Search records">
        <div class="row no-gutters bg-white rounded search-form" id="simple_search" style="border-radius: 0.375rem !important;">
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
                <div class="form-group mb-0 position-relative main_search">
                    <label for="main-search" class="sr-only notranslate" data-khub-i18n="home_sections.search_keywords_label">{{ __('home_sections.search_keywords_label') }}</label>
                    <input type="text" id="main-search" class="form-control left-ico autocomplete term main-search notranslate"
                           name="term" value="{{ old('term') }}" placeholder="{{ __('home_sections.search_placeholder_keywords') }}" data-khub-i18n="home_sections.search_placeholder_keywords" />
                </div>
            </div>
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12 bg-show">
                <div class="form-group mb-0 position-relative">
                    <button class="btn full-width text-white fs-md search-btn-unified" type="submit" style="
                        background: {{ $primaryColor }};
                        border: none;
                        border-radius: 0 0.375rem 0.375rem 0;
                        height: 100%;
                        min-height: 62px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                    ">
                        <i class="fa fa-magnifying-glass"></i>
                        <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</span>
                    </button>
                </div>
            </div>
        </div>
        @include('partials.search.advanced_search')
        <div class="col-md-12 sm-show mt-1 d-md-none">
            <button class="btn full-width text-white fs-md py-3 search-btn-unified" type="submit" style="background: {{ $primaryColor }}; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa fa-magnifying-glass"></i>
                <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</span>
            </button>
        </div>
        </form>

        @if(settings()->show_quotes ?? false)
        <div class="row spot-row col-sm-12 mt-3">
            <div class="col-12">
                @include('home.partials.quotes')
            </div>
        </div>
        @endif
    </div>
</div>
