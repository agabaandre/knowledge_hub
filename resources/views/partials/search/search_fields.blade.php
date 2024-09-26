<div class="row">

    @if(@states_enabled())
        <div class="col-md-4">
            <label class="text-bold"><small>RCC</small></label>
            @include('partials.regions.dropdown', ['class' => 'rcc select2', 'selected' => @$search->rcc, 'allfield' => 'All'])
        </div>

        <div class="col-md-4">
            <label class="text-bold"><small>Member State</small></label>
            @include('partials.countries.dropdown', ['class' => 'country select2', 'selected' => @$search->country_id, 'allfield' => 'All'])
        </div>

        <div class="col-md-4">
            <label class="text-bold"><small>Source</small></label>
            @include('partials.authors.dropdown', ['class' => 'author select2', 'selected' => @$search->author_id, 'allfield' => 'All'])
        </div>

    @endif

    <div class="col-md-4">
        <label class="text-bold"><small>Thematic Area</small></label>
        @include('partials.publications.theme_dropdown', ['class' => 'theme select2', 'selected' => @$search->thematic_area_id, 'allfield' => 'All'])
    </div>

    <div class="col-md-4">
        <label class="text-bold"><small>Sub-Thematic Area</small></label>
        @include('partials.publications.subtheme_dropdown', ['class' => 'subtheme select2', 'selected' => @$search->sub_thematic_area_id, 'allfield' => 'All'])
    </div>

    <div class="col-md-4">
        <label class="text-bold"><small>Type</small></label>
        @include('partials.publications.filetype_dropdown', ['class' => 'filetype select2', 'selected' => @$search->file_type_id, 'allfield' => 'All'])
    </div>

    <div class="col-md-6 offset-md-6">
        <input type="submit" class="btn theme-bg text-white ft-medium apply-btn fs-sm rounded mt-3"
            value="Apply Filters">
    </div>
</div>


@include('partials.search.fields_js')