<div class="row py-2 advanced_filters main_search mt-2" style="padding-bottom:0px; margin-left: 0; margin-right: 0; width: 100%; max-width: 100%; box-sizing: border-box;">
    @php
        $advanced_filter =
            @$search->rcc ||
            @$search->country_id ||
            @$search->author_id ||
            @$search->file_type_id ||
            @$search->thematic_area_id
                ? true
                : false;

        // Determine text color based on context
        // Check if text_color parameter is passed, otherwise detect context
        // Home page spotlight = white, regular pages with custom-bg = white, others = light gray
        $textColor = $text_color ?? '#ffffff';
        
        // If no explicit parameter, check the context
        if (!isset($text_color)) {
            // Check if we're in a spotlight or custom-bg context (home page or page_search)
            $requestUri = request()->getRequestUri();
            $isHomePage = request()->is('/');
            $isSearchPage = strpos($requestUri, '/records') !== false;
            
            // If on home page or in spotlight context, use white
            // Otherwise use light gray for regular pages
            if ($isHomePage) {
                $textColor = '#ffffff';
            } else {
                // For records/search pages, use white to match home page
                $textColor = '#ffffff';
            }
        }
        
        // Ensure consistent format
        if ($textColor === 'white') {
            $textColor = '#ffffff';
        }
        
        $textShadow = ($textColor === '#ffffff' || $textColor === 'white') ? '0 1px 3px rgba(0,0,0,0.3)' : 'none';
    @endphp

    <a data-toggle="collapse" href="#collapseExample" role="button" id="advanced_search"
        aria-expanded="false" aria-controls="collapseExample" class="advanced col-12"
        style="color: {{ $textColor }} !important; font-size:14px; padding: 0.5rem 0; display: block; text-shadow: {{ $textShadow }};">
        <span class="filter text-bold" style="color: {{ $textColor }} !important; text-shadow: {{ $textShadow }};"> 
            <i class="fa fa-sliders-h me-2"></i>Advance your Search With Filters
        </span> 
        <i class="fa fa-angle-down ms-2" style="color: {{ $textColor }} !important; transition: transform 0.3s ease; text-shadow: {{ $textShadow }};"></i>
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
