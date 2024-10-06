@php
    if (@$row && @$row->cover):
        $image_link = $row->image_url;
    else:
        $image_link = asset('assets/images/placeholder.jpg');
    endif;
@endphp

<!-- SmartWizard html -->
<div id="smartwizard" class="mt-3">
    <ul class="nav">
        <li>
            <a class="nav-link" href="#step-1"> <strong>Step 1</strong>
                <br>Start Here</a>
        </li>
        <li>
            <a class="nav-link" href="#step-2"> <strong>Step 2</strong>
                <br>Resource Categorization</a>
        </li>
        <li>
            <a class="nav-link" href="#step-3"> <strong>Step 3</strong>
                <br>Resource Details</a>
        </li>
        <li>
            <a class="nav-link" href="#step-4"> <strong>Step 4</strong>
                <br>Resource Attachments</a>
        </li>

    </ul>


    <div class="tab-content">
        <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
            <div class="row">


                <div class="col-md-12 mt-3 ml-3">
                    <label class="form-check-inline px-2">
                        <input type="radio" name="upload_type" value="upload" checked class="form-check-input">
                        Attachment
                    </label>
                    <label class="form-check-inline px-2">
                        <input type="radio" name="upload_type" value="link" class="form-check-input">External Link
                    </label>

                    <label class="form-check-inline px-2">
                        <input type="checkbox" name="is_embedded" value="1" class="form-check-input">Embedded On
                        Page
                    </label>

                    @if (is_admin())
                        <label class="form-check-inline px-2">
                            <input type="checkbox" name="is_default" value="1" class="form-check-input"
                                {{ @$row->is_default_in_category ? 'checked' : '' }}> Default in Category
                        </label>
                        <label class="form-check-inline px-2">
                            <input type="checkbox" name="admin_only" value="1" class="form-check-input"
                                {{ @$row->is_admin_only_access ? 'checked' : '' }}> Admin Only Access
                        </label>
                    @endif
                </div>

                <div class="col-md-12 mt-3">

                    <input type="hidden" name="id" id="id" class="newform"
                        value="{{ @$row->id ?? old('id') }}">


                    <h3>What is the title of the resource you what to publish?</h3>

                    <div class="mb-3">
                        <input placeholder="Resource Title" class="form-control newform" id="title" name="title"
                            value="{{ @$row->title ?? old('title') }}" required="">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Resource Type</label>
                        @include('partials.datarecords.categories_dropdown', [
                            'field' => 'data_category_id',
                            'required' => 'required',
                            'exclude_special' => true,
                            'selected' => @$row->data_category_id
                                ? $row->data_category_id
                                : old('data_category_id') ?? '',
                        ])
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">RCC</label>
                        @include('partials.regions.dropdown', [
                            'field' => 'rcc',
                            'class' => 'rcc',
                            'selected' => @$row->country->region_id ? $row->country->region_id : old('rcc') ?? '',
                        ])
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Member States</label>
                        @include('partials.countries.dropdown', [
                            'field' => 'countries[]',
                            'required' => 'required',
                            'class' => 'country select2',
                            'selected' =>
                                $row->countries ?? false && @$row->countries->pluck('id')->toArray()
                                    ? $row->countries->pluck('id')->toArray()
                                    : old('geo_area_id') ?? current_user()->country_id,
                            'multiple' => 'multiple',
                        ])
                    </div>
                </div>
            </div>
        </div>

        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
            <br>
            <h3>Choose resource categorization</h3>
            <div class="row">

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Resource Category</label>
                        @include('partials.publications.filecategory_dropdown', [
                            'field' => 'category_id',
                            'selected' => @$row->publication_catgory_id
                                ? $row->publication_catgory_id
                                : old('category_id'),
                        ])
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Thematic Area</label>
                        @include('partials.publications.theme_dropdown', [
                            'field' => 'theme',
                            'class' => 'select2 theme',
                            'selected' => @$row->sub_thematic_area_id ? $row->sub_thematic_area_id : old('theme'),
                        ])
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Sub Theme</label>
                        @include('partials.publications.subtheme_dropdown', [
                            'field' => 'sub_theme',
                            'class' => 'select2 subtheme',
                            'selected' => @$row->sub_thematic_area_id ? $row->sub_thematic_area_id : '',
                        ])
                    </div>
                </div>

                <div class="col-md-12 url_wrapper">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Publication URL Link</label>
                        <input type="text" placeholder="URL Link" class="form-control url" id="publication"
                            name="link" value="{{ @$row->publication ?? old('publication') }}">
                    </div>
                </div>
            </div>
        </div>

        <div id="step-3" class="tab-pane" role="tabpanel" aria-labelledby="step-3">
            <br>
            <h3>Provide publication details</h3>
            <div class="row">

                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label" for="summernote">Publication Description</label>
                        <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required="">{!! $row->description ?? old('description') !!}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div id="step-4" class="tab-pane" role="tabpanel" aria-labelledby="step-4">
            <br>
            <h3>Provide Attachments</h3>
            <div class="row" style="min-height: 300px;">


                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Cover Image</label>
                        <div class="custom-file">
                            <input type="file" style="display: none;" name="cover" id="cover">
                            <div onclick="$('#cover').click()" class="cover_preview py-2 rounded"
                                style="max-width:300px; min-height:200px; max-height:550px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 attachment">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Publication Attachments</label>

                        @if (@$row && @$row->has_attachments)

                            @php
                                $count = 1;
                            @endphp

                            @foreach ($publication->attachments as $pub_file)
                                <br><a href="{{ url('uploads/publications/') }}{{ $pub_file->file }}" target="_blank"
                                    class="btn btn-md rounded bg-white border fs-sm ft-medium col-lg-12 text-left"><i
                                        class="fa fa-file"></i> View Attachment {{ $count > 1 ? $count : '' }}</a>
                                @php
                                    $count++;
                                @endphp
                            @endforeach
                        @endif

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="files" id="attachments">
                            <label class="custom-file-label" for="validatedCustomFile">Choose file.s..</label>
                            <div class="preview py-2"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                        <!-- <a href="#" class="btn btn-sm btn-dark btn-outline mb-2"><i class="fa fa-plus"></i> Add Community Of Practice</a> -->
                        @include('partials.publications.publication_communities_dropdown', [
                            'field' => 'communities[]',
                            'selected' => @$row->communities ? $row->communities->pluck('id')->toArray() : [],
                        ])
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="sources">Associated Authors</label>
                        <input type="text" class="form-control" name="associated_authors"
                            placeholder="Associated Authors"
                            value="{{ @$row->associated_authors ?? old('associated_authors') }}">
                    </div>
                </div>

            </div>
            <div class="col-md-6 justify-content-center video" style="display: none;">
                <label class="form-label" for="publication">Video</label>
                <div class="mb-3">
                    <iframe width="450" height="260"class="vid" src="">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="row mt-3 mb-3 submit">
            <div class="col-lg-8 mt-5  float-end">
            </div>
            <div class="col-lg-3 mt-5  float-end">
                <button class="btn btn-dark col-lg-12 savebtn" type="submit" id="submit">
                    {{ @$row ? 'Save Changes' : 'Submit' }}
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Include optional progressbar HTML -->
<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
        aria-valuemax="100"></div>
</div>

<br>
</div>

@include('partials.search.fields_js')

<script type="text/javascript">
    $(document).ready(function() {

        $('.theme').on('change', function(e) {

            var themes = @json($themes);
            const theme = themes.find((item) => item.id === parseFloat(e.target.value));
            theme_subs = theme.subthemes;

            $('.subtheme').html('');

            theme_subs.forEach(item => {
                $('.subtheme').append(
                    `<option value="${item.id}">${item.description}</option>`);
            });

        });

        $('input[name="upload_type"]').on('change', function() {

            /*
            if ($(this).val() == 'upload') {
                  $('.url_wrapper').show();
                  $('.url_wrapper').hide();
              } else {
                  $('.url_wrapper').hide();
                  $('.url_wrapper').show();
              }

              */

        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            autoAdjustHeight: false,
            selected: 0,
            theme: 'dots',
            toolbarSettings: {
                toolbarPosition: 'both', // both bottom
            },
        });

        // Step show event
        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {

            console.log('Step Position', stepNumber);

            $("#prev-btn").removeClass('disabled');
            $("#next-btn").removeClass('disabled');
            $("#submit").addClass('disabled');
            $(".submit").hide();

            if (stepPosition === 'first') {
                $("#prev-btn").addClass('disabled');
                $("#submit").addClass('disabled');
            } else if (stepPosition === 'last') {
                $("#next-btn").addClass('disabled');
                $("#submit").removeClass('disabled');
                $(".submit").show();
            } else {
                $("#prev-btn").removeClass('disabled');
                $("#next-btn").removeClass('disabled');
                $("#submit").addClass('disabled');
                $(".submit").hide();
            }

        });


        $("#prev-btn").on("click", function() {
            // Navigate previous
            $('#smartwizard').smartWizard("prev");
            return true;
        });


        $("#next-btn").on("click", function() {

            $('#smartwizard').smartWizard("next");
            return true;

        });


    });


    $(function() {
        $('#publication_form').parsley().on('field:validated', function() {

                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);

            })
            .on('form:submit', function() {
                return false; // Don't submit form for this demo
            });
    });
</script>
