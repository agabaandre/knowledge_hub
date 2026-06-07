<div class="row py-2 advanced_filters main_search mt-2" style="padding-bottom:0px; margin-left: 0; margin-right: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
    @php
        $advanced_filter =
            @$search->rcc ||
            @$search->country_id ||
            @$search->author_id ||
            @$search->author ||
            @$search->file_type_id ||
            @$search->file_type ||
            @$search->file_category_id ||
            @$search->data_category_id ||
            @$search->category ||
            @$search->thematic_area_id ||
            @$search->sub_thematic_area_id ||
            @$search->subtheme
                ? true
                : false;

        // When theme uses header-style for this link (e.g. Theme1), parent CSS sets color
        $useThemeHeaderStyle = $use_theme_header_style ?? false;
        $textColor = $text_color ?? '#ffffff';
        if (!$useThemeHeaderStyle && !isset($text_color)) {
            $requestUri = request()->getRequestUri();
            $isHomePage = request()->is('/');
            $textColor = ($isHomePage || strpos($requestUri, '/records') !== false) ? '#ffffff' : '#475569';
        }
        if ($textColor === 'white') {
            $textColor = '#ffffff';
        }
        $textShadow = ($textColor === '#ffffff') ? '0 1px 3px rgba(0,0,0,0.3)' : 'none';
        $inlineStyle = $useThemeHeaderStyle ? 'font-size: 0.9375rem; padding: 0.5rem 0; display: block;' : 'color: ' . $textColor . ' !important; font-size:14px; padding: 0.5rem 0; display: block; text-shadow: ' . $textShadow . ';';
        $spanStyle = $useThemeHeaderStyle ? '' : 'color: ' . $textColor . ' !important; text-shadow: ' . $textShadow . ';';
        $iconStyle = $useThemeHeaderStyle ? 'transition: transform 0.3s ease;' : 'color: ' . $textColor . ' !important; transition: transform 0.3s ease; text-shadow: ' . $textShadow . ';';
    @endphp

    <a data-toggle="collapse" data-bs-toggle="collapse" data-bs-target="#collapseExample" href="#collapseExample" role="button" id="advanced_search"
        aria-expanded="false" aria-controls="collapseExample" class="advanced col-12 {{ $useThemeHeaderStyle ? 'advanced-search-theme-header-style' : '' }}"
        style="{{ $inlineStyle }}">
        <span class="filter text-bold notranslate" style="{{ $spanStyle }}" data-khub-i18n="home_sections.advance_search_filters">
            <i class="fa fa-sliders-h me-2"></i><span class="khub-i18n-text">{{ __('home_sections.advance_search_filters') }}</span>
        </span> 
        <i class="fa fa-angle-down ms-2" style="{{ $iconStyle }}"></i>
    </a>

    <div class="col-12 collapse mt-2" id="collapseExample" style="padding: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
        <div class="bg-white rounded p-3" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1); width: 100%; max-width: 100%; box-sizing: border-box;">

            @include('partials.search.search_fields')

        </div>
    </div>

</div>

<style>
#advanced_search[aria-expanded="true"] .fa-angle-down {
    transform: rotate(180deg);
}

.advanced_filters {
    width: 100%;
    max-width: 100%;
}

.advanced_filters .card-body {
    max-width: 100%;
}
</style>

<script>
$(document).ready(function() {
    $('#advanced_search').on('click', function() {
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        $(this).find('.fa-angle-down').css('transform', isExpanded ? 'rotate(0deg)' : 'rotate(180deg)');
    });
});
</script>
