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
    <h5 class="popular-tags-title mb-2">{{ __('Refine results') }}</h5>

    @if ($facetVisibleCategories->isNotEmpty())
    <h6 class="facet-group-title">{{ __('Category') }}</h6>
    <div class="facet-checkbox-column mb-3" data-facet-group="data_category_id">
        @foreach ($facetVisibleCategories as $category)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $category->id }}" data-param="data_category_id">
                <span>{{ $category->category_name }}</span>
            </label>
        @endforeach
    </div>
    @endif

    @if ($facetFileCategories->isNotEmpty())
    <h6 class="facet-group-title">{{ __('Sub category') }}</h6>
    <div class="facet-checkbox-column mb-3" data-facet-group="file_category_id">
        @foreach ($facetFileCategories as $pubCategory)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $pubCategory->id }}" data-param="file_category_id">
                <span>{{ $pubCategory->category_name }}</span>
            </label>
        @endforeach
    </div>
    @endif

    @if ($facetFileTypes->isNotEmpty())
    <h6 class="facet-group-title">{{ __('File type') }}</h6>
    <div class="facet-checkbox-column mb-3" data-facet-group="file_type_id">
        @foreach ($facetFileTypes as $filetype)
            <label class="facet-checkbox-label">
                <input type="checkbox" class="search-facet-cb" value="{{ $filetype->id }}" data-param="file_type_id">
                <span>{{ $filetype->name }}</span>
            </label>
        @endforeach
    </div>
    @endif

    <button type="button" class="btn btn-sm btn-primary w-100" id="search-sidebar-facets-apply">{{ __('Apply filters') }}</button>
</div>
@endif

<script>
(function () {
    function removeFacetParams(params) {
        var strip = ['data_category_id', 'data_category_id[]', 'file_category_id', 'file_category_id[]',
            'file_type_id', 'file_type_id[]', 'file_type', 'category'];
        strip.forEach(function (k) { params.delete(k); });
    }

    function getMultiParam(params, base) {
        var bracketed = params.getAll(base + '[]');
        if (bracketed.length) {
            return bracketed;
        }
        var single = params.get(base);
        if (single !== null && single !== '') {
            return [single];
        }
        if (base === 'data_category_id') {
            var c = params.get('category');
            if (c !== null && c !== '') {
                return [c];
            }
        }
        if (base === 'file_type_id') {
            var ft = params.get('file_type');
            if (ft !== null && ft !== '') {
                return [ft];
            }
        }
        return null;
    }

    function initSearchSidebarFacets() {
        var params = new URLSearchParams(window.location.search);
        var groups = ['data_category_id', 'file_category_id', 'file_type_id'];
        groups.forEach(function (paramName) {
            var selected = getMultiParam(params, paramName);
            document.querySelectorAll('.search-facet-cb[data-param="' + paramName + '"]').forEach(function (cb) {
                if (selected === null) {
                    cb.checked = true;
                } else {
                    cb.checked = selected.indexOf(cb.value) !== -1 || selected.indexOf(String(cb.value)) !== -1;
                }
            });
        });
    }

    function applySearchSidebarFacets() {
        var params = new URLSearchParams(window.location.search);
        removeFacetParams(params);

        var groups = [
            { param: 'data_category_id', selector: '.search-facet-cb[data-param="data_category_id"]' },
            { param: 'file_category_id', selector: '.search-facet-cb[data-param="file_category_id"]' },
            { param: 'file_type_id', selector: '.search-facet-cb[data-param="file_type_id"]' }
        ];

        groups.forEach(function (g) {
            var boxes = document.querySelectorAll(g.selector);
            if (!boxes.length) {
                return;
            }
            var checked = document.querySelectorAll(g.selector + ':checked');
            if (checked.length === 0 || checked.length === boxes.length) {
                return;
            }
            checked.forEach(function (cb) {
                params.append(g.param + '[]', cb.value);
            });
        });

        var q = params.toString();
        window.location.href = window.location.pathname + (q ? '?' + q : '');
    }

    document.addEventListener('DOMContentLoaded', function () {
        initSearchSidebarFacets();
        var btn = document.getElementById('search-sidebar-facets-apply');
        if (btn) {
            btn.addEventListener('click', applySearchSidebarFacets);
        }
    });
})();
</script>
