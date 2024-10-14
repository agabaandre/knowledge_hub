<div class="row py-3 advanced_filters main_search">

    @php

        $advanced_filter =
            @$search->rcc ||
            @$search->country_id ||
            @$search->author_id ||
            @$search->file_type_id ||
            @$search->thematic_area_id
                ? true
                : false;

    @endphp

    <a data-toggle="collapse" href="#collapseExample" role="button" id="advanced_search"
        aria-expanded="{{ $advanced_filter }}" aria-controls="collapseExample" class="advanced col-lg-12"
        style="color:var(--banner-text-color) !important; font-size:14px;">
        <span class="filter  text-bold" style="color:var(--banner-text-color) !important;"> Advance your Search With
            Filters</span> <i class="fa fa-angle-down" style="color:var(--banner-text-color) !important;"></i>
    </a>

    <div class="col-lg-12 {{ $advanced_filter ? '' : 'collapse' }}" id="collapseExample">
        <div class="card card-body">

            @include('partials.search.search_fields')

        </div>
    </div>

</div>
