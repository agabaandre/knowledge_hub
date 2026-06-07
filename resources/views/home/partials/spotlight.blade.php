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
    
    // Check if AI Search is enabled
    $aiSearchEnabled = settings()->enable_ai_search ?? true;
    
    // Get primary color and create a shade for AI Search button
    $primaryColor = $settings->primary_color ?? '#119A48';
    // Create a darker shade (reduce lightness by ~15%)
    $primaryColorRgb = sscanf($primaryColor, "#%02x%02x%02x");
    if ($primaryColorRgb) {
        $aiSearchColor = sprintf("#%02x%02x%02x", 
            max(0, min(255, (int)($primaryColorRgb[0] * 0.85))),
            max(0, min(255, (int)($primaryColorRgb[1] * 0.85))),
            max(0, min(255, (int)($primaryColorRgb[2] * 0.85)))
        );
    } else {
        $aiSearchColor = '#0e7a3a'; // Fallback darker green
    }
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
</style>
<div class="spotlight home-spotlight px-3 py-3" style="{{ $bgStyle }} --spotlight-overlay-bg: {{ !empty($bannerImage) ? $overlayGradient : 'transparent' }};">
    <h1 class="sr-only">{{ settings()->site_name ?? settings()->title ?? 'Africa Health Knowledge Hub' }}@if(!empty(settings()->slogan)) — {{ settings()->slogan }}@endif</h1>
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
            <div class="{{ $aiSearchEnabled ? 'col-xl-2' : 'col-xl-4' }} col-lg-{{ $aiSearchEnabled ? '2' : '4' }} col-md-{{ $aiSearchEnabled ? '2' : '4' }} col-sm-12 col-12 bg-show">
                <div class="form-group mb-0 position-relative">
                    <button class="btn full-width text-white fs-md ai-search-btn-primary" type="submit" style="
                        background: {{ $primaryColor }};
                        border: none;
                        border-radius: {{ $aiSearchEnabled ? '0' : '0 0.375rem 0.375rem 0' }};
                        border-right: {{ $aiSearchEnabled ? '1px solid rgba(255,255,255,0.3)' : 'none' }};
                        height: 100%;
                        min-height: 62px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                    ">
                        <i class="fa fa-magnifying-glass"></i> <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</span>
                    </button>
                </div>
            </div>
            @if($aiSearchEnabled)
            <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12 bg-show">
                <div class="form-group mb-0 position-relative">
                    <button type="button" class="btn full-width text-white fs-md ai-search-btn-home" style="
                        background: {{ $aiSearchColor }};
                        border: none;
                        border-radius: 0;
                        border-left: 1px solid rgba(255,255,255,0.3);
                        height: 100%;
                        min-height: 62px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 0.5rem;
                    ">
                        <i class="fa fa-robot"></i> <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.ai_search">{{ __('home_sections.ai_search') }}</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
        @include('partials.search.advanced_search')
        <div class="{{ $aiSearchEnabled ? 'col-md-6' : 'col-md-12' }} sm-show mt-1 d-md-none">
            <button class="btn full-width text-white fs-md py-3 ai-search-btn-primary" type="submit" style="background: {{ $primaryColor }}; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa fa-magnifying-glass"></i> <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.search">{{ __('home_sections.search') }}</span>
            </button>
        </div>
        @if($aiSearchEnabled)
        <div class="col-md-6 sm-show mt-1 d-md-none">
            <button type="button" class="btn full-width text-white fs-md py-3 ai-search-btn-home" style="background: {{ $aiSearchColor }}; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                <i class="fa fa-robot"></i> <span class="khub-i18n-text notranslate" data-khub-i18n="home_sections.ai_search">{{ __('home_sections.ai_search') }}</span>
            </button>
        </div>
        @endif
        </form>

        @if(settings()->show_quotes ?? false)
        <div class="row spot-row col-sm-12 mt-3">
            <div class="col-12">
                @include('home.partials.quotes')
            </div>
        </div>
        @endif
        
        @if($aiSearchEnabled)
        <style>
            .ai-search-btn-primary:hover {
                opacity: 0.9 !important;
                filter: brightness(0.95) !important;
            }
            .ai-search-btn-home:hover {
                opacity: 0.9 !important;
                filter: brightness(0.95) !important;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/js/lobibox.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lobibox@1.2.7/dist/css/lobibox.min.css" />
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aiSearchBtns = document.querySelectorAll('.ai-search-btn-home');
            aiSearchBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (typeof Lobibox !== 'undefined') {
                        Lobibox.notify('info', {
                            title: 'AI Search',
                            msg: 'Coming Soon',
                            sound: false,
                            delay: 3000,
                            position: 'top right'
                        });
                    } else {
                        alert('AI Search - Coming Soon');
                    }
                });
            });
        });
        </script>
        @endif
    </div>

    @if(settings()->show_health_themes ?? true)
    <div class="row spot-row col-sm-12 d-flex align-items-center" style="margin-top: 1rem;">
        @include('home.partials.theme_tabs')
    </div>
    @endif
</div>
