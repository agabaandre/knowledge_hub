@php
    $categorySelected = old('data_category_id', @$search->data_category_id ?? @$search->category ?? '');
    $fileCategorySelected = old('file_category_id', @$search->file_category_id ?? '');
    $fileTypeSelected = old('file_type_id', @$search->file_type_id ?? @$search->file_type ?? '');
    // Desktop: 4 filters per row (col-lg-3). Tablet: 2 per row. Mobile: full width.
    $advCol = 'col-12 col-sm-6 col-lg-3 mb-3';
@endphp

@if (@states_enabled())
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="rcc"><small>Region</small></label>
            @include('partials.regions.dropdown', [
                'class' => 'rcc select2 form-control',
                'selected' => @$search->rcc,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="country_id"><small>Member State</small></label>
            @include('partials.countries.dropdown', [
                'class' => 'country select2 form-control',
                'selected' => @$search->country_id,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="thematic_area_id"><small>Thematic Area</small></label>
            @include('partials.publications.theme_dropdown', [
                'class' => 'theme select2 form-control',
                'selected' => @$search->thematic_area_id,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="sub_thematic_area_id"><small>Sub-Thematic Area</small></label>
            @include('partials.publications.subtheme_dropdown', [
                'class' => 'subtheme select2 form-control',
                'selected' => @$search->sub_thematic_area_id ?? @$search->subtheme,
                'allfield' => 'All',
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="file_category_id"><small>Sub Category</small></label>
            @include('partials.publications.filecategory_dropdown', [
                'field' => 'file_category_id',
                'class' => 'select2 form-control',
                'selected' => $fileCategorySelected,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="data_category_id"><small>Category</small></label>
            @include('partials.datarecords.categories_dropdown', [
                'class' => 'category select2 form-control',
                'field' => 'data_category_id',
                'selected' => $categorySelected,
                'allfield' => 'All',
                'exclude_special' => true,
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="author_id"><small>Source</small></label>
            @include('partials.authors.dropdown', [
                'class' => 'author select2 form-control',
                'selected' => @$search->author_id ?? @$search->author,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="file_type_id"><small>File type</small></label>
            @include('partials.publications.filetype_dropdown', [
                'field' => 'file_type_id',
                'class' => 'select2 form-control',
                'selected' => $fileTypeSelected,
                'allfield' => 'All',
            ])
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="thematic_area_id"><small>Thematic Area</small></label>
            @include('partials.publications.theme_dropdown', [
                'class' => 'theme select2 form-control',
                'selected' => @$search->thematic_area_id,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="sub_thematic_area_id"><small>Sub-Thematic Area</small></label>
            @include('partials.publications.subtheme_dropdown', [
                'class' => 'subtheme select2 form-control',
                'selected' => @$search->sub_thematic_area_id ?? @$search->subtheme,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="file_category_id"><small>Sub Category</small></label>
            @include('partials.publications.filecategory_dropdown', [
                'field' => 'file_category_id',
                'class' => 'select2 form-control',
                'selected' => $fileCategorySelected,
                'allfield' => 'All',
            ])
        </div>
    </div>
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="data_category_id"><small>Category</small></label>
            @include('partials.datarecords.categories_dropdown', [
                'class' => 'category select2 form-control',
                'field' => 'data_category_id',
                'selected' => $categorySelected,
                'allfield' => 'All',
                'exclude_special' => true,
            ])
        </div>
    </div>
</div>
<div class="row">
    <div class="{{ $advCol }}">
        <div class="form-group">
            <label class="form-label-sm" for="file_type_id"><small>File type</small></label>
            @include('partials.publications.filetype_dropdown', [
                'field' => 'file_type_id',
                'class' => 'select2 form-control',
                'selected' => $fileTypeSelected,
                'allfield' => 'All',
            ])
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-md-6 offset-md-6 d-none">
        <input type="submit" class="btn theme-bg text-white ft-medium apply-btn fs-sm rounded mt-3"
            value="Apply Filters">
    </div>
</div>


@include('partials.search.fields_js')
