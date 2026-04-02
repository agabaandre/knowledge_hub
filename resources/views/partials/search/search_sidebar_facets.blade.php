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
<div class="sidebar-content search-facet-filters">
    <h5 class="popular-tags-title mb-3">{{ __('Refine results') }}</h5>

    @if ($facetFileTypes->isNotEmpty())
    <h5 class="popular-tags-title facet-subheading">{{ __('File type') }}</h5>
    <div class="facet-checkbox-column facet-checkbox-column--2col mb-3" data-facet-group="file_type_id">
        @foreach ($facetFileTypes as $filetype)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $filetype->id }}" data-param="file_type_id">
                <span>{{ $filetype->name }}</span>
            </label>
        @endforeach
    </div>
    @endif

    @if ($facetVisibleCategories->isNotEmpty())
    <h5 class="popular-tags-title facet-subheading">{{ __('Category') }}</h5>
    <div class="facet-checkbox-column facet-checkbox-column--2col mb-3" data-facet-group="data_category_id">
        @foreach ($facetVisibleCategories as $category)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $category->id }}" data-param="data_category_id">
                <span>{{ $category->category_name }}</span>
            </label>
        @endforeach
    </div>
    @endif

    @if ($facetFileCategories->isNotEmpty())
    <h5 class="popular-tags-title facet-subheading">{{ __('Sub category') }}</h5>
    <div class="facet-checkbox-column facet-checkbox-column--2col mb-3" data-facet-group="file_category_id">
        @foreach ($facetFileCategories as $pubCategory)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $pubCategory->id }}" data-param="file_category_id">
                <span>{{ $pubCategory->category_name }}</span>
            </label>
        @endforeach
    </div>
    @endif
</div>
@endif
