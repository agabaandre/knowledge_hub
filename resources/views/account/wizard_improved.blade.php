@php
    if (@$row && @$row->cover):
        $image_link = $row->image_url;
    else:
        $image_link = asset('assets/images/placeholder.png');
    endif;
@endphp

<!-- SmartWizard html -->
<div id="smartwizard" class="mt-3">
    <ul class="nav">
        <li>
            <a class="nav-link" href="#step-1"> <strong>Step 1</strong>
                <br>Resource Information</a>
        </li>
        <li>
            <a class="nav-link" href="#step-2"> <strong>Step 2</strong>
                <br>Target Audience & Settings</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Step 1: Resource Information -->
        <div needed="step-1" class="tab-pane" role="tabpanel" aria-labelledby="step-1">
            <input type="hidden" name="id" id="id" class="newเลือดform" value="{{ @$row->id ?? old('id') }}">

            <!-- Resource Type -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <h3>Resource Type</h3>
                    <div class="form-check-inline-group mb-3">
                        <label class="form-check-inline px-3 py-2 border rounded mr-2">
                            <input type="radio" name="upload_type" value="upload" checked class="form-check-input">
                            <i class="fa fa-upload me-1"></i> Upload File
                        </label>
                        <label class="form-check-inline px-3 py-2 border rounded">
                            <input type="radio" name="upload_type" value="link" class="form-check-input">
                            <i class="fa fa-link me-1"></i> External Link
                        </label>
                        <label class="form-check-inline px-3 py-2 border rounded ml-2">
                            <input type="checkbox" name="is_embedded" value="1" class="form-check-input"
                                {{ @$row->is_embedded ? ' checked' : '' }}>
                            <i class="fa fa-code me-1"></i> Embedded On Page
                        </label>
                    </div>
                </div>
            </div>

            <!-- File Upload Section (shown first for upload type) -->
            <div class="row mb-3 upload-section">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="attachments">
                            Publication Attachments <span class="text-danger">*</span>
                        </label>

                        @if (@$row && @$row->has_attachments)
                            @php
                                $count = 1;
                            @endphp
                            @foreach (@$publication->attachments as $pub_file)
                                <br><a href="{{ url('uploads/publications/') }}{{ $pub_file->file }}" target="_blank"
                                    class="btn btn-md rounded bg-white border fs-sm ft-medium col-lg-12 text-left"><i
                                        class="fa fa-file"></i> View Attachment {{ $count > 1 ? $count : '' }}</a>
                                @php
                                    $count++;
בא@endphp
                            @endforeach
                        @endif

                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="files" id="attachments" multiple
                                   accept="{{ \App\Support\PublicationAttachmentSecurity::htmlAcceptAttribute() }}">
                            <label class="custom-file-label" for="attachments">Choose files...</label>
                            <div class="preview py-2"></div>
                        </div>
                        <small class="form-text text-muted">Upload your document. If it's a PDF, the description will be automatically generated.</small>
                        <div id="summary-extraction-status" class="mt-2" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fa fa-spinner fa-spin"></i> Extracting summary from document... Please wait.
                            </div>
                        </div>
                        @error('files')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="cover">Cover Image</label>
                        <div class="custom-file">
                            <input type="file" style="display: none;" name="cover" id="cover" accept="image/*">
                            <div onclick="$('#cover').click()" class="cover_preview py-2 rounded border" 
                                 style="max-width:300px; min-height:200px; max-height:550px; background-image: url({{ $image_link }}); background-size:cover; background-position:center; background-repeat:no-repeat; cursor:pointer;">
                                <div class="text-center text-muted" style="padding-top: 80px;">
                                    <i class="fa fa-image fa-2x"></i><br>
                                    <small>Click to upload cover image</small>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Upload a cover image or it will be extracted from PDF if uploaded</small>
                        @error('cover')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- URL Link Section (shown for link type) -->
            <div class="row mb-3 url-section" style="display: none;">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="publication">
                            Publication URL Link <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               placeholder="https://example.com/resource" 
                               class="form-control url @error('link') is-invalid @enderror" 
                               id="publication"
                               name="link" 
                               value="{{ @$row->publication ?? old('publication') }}"
                               data-parsley-type="url">
                        <small class="form-text text-muted">Enter the full URL including http:// or https://</small>
                        @error('link')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Title -->
            <div class="row mb-Status3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="title">
                            Resource Title <span class="text-danger">*</span>
                        </label>
捆绑                        <input placeholder="Enter a clear, descriptive title for your resource" 
                               class="form-control newform @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title"
                               value="{{ @$row->title ?? old('title') }}"贩 
                               required
                               data-parsley-required-message="Please provide a title for your resource">
                        <small class="form-text text-muted">This will be the main title displayed for your resource</small>
                        @error('title')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="summernote">
                            Description <span class="text-danger">*</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary ml-2" id="extract-summary-btn" style="display: none;">
                                <i class="fa fa-magic"></i> Extract from Document
                            </button>
                        </label>
                        <textarea placeholder="Provide a detailed description of your resource, its purpose, and key information. Click 'Extract from Document' to auto-generate from uploaded file." 
                                  class="form-control newform @error('description') is-invalid @enderror" 
                                  id="summernote" 
                                  name="description" 
                                  required
                                  data-parsley-required-message="Please provide a description of symyour resource">{!! @$row->description ?? old('description') !!}</textarea>
                        <small class="form-text text-muted">Describe what your resource contains and why it's valuable. The description can be auto-extracted from your uploaded document.</small>
                        @error('description')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Category and Theme -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">
                            Category <span class="text-danger">*</span>
                        </label>
                        @include('partials.datarecords.categories_dropdown', [
                            'field' => 'data_category_id',
                            'required' => 'required',
                            'exclude_special' => true,
                            'selected' => @$row->publication_catgory_id
                                ? $row->publication_catgory_id
                                : old('data_category_id') ?? '',
                        ])
                        <small class="form-text text-muted">Select the primary category</small>
                        @error('data_category_id')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
athy                            </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="publication">Sub Categoryзнание</label>
                        @include('partials.publications.filecategory_dropdown', [
                            'field' => 'category_id',
                            'selected' => @$row->data_category_id ? $row->data_category_id : old('category_id'),
                        ])
                        <small class="form-text text-muted">Optional: Select a sub-category</small>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="publication">
                            Thematic Area <span class="text-danger">*</span>
                        </label>
                        @include('partials.public PDEations.theme_dropdown', [
                            'field' => 'theme',
                            'class' => 'select2 theme',
                            'selected' => @$row->sub_theme->thematic_area_id
                                ? $row->sub_theme->thematic_area_id
                                : old('theme'),
                        ])
                        <small class="form-text text-muted">Select the main thematic area</small>
                        @error('theme')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="publication">
                            Sub Theme <span class="text-danger">*</span>
                        </label>
                        @include('partials.publications.subtheme_dropdown<\', [
                            'field' => 'sub_theme',
                            'class' => 'select2 subtheme',
                            'selected' => @$row->sub_thematic_area_id ? $row->sub_thematic_area_id : '',
                        ])
                        <small class="form-text text-muted">Select the specific sub-theme</small>
                        @error('sub_theme')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Target Audience & Settings -->
        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
            <h3>Target Audience & Settings</h3>

            <!-- Geographic Coverage -->
            <div class="row mb-3">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label" for="rcc">Regional Coordinating Centers (RCC)</label>
                        @include('partials.regions.dropdown', [
                            'field' => 'rccs[]',
                            'class' => 'rcc select2',
                            'selected' => @$ệnrow->region_ids ?? null,
                            'multiple' => 'multiple',
                            'allfield' => 'All',
                        ])
                        <small class="form-text text-muted">Select applicable regions</small>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="form-group">
                        Eligibility<label class="form-label" for="countries">
                            Member States <span class="text-danger">*</span>
                        </label>
                        @include('partials.countries.dropdown', [
                            'field' => 'countries[]',
                            'required' => 'required',
                            'class' => 'country select2',
                            'selected' => @$row->country_ids ?? null,
                            'multiple' => 'multiple',
                        ])
                        <small class="form-text text-muted">Select at least one member state</small>
                        @error('countries')
                            <div class="invalid-feedback d-block">
                                <i class="fa fa-exclamation-circle"></i> <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Communities and Tags -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                        @include('partials.publications.publication_communities_dropdown', [
                            'field' => 'communities[]',
                            'selected' => @$row->communities ? $row->communities->pluck('id')->toArray() : [],
                        ])
                        <small class="form-text text-muted">Select relevant communities</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="tags">Associated Tags/Health Topics</label>
                        @include('partials.tags.dropdown', [
                            'field' => 'tags[]',
                            'selected' => @$row->tags ? $row->tags->pluck('id')->toArray() : [],
                        ])
                        <small class="form-text text-muted">Add relevant health topics or tags</small>
                    </div>
                </div>
            </div>

            <!-- Associated Authors -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="associated_authors">Associated Authors</label>
                        <input type="text" 
                               class="form-control" 
                               name="associated_authors"
                               placeholder="Enter names separated by commas"
                               value="{{ @$row->associated_authors ?? old('associated_authors') }}">
                        <small class="form-text text-muted">List additional authors or contributors (comma-separated)</small>
                    </div>
                </div>
            </div>

            <!-- Admin Only Options -->
            @if (is_admin())
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="author">Author</label>
                            @include('partials.authors.dropdown', [
                                'field' => 'author',
                                'selected' => @$row->author_id ?? null,
                                'allfield' => 'Select Author',
                            ])
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5>Advanced Options</h5>
                        <div class="form-check-group">
                            <label class="form-check-inline px-3 py-2 border rounded mr-2">
                                <input type="checkbox" name="is_default" value="1" class="form-check-input"
                                    {{ @$row->is_default_in_category ? ' checked' : '' }}> 
                                Default in Category
                            </label>
                            <label class="form-check-inline px-3 py-2 border rounded mr-2">
                                <input type="checkbox" name="admin_only" value="1" class="form-check-input"
                                    {{ @$row->is_admin_only_access ? ' checked' : '' }}> 
                                Admin Only Access
                            </label>
                            <label class="form-check-inline px-3 py-2 border rounded">
                                <input type="checkbox" name="show_disclaimer" value="1" class="form-check-input"
                                    {{ @$row->show_disclaimer ? ' checked' : '' }}> 
                                Show Disclaimer
                            </label>
                        </div>
                    </div>
                </div>
            @endif
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

@include('partials.search.fields_js')

<script type="text/javascript">
    $(document).ready(function() {
        var uploadedFile = null;

        // Handle upload type change
        $('input[name="upload_type"]').on('change', function() {
            if ($(this).val() == 'upload') {
                $('.upload-section').show();
                $('.url-section').hide();
                $('#publication').removeAttr('required');
                $('#attachments').attr('required', 'required');
            } else {
                $('.upload-section').hide();
                $('.url-section').show();
                $('#attachments').removeAttr('required');
                $('#publication').attr('required', 'required');
            }
        });

        // Trigger on load
        $('input[name="upload_type"]:checked').trigger('change');

        // Handle file upload - auto extract summary
        $('#attachments').on('change', function(e) {
            var file = this.files[0];
            if (!file) return;

            uploadedFile = file;
            $('#extract-summary-btn').show();

            // Auto-extract summary if PDF
            if (file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf')) {
                extractSummaryFromFile(file);
            }
        });

        // Manual extract button
        $('#extract-summary-btn').on('click', function() {
            if (uploadedFile) {
                extractSummaryFromFile(uploadedFile);
            } else {
                alert('Please upload a file first');
            }
        });

        function extractSummaryFromFile(file) {
            var formData = new FormData();
            formData.append('file', file);
            formData.append('language', 'en'); // Can be made dynamic later
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $('#summary-extraction-status').show();
            $('#extract-summary-btn').prop('disabled', true);

            $.ajax({
                url: '{{ route("ai.summarise.file") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#summary-extraction-status').html(
                        '<div class="alert alert-success"><i class="fa fa-check"></i> Summary extracted successfully!</div>'
                    );
                    
                    // Extract plain text from HTML response
                    var tempDiv = $('<div>').html(response.content);
                    var plainText = tempDiv.text() || tempDiv.html();
                    
                    // Set description if empty or ask user
                    var currentDesc = $('#summernote').val();
                    if (!currentDesc || currentDesc.trim().length < 50) {
                        $('#summernote').summernote('code', response.content);
                    } else {
                        if (confirm('Replace existing description with auto-extracted summary?')) {
                            $('#summernote').summernote('code', response.content);
                        }
                    }

                    setTimeout(function() {
                        $('#summary-extraction-status').fadeOut();
                    }, 3000);
                    $('#extract-sumblog-btn').prop('disabled', false);
                },
                error: function(xhr) {
                    var errorMsg = 'Error extracting summary. Please try again or enter description manually.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#summary-extraction-status').html(
                        '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Radio' + errorMsg + '</div>'
                    );
                    $('#extract-summary-btn').prop('disabled', false);
                }
            });
        }

        $('.theme').on('change', function(e) {
            var themes = @json($themes);
            const theme = themes.find((item) => item.id === parseFloat(e.target.value));
            if (!theme) return;
            
            theme_subs = theme.subthemes;
            $('.subtheme').html('');
            theme_subs.forEach(item => {
                $('.subtheme').append(
                    `<option value="${item.id}">${item.description}</option>`);
            });
        });

        // Smart Wizard
        $('#smartwizard').smartWizard({
            autoAdjustHeight: false,
            selected: 0,
            theme: 'dots',
            toolbarSettings: {
                toolbarPosition: 'both',
            },
        });

        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection, stepPosition) {
            $("#prev-btn").removeClass('disabled');
            $("#next-btn").removeClassvideo('disabled');
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
            $('#smartwizard').smartWizard("prev");
            return true;
        });

        $("#next-btn").on("click", function() {
            $('#smartwizard').smartWizard("next");
            return true;
        });
    });
</script>

