@php
    if (@$row && @$row->cover):
        $image_link = $row->image_url;
    else:
        $image_link = asset('assets/images/placeholder.jpg');
    endif;

    // dd($row->country_ids);

@endphp

<!-- SmartWizard html -->
<div id="smartwizard" class="mt-3">
    <style>
        /* Local layout refinements for this wizard only */
        #smartwizard .form-group { margin-bottom: 8px; }
        #smartwizard label.form-label, #smartwizard label { font-weight: 600; }
        #smartwizard .cover_preview { box-shadow: 0 0 0 1px #e2e8f0 inset; }
        #smartwizard .select2-container { width: 100% !important; }
        #smartwizard .mb-2 { margin-bottom: 8px !important; }
        #smartwizard .mt-2 { margin-top: 8px !important; }
    </style>
    <ul class="nav">
        <li>
            <a class="nav-link" href="#step-1"> <strong>Step 1</strong>
                <br>Start Here</a>
        </li>
        <li>
            <a class="nav-link" href="#step-2"> <strong>Step 2</strong>
                <br>Details & Attachments</a>
        </li>

    </ul>


    <div class="tab-content">
        <div id="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
            <div class="row" style="margin-top:12px;">
                <div class="col-lg-12 mb-2" style="padding-left:12px;">
                    <div class="d-flex flex-wrap align-items-center" style="gap:10px;">
                        <label class="form-check-inline mb-0">
                            <input type="radio" name="upload_type" value="upload" checked class="form-check-input"> Attachment
                        </label>
                        <label class="form-check-inline mb-0">
                            <input type="radio" name="upload_type" value="link" class="form-check-input"> External Link
                        </label>
                        <label class="form-check-inline mb-0">
                            <input type="checkbox" name="is_embedded" value="1" class="form-check-input" {{ @$row->is_embedded ? ' checked' : '' }}> Embedded On Page
                        </label>
                        @if (is_admin())
                        <label class="form-check-inline mb-0">
                            <input type="checkbox" name="is_default" value="1" class="form-check-input" {{ @$row->is_default_in_category ? ' checked' : '' }}> Default in Category
                        </label>
                        <label class="form-check-inline mb-0">
                            <input type="checkbox" name="admin_only" value="1" class="form-check-input" {{ @$row->is_admin_only_access ? ' checked' : '' }}> Admin Only Access
                        </label>
                        <label class="form-check-inline mb-0">
                            <input type="checkbox" name="show_disclaimer" value="1" class="form-check-input" {{ isset($row) ? ($row->show_disclaimer ? ' checked' : '') : 'checked' }}> Shows Disclaimer
                        </label>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <label>Category</label>
                    @include('partials.datarecords.categories_dropdown', [
                        'field' => 'data_category_id',
                        'required' => 'required',
                        'exclude_special' => true,
                        'selected' => @$row->publication_catgory_id ? $row->publication_catgory_id : old('data_category_id') ?? '',
                    ])
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Sub Category</label>
                    @include('partials.publications.filecategory_dropdown', [
                        'field' => 'category_id',
                        'selected' => @$row->data_category_id ? $row->data_category_id : old('category_id'),
                    ])
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Thematic Area</label>
                    @include('partials.publications.theme_dropdown', [
                        'field' => 'theme',
                        'class' => 'select2 theme',
                        'selected' => @$row->sub_theme->thematic_area_id ? $row->sub_theme->thematic_area_id : old('theme'),
                    ])
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Sub Theme</label>
                    @include('partials.publications.subtheme_dropdown', [
                        'field' => 'sub_theme',
                        'class' => 'select2 subtheme',
                        'selected' => @$row->sub_thematic_area_id ? $row->sub_thematic_area_id : '',
                    ])
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Region</label>
                    @include('partials.regions.dropdown', [
                        'field' => 'rccs[]',
                        'class' => 'rcc select2',
                        'selected' => @$row->region_ids ?? null,
                        'multiple' => 'multiple',
                        'allfield' => 'All',
                    ])
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Member States</label>
                    @include('partials.countries.dropdown', [
                        'field' => 'countries[]',
                        'required' => 'required',
                        'class' => 'country select2',
                        'selected' => $row->country_ids ?? null,
                        'multiple' => 'multiple',
                    ])
                </div>

                @if (is_admin())
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Author</label>
                    @include('partials.authors.dropdown', [
                        'field' => 'author',
                        'selected' => @$row->author_id ?? null,
                        'allfield' => 'Select Author',
                    ])
                </div>
                @endif
            </div>
        </div>

        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
            <br>
            <div class="col-md-12 mt-3">

                <input type="hidden" name="id" id="id" class="newform"
                    value="{{ @$row->id ?? old('id') }}">


                <h3>What is the title of the resource you want to publish?</h3>

                <div class="mb-3">
                    <input placeholder="Resource Title" class="form-control newform" id="title" name="title"
                        value="{{ @$row->title ?? old('title') }}" required="">
                </div>
            </div>


            <div class="col-md-12 url_wrapper">
                <div class="mb-3">
                    <label class="form-label" for="publication">Publication URL Link</label>
                    <input type="text" placeholder="URL Link" class="form-control url" id="publication"
                        name="link" value="{{ @$row->publication ?? old('publication') }}">
                </div>
            </div>
            <h3 class="mb-2" style="font-weight:600;">Attachments & Additional Details</h3>
            <div class="row" style="min-height: 0;">


                <div class="col-md-6">
                    <div class="mb-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="publication">Cover Image</label>
                        <div class="custom-file">
                            <input type="file" style="display: none;" name="cover" id="cover">
                            <div onclick="$('#cover').click()" class="cover_preview py-2"
                                style="width:200px; height:130px; margin-bottom:10px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat; display:block; clear:both;">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-2 p-2" style="background:#ffffff; position:relative; z-index:1;">
                        <label class="form-label" for="sources">Associated Authors</label>
                        <input type="text" class="form-control" name="associated_authors"
                            placeholder="Associated Authors"
                            value="{{ @$row->associated_authors ?? old('associated_authors') }}">
                    </div>
                    <div class="form-group mt-2 p-2" style="background:#ffffff; position:relative; z-index:1;">
                        <label class="form-label" for="sources">Associated Tags/Health Topics</label>
                        @include('partials.tags.dropdown', [
                            'field' => 'tags[]',
                            'selected' => @$row->tags ? $row->tags->pluck('id')->toArray() : [],
                        ])
                    </div>
                </div>

                <div class="col-md-6 attachment">
                    <div class="mb-2 p-2" style="background:#ffffff;">
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

                        <style>
                            .dropzone-attachments {
                                border: 2px dashed #94a3b8;
                                border-radius: 10px;
                                padding: 16px;
                                text-align: center;
                                cursor: pointer;
                                transition: background-color 0.2s ease, border-color 0.2s ease;
                                min-height: 90px;
                                position: relative;
                            }
                            .dropzone-attachments.dragover {
                                background-color: #f8fafc;
                                border-color: var(--theme-color-primary, #119A48);
                            }
                            .dropzone-attachments .hint { color: #64748b; font-size: 0.9rem; }
                            .cover_preview { display:block; }
                        </style>
                        <div class="dropzone-attachments" id="dropzone-attachments">
                            <i class="fa fa-cloud-upload-alt"></i>
                            <div class="hint">Drag & drop files here, or click to browse</div>
                            <small class="text-muted">Images, PDF, Word, Excel, PowerPoint, audio, video</small>
                            <input type="file" class="custom-file-input" name="files" id="attachments" multiple
                                   style="position:absolute;left:0;top:0;width:100%;height:100%;opacity:0;cursor:pointer;">
                        </div>
                        <div class="preview py-2" style="min-height: 24px;"></div>
                    </div>

                    <div class="form-group mt-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                        <!-- <a href="#" class="btn btn-sm btn-dark btn-outline mb-2"><i class="fa fa-plus"></i> Add Community Of Practice</a> -->
                        @include('partials.publications.publication_communities_dropdown', [
                            'field' => 'communities[]',
                            'selected' => @$row->communities ? $row->communities->pluck('id')->toArray() : [],
                        ])
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

            <br>
            <h3 class="mb-2" style="font-weight:600;">Publication Details</h3>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="summernote">Publication Description</label>
                        <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" required="">{!! $row->description ?? old('description') !!}</textarea>
                    </div>
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

        // Fancy drag & drop for attachments
        var $drop = $('#dropzone-attachments');
        var $input = $('#attachments');
        if ($drop.length && $input.length) {
            $drop.on('click', function() { $input.trigger('click'); });
            $drop.on('dragover dragenter', function(e) {
                e.preventDefault(); e.stopPropagation();
                $drop.addClass('dragover');
            });
            $drop.on('dragleave dragend drop', function(e) {
                e.preventDefault(); e.stopPropagation();
                $drop.removeClass('dragover');
            });
            $drop.on('drop', function(e) {
                var files = e.originalEvent.dataTransfer.files;
                if (!files || !files.length) return;
                // Merge with existing FileList
                const dT = new DataTransfer();
                // Add old files first
                if ($input[0].files && $input[0].files.length) {
                    Array.from($input[0].files).forEach(f => dT.items.add(f));
                }
                // Add new dropped files
                Array.from(files).forEach(f => dT.items.add(f));
                $input[0].files = dT.files;
                $input.trigger('change');
            });
        }

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
