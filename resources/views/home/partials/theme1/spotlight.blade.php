@php
    use App\Support\SpotlightBackground;

    $spotlightBg = SpotlightBackground::resolve();
    $primary = settings()->primary_color ?? '#119A48';
@endphp
<style>
.theme1-spotlight {
    position: relative;
    padding: 2.5rem 0 1rem;
    min-height: 200px;
    scroll-margin-top: 72px;
    margin-top: 1.25rem;
    background-color: #f0f4f8;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /* Break out of padded layout so background fills full viewport width */
    width: 100vw;
    max-width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    box-sizing: border-box;
}
.theme1-spotlight::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(17, 154, 72, 0.12) 0%, rgba(22, 198, 83, 0.08) 100%);
    pointer-events: none;
    z-index: 0;
}
.theme1-spotlight.theme1-spotlight--has-banner::before {
    display: none;
}
.theme1-spotlight .theme1-spotlight-inner { position: relative; z-index: 1; }
.theme1-spotlight .banner-headline { color: #1e293b !important; font-size: 1.05rem; line-height: 1.5; }
.theme1-spotlight .banner-headline p { color: #334155 !important; margin: 0; }
.theme1-spotlight-search {
    max-width: 100%;
    margin: 1.5rem 0 0;
    width: 100%;
}
.theme1-spotlight-inner .col-lg-7 .theme1-spotlight-search { max-width: 100%; }
/* Hero keyword field only — do not target .advanced_filters (matches records / browse sizing) */
.theme1-spotlight-search > .input-group .form-control {
    height: 52px;
    font-size: 1rem;
    border: 2px solid #e2e8f0;
    border-right: 0;
    border-radius: 0.5rem 0 0 0.5rem;
    padding-left: 1rem;
    color: #1e293b;
    background: #fff;
}
.theme1-spotlight-search > .input-group .form-control::placeholder {
    color: #64748b;
    opacity: 1;
}
.theme1-spotlight-search > .input-group .form-control:focus {
    border-color: {{ $primary }};
    box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.15);
    outline: 0;
}
/* Advanced filters: align with standard hub controls (e.g. /records?category=…) */
.theme1-spotlight-search .advanced_filters .form-group .form-control,
.theme1-spotlight-search .advanced_filters select.form-control {
    height: auto;
    min-height: 38px;
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}
.theme1-spotlight-search .advanced_filters .form-label-sm,
.theme1-spotlight-search .advanced_filters label.form-label-sm {
    font-size: 0.8125rem;
}
.theme1-spotlight-search .advanced_filters .select2-container--default .select2-selection--single {
    min-height: 38px !important;
    height: 38px !important;
    padding: 0.125rem 0.5rem;
    font-size: 0.875rem;
    border: 1px solid #ced4da !important;
    border-radius: 0.25rem !important;
}
.theme1-spotlight-search .advanced_filters .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 2.125rem;
    padding-left: 0.25rem;
    font-size: 0.875rem;
    color: #495057;
}
.theme1-spotlight-search .advanced_filters .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
.theme1-spotlight-search .btn-search {
    height: 52px;
    padding: 0 1.5rem;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 0 0.5rem 0.5rem 0;
    background: {{ $primary }};
    border: 2px solid {{ $primary }};
    color: #fff;
}
.theme1-spotlight-search .btn-search:hover { background: #0d5034; border-color: #0d5034; color: #fff; }
.theme1-advanced-link {
    display: inline-block;
    margin-top: 1rem;
    color: #475569 !important;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
}
.theme1-advanced-link:hover { color: {{ $primary }} !important; }
.theme1-advanced-link i { margin-right: 0.35rem; }
/* Advance your Search link: match header nav styling; no hover state */
.theme1-spotlight .advanced_filters .advanced,
.theme1-spotlight .advanced_filters .advanced .filter,
.theme1-spotlight .advanced_filters .advanced .fa-angle-down {
    color: var(--text-color-primary, #1e293b) !important;
    font-weight: 500;
    font-size: 0.9375rem;
    text-shadow: none;
    text-decoration: none !important;
}
.theme1-spotlight .advanced_filters .advanced:hover,
.theme1-spotlight .advanced_filters .advanced:hover .filter,
.theme1-spotlight .advanced_filters .advanced:hover .fa-angle-down {
    color: var(--text-color-primary, #1e293b) !important;
    text-decoration: none !important;
}
.theme1-spotlight #quotes { scroll-margin-top: 72px; }
/* Quotes container: fixed min/max height, no white border */
.theme1-spotlight-quotes,
.theme1-spotlight-quotes .quotes-slider-wrapper,
.theme1-spotlight-quotes .quotes-slider,
.theme1-spotlight-quotes .quotes-slider .reviews_wrap,
.theme1-spotlight-quotes .quotes-slide {
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
}
.theme1-spotlight-quotes {
    min-height: 9rem;
    overflow: visible;
}
.theme1-spotlight-quotes .quotes-slider-wrapper {
    max-width: 100%;
    margin: 0;
    min-height: 9rem;
    max-height: none;
}
.theme1-spotlight-quotes .quotes-slider {
    min-height: 9rem;
    max-height: none;
}
/* Quotes: same structure as top searches card (image left, content right) but transparent; text fills space below image, justified */
.theme1-spotlight-quotes .reviews_wrap {
    text-align: left;
    background: transparent !important;
    padding: 0.5rem 0 0 !important;
    overflow: hidden;
    min-height: 12rem;
    max-height: none;
}
.theme1-spotlight-quotes .quotes-slider .reviews_wrap {
    max-height: none;
}
.theme1-spotlight-quotes .reviews_wrap::after {
    content: '';
    display: table;
    clear: both;
}
.theme1-spotlight-quotes .quotes-slide-img {
    float: left;
    width: 140px;
    height: 180px;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
    flex-shrink: 0;
}
.theme1-spotlight-quotes .quotes-slide-img img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 4px;
}
.theme1-spotlight-quotes .quotes-slide-text {
    margin-bottom: 0.35rem;
    text-align: justify;
    -webkit-line-clamp: unset;
    display: block;
    overflow: visible;
}
.theme1-spotlight-quotes .quotes-slide-link {
    display: inline-block;
    margin-top: 0.25rem;
}
@media (max-width: 575.98px) {
    .theme1-spotlight-quotes .quotes-slide-img {
        width: 110px;
        height: 140px;
        margin-right: 0.75rem;
    }
}
</style>
<div class="theme1-spotlight{{ $spotlightBg['has_banner'] ? ' theme1-spotlight--has-banner' : '' }}" style="{{ $spotlightBg['inline_style'] }}">
    <h1 class="visually-hidden notranslate">
        <span data-khub-i18n="ui_body.site_title">{{ \App\Support\UiLocaleLabels::siteTitle() }}</span>@if(\App\Support\UiLocaleLabels::siteTagline() !== '') — <span data-khub-i18n="ui_body.site_tagline">{{ \App\Support\UiLocaleLabels::siteTagline() }}</span>@endif
    </h1>
    <div class="container theme1-spotlight-inner">
        <div class="row align-items-start justify-content-center g-3">
            <div class="col-12 col-lg-7 col-xl-7">
                <div class="banner-headline">
                    <form action="{{ url('records/search') }}" method="get">
                        <div class="theme1-spotlight-search">
                            <div class="input-group shadow-sm">
                                <input type="search"
                                       name="term"
                                       id="autocomplete-input"
                                       class="form-control notranslate"
                                       placeholder="{{ __('home_sections.search_placeholder_resources') }}"
                                       value="{{ request('term') }}"
                                       aria-label="{{ __('home_sections.search_resources_label') }}"
                                       data-khub-i18n="home_sections.search_placeholder_resources"
                                       data-khub-i18n-aria="home_sections.search_resources_label">
                                <button class="btn btn-search notranslate" type="submit" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</button>
                            </div>
                        </div>
                        @include('partials.search.advanced_search', ['text_color' => null, 'use_theme_header_style' => true])
                    </form>
                </div>
            </div>
            @if(settings()->show_quotes ?? false)
            <div class="col-12 col-lg-5 col-xl-4">
                <div class="theme1-spotlight-quotes">
                    @include('home.partials.quotes')
                </div>
            </div>
            @endif
        </div>
        @if(settings()->show_health_themes ?? true)
        <div class="row mt-3 px-2 justify-content-center">
            @include('home.partials.theme1.theme_tabs')
        </div>
        @endif
    </div>
</div>
