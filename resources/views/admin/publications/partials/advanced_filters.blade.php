@php
    $facetScalarForSelect = function ($value) {
        if ($value === null || $value === '' || $value === 'all') {
            return '';
        }
        if (is_array($value)) {
            $ids = array_values(array_filter(array_map('intval', $value), function ($id) {
                return $id > 0;
            }));

            return $ids !== [] ? $ids[0] : '';
        }

        return $value;
    };
    $categorySelected = $facetScalarForSelect(old('data_category_id', @$search->data_category_id ?? @$search->category ?? ''));
    $fileCategorySelected = $facetScalarForSelect(old('file_category_id', @$search->file_category_id ?? ''));
    $advCol = 'col-12 col-sm-6 col-lg-3';
@endphp

<div class="pub-filters-advanced__label">
    <i class="fa fa-layer-group"></i> {{ ui_t('admin_publications.advanced_filters_label', 'Advanced filters') }}
</div>

@if (@states_enabled())
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="rcc">{{ ui_t('admin_publications.filter_region', 'Region') }}</label>
            @include('partials.regions.dropdown', [
                'class' => 'rcc select2 form-control pub-filter-input',
                'selected' => @$search->rcc,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="country_id">{{ ui_t('admin_publications.filter_member_state', 'Member State') }}</label>
            @include('partials.countries.dropdown', [
                'class' => 'country select2 form-control pub-filter-input',
                'selected' => @$search->country_id,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="thematic_area_id">{{ ui_t('admin_publications.filter_thematic_area', 'Thematic Area') }}</label>
            @include('partials.publications.theme_dropdown', [
                'class' => 'theme select2 form-control pub-filter-input',
                'selected' => @$search->thematic_area_id,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="sub_thematic_area_id">{{ ui_t('admin_publications.filter_sub_thematic', 'Sub-Thematic Area') }}</label>
            @include('partials.publications.subtheme_dropdown', [
                'class' => 'subtheme select2 form-control pub-filter-input',
                'selected' => @$search->sub_thematic_area_id ?? @$search->subtheme,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="file_category_id">{{ ui_t('admin_publications.filter_sub_category', 'Sub Category') }}</label>
            @include('partials.publications.filecategory_dropdown', [
                'field' => 'file_category_id',
                'id' => 'file_category_id',
                'class' => 'select2 form-control pub-filter-input',
                'selected' => $fileCategorySelected,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="data_category_id">Category</label>
            @include('partials.datarecords.categories_dropdown', [
                'class' => 'category select2 form-control pub-filter-input',
                'field' => 'data_category_id',
                'id' => 'data_category_id',
                'selected' => $categorySelected,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
                'exclude_special' => true,
            ])
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="thematic_area_id">{{ ui_t('admin_publications.filter_thematic_area', 'Thematic Area') }}</label>
            @include('partials.publications.theme_dropdown', [
                'class' => 'theme select2 form-control pub-filter-input',
                'selected' => @$search->thematic_area_id,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="sub_thematic_area_id">{{ ui_t('admin_publications.filter_sub_thematic', 'Sub-Thematic Area') }}</label>
            @include('partials.publications.subtheme_dropdown', [
                'class' => 'subtheme select2 form-control pub-filter-input',
                'selected' => @$search->sub_thematic_area_id ?? @$search->subtheme,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="file_category_id">{{ ui_t('admin_publications.filter_sub_category', 'Sub Category') }}</label>
            @include('partials.publications.filecategory_dropdown', [
                'field' => 'file_category_id',
                'id' => 'file_category_id',
                'class' => 'select2 form-control pub-filter-input',
                'selected' => $fileCategorySelected,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group pub-filter-field">
            <label class="pub-filter-label" for="data_category_id">Category</label>
            @include('partials.datarecords.categories_dropdown', [
                'class' => 'category select2 form-control pub-filter-input',
                'field' => 'data_category_id',
                'id' => 'data_category_id',
                'selected' => $categorySelected,
                'allfield' => ui_t('admin_publications.filter_all', 'All'),
                'exclude_special' => true,
            ])
        </div>
    </div>
</div>
@endif

@include('partials.search.fields_js')
