<div class="row">
    @if (@states_enabled())
        <div class="col-md-4 col-lg-4 mb-3">
            <div class="form-group">
                <label class="form-label-sm" for="rcc"><small>Region</small></label>
                @include('partials.regions.dropdown', [
                    'class' => 'rcc select2 form-control',
                    'selected' => @$search->rcc,
                    'allfield' => 'All',
                ])
            </div>
        </div>

        <div class="col-md-4 col-lg-4 mb-3">
            <div class="form-group">
                <label class="form-label-sm" for="country_id"><small>Member State</small></label>
                @include('partials.countries.dropdown', [
                    'class' => 'country select2 form-control',
                    'selected' => @$search->country_id,
                    'allfield' => 'All',
                ])
            </div>
        </div>

        <div class="col-md-4 col-lg-4 mb-3">
            <div class="form-group">
                <label class="form-label-sm" for="author_id"><small>Source</small></label>
                @include('partials.authors.dropdown', [
                    'class' => 'author select2 form-control',
                    'selected' => @$search->author_id,
                    'allfield' => 'All',
                ])
            </div>
        </div>
    @endif

    <div class="col-md-4 col-lg-4 mb-3">
        <div class="form-group">
            <label class="form-label-sm" for="thematic_area_id"><small>Thematic Area</small></label>
            @include('partials.publications.theme_dropdown', [
                'class' => 'theme select2 form-control',
                'selected' => @$search->thematic_area_id,
                'allfield' => 'All',
            ])
        </div>
    </div>

    <div class="col-md-4 col-lg-4 mb-3">
        <div class="form-group">
            <label class="form-label-sm" for="sub_thematic_area_id"><small>Sub-Thematic Area</small></label>
            @include('partials.publications.subtheme_dropdown', [
                'class' => 'subtheme select2 form-control',
                'selected' => @$search->sub_thematic_area_id,
                'allfield' => 'All',
            ])
        </div>
    </div>

    <div class="col-md-4 col-lg-4 mb-3">
        <div class="form-group">
            <label class="form-label-sm" for="category"><small>Category</small></label>
            @include('partials.datarecords.categories_dropdown', [
                'class' => 'category select2 form-control',
                'selected' => @$search->category,
                'allfield' => 'All',
            ])
        </div>
    </div>

    <div class="col-md-6 offset-md-6 d-none">
        <input type="submit" class="btn theme-bg text-white ft-medium apply-btn fs-sm rounded mt-3"
            value="Apply Filters">
    </div>
</div>


@include('partials.search.fields_js')
