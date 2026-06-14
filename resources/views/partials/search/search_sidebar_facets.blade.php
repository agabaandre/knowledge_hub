@php
    $facetDataCategories = isset($data_categories) ? $data_categories : collect();
    $facetFileCategories = isset($file_categories) ? $file_categories : collect();
    $facetFileTypes = isset($file_types) ? $file_types : collect();
    $facetVisibleCategories = $facetDataCategories->filter(function ($c) {
        return empty($c->is_special);
    });
    $hasFacetGroups = $facetVisibleCategories->isNotEmpty() || $facetFileCategories->isNotEmpty() || $facetFileTypes->isNotEmpty();
@endphp
@if ($hasFacetGroups)
<div class="records-sidebar-card records-sidebar-card--filters search-facet-filters">
    <div class="records-sidebar-card__head">
        <span class="records-sidebar-card__icon records-sidebar-card__icon--filters" aria-hidden="true">
            <i class="fa fa-sliders-h"></i>
        </span>
        <div>
            <h2 class="records-sidebar-card__title">{{ __('Refine results') }}</h2>
            <p class="records-sidebar-card__hint">Filter resources by file type and category.</p>
        </div>
    </div>

    @if ($facetFileTypes->isNotEmpty())
    <div class="records-facet-group" data-facet-group="file_type_id">
        <div class="records-facet-group__head">
            <span class="records-facet-group__icon records-facet-group__icon--file" aria-hidden="true"><i class="fa fa-file"></i></span>
            <h3 class="records-facet-group__title">{{ __('File type') }}</h3>
        </div>
        <div class="records-facet-grid">
            @foreach ($facetFileTypes as $filetype)
                <label class="records-facet-chip facet-checkbox-label">
                    <input type="checkbox" class="search-facet-cb" value="{{ $filetype->id }}" data-param="file_type_id">
                    <span>{{ $filetype->name }}</span>
                </label>
            @endforeach
        </div>
    </div>
    @endif

    @if ($facetVisibleCategories->isNotEmpty())
    <div class="records-facet-group" data-facet-group="data_category_id">
        <div class="records-facet-group__head">
            <span class="records-facet-group__icon records-facet-group__icon--category" aria-hidden="true"><i class="fa fa-folder-open"></i></span>
            <h3 class="records-facet-group__title">{{ __('Category') }}</h3>
        </div>
        <div class="records-facet-grid">
            @foreach ($facetVisibleCategories as $category)
                <label class="records-facet-chip facet-checkbox-label">
                    <input type="checkbox" class="search-facet-cb" value="{{ $category->id }}" data-param="data_category_id">
                    <span>{{ $category->category_name }}</span>
                </label>
            @endforeach
        </div>
    </div>
    @endif

    @if ($facetFileCategories->isNotEmpty())
    <div class="records-facet-group" data-facet-group="file_category_id">
        <div class="records-facet-group__head">
            <span class="records-facet-group__icon records-facet-group__icon--subcategory" aria-hidden="true"><i class="fa fa-layer-group"></i></span>
            <h3 class="records-facet-group__title">{{ __('Sub category') }}</h3>
        </div>
        <div class="records-facet-grid">
            @foreach ($facetFileCategories as $pubCategory)
                <label class="records-facet-chip facet-checkbox-label">
                    <input type="checkbox" class="search-facet-cb" value="{{ $pubCategory->id }}" data-param="file_category_id">
                    <span>{{ $pubCategory->category_name }}</span>
                </label>
            @endforeach
        </div>
    </div>
    @endif
    <div id="records-facet-clear-wrap" class="records-sidebar-clear-wrap d-none">
        <button type="button" class="records-sidebar-tags__clear records-sidebar-tags__clear--btn js-records-clear-facets">
            Clear filters
        </button>
    </div>
</div>
@endif
