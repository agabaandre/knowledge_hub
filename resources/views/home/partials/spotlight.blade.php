@php
    $settings = settings();
    $gradientStart = $settings->gradient_start_color ?? '#119A48';
    $gradientEnd = $settings->gradient_end_color ?? '#16c653';
    $bgStyle = "background: linear-gradient(135deg, {$gradientStart} 0%, {$gradientEnd} 100%) !important;";
    $primaryColor = $settings->primary_color ?? '#119A48';
@endphp
<style>
    .home-spotlight {
        position: relative;
        background-repeat: no-repeat !important;
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
<div class="spotlight home-spotlight px-3 py-3" style="{{ $bgStyle }}">
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

        @if(settings()->show_health_themes ?? true)
            @include('home.partials.theme_tabs')
        @endif
    </div>
</div>
