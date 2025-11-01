@php
    // Handle both $row and $publication (admin form uses $publication, user form uses $row)
    // Normalize to $publication for consistency
    $publication = $row ?? $publication ?? null;
    
    // Debug: Log tag_ids if publication exists
    if ($publication && isset($publication->id)) {
        \Log::info('Wizard loading tags', [
            'publication_id' => $publication->id,
            'tag_ids' => $publication->tag_ids ?? 'NOT_SET',
            'tags_relationship_count' => $publication->tags ? $publication->tags->count() : 0
        ]);
    }
    
    if (@$publication && isset($publication->id)) {
        // Get raw cover value (before accessor processes it)
        $raw_cover = $publication->getRawOriginal('cover');
        $cover_is_external = $publication->cover_is_exteranl ?? false;
        
        // Debug logging
        \Log::info('Wizard loading cover image', [
            'publication_id' => $publication->id,
            'raw_cover' => $raw_cover,
            'cover_is_external' => $cover_is_external,
            'cover_accessor' => $publication->cover
        ]);
        
        if (!empty($raw_cover)) {
            if ($cover_is_external && filter_var($raw_cover, FILTER_VALIDATE_URL)) {
                // External URL - use as is
                $image_link = $raw_cover;
            } else {
                // Local file - use storage_link helper
                $image_link = storage_link('uploads/publications/' . $raw_cover);
            }
        } else {
            // No cover image - use placeholder
            $image_link = asset('assets/images/placeholder.png');
        }
    } else {
        // New publication or no publication
        $image_link = asset('assets/images/placeholder.png');
    }

    // Get required fields configuration from settings
    $requiredFields = [];
    try {
        $settingsRequiredFields = settings()->publication_required_fields ?? '{}';
        $requiredFields = json_decode($settingsRequiredFields, true);
        if (!is_array($requiredFields) || empty($requiredFields)) {
            $requiredFields = [
                'title' => true,
                'description' => true,
                'associated_authors' => true,
                'tags' => true,
                'theme' => true,
                'sub_theme' => true,
                'data_category_id' => true,
            ];
        }
    } catch (\Exception $e) {
        $requiredFields = [
            'title' => true,
            'description' => true,
            'associated_authors' => true,
            'tags' => true,
            'theme' => true,
            'sub_theme' => true,
            'data_category_id' => true,
        ];
    }

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
        /* Ensure consistent horizontal padding on all steps so first controls aren't clipped */
        #smartwizard .tab-content .tab-pane { padding-left: 16px; padding-right: 16px; }
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
        <div id="step-1" class="tab-pane active" role="tabpanel" aria-labelledby="step-1">
            <!-- Required Fields Reminder -->
            <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #119A48; padding: 12px 16px; border-radius: 4px;">
                <i class="fa fa-info-circle mr-2" style="color: #119A48;"></i>
                <strong>Please Note:</strong> All fields marked with a <span class="text-danger">*</span> (red asterisk) are <strong>required</strong>. Please ensure you fill in all required fields before proceeding to Step 2.
                </div>

            <div class="row" style="margin-top:12px;">
                <div class="col-lg-12 mb-2" style="margin-left:12px !important;">
                    <div class="d-flex flex-wrap align-items-center" style="gap:20px;">
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
                        <div class="w-100"></div>
                        <div class="container-fluid mt-1" style="line-height:1.3; padding-left:0; padding-right:0;">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block"><strong>Attachment</strong>: Upload a file (PDF, Word, images, etc.). We’ll extract a summary from PDFs where possible.</small>
                                    <small class="text-muted d-block"><strong>External Link</strong>: Provide a URL to content hosted elsewhere.</small>
                                    <small class="text-muted d-block"><strong>Embedded On Page</strong>: Display the resource directly on the page (use for embeddable content like videos or interactive views).</small>
                                </div>
                                <div class="col-md-6">
                                    @if (is_admin())
                                    <small class="text-muted d-block"><strong>Default in Category</strong>: Feature this resource as the primary/default item in its category so it appears prominently in listings.</small>
                                    <small class="text-muted d-block"><strong>Admin Only Access</strong>: Limit visibility to administrators only.</small>
                                    <small class="text-muted d-block"><strong>Shows Disclaimer</strong>: Include the standard disclaimer on the resource details page.</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Title & URL moved from Step 2 -->
                <div class="col-md-12 mt-2">
                    <h3>What is the title of the resource you want to publish?</h3>
                    <div class="mb-3">
                        <label class="form-label" for="title">Resource Title
                            @if(($requiredFields['title'] ?? true) == true)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <input placeholder="Resource Title" class="form-control newform" id="title" name="title"
                            value="{{ @$publication->title ?? old('title') }}" {{ ($requiredFields['title'] ?? true) ? 'required=""' : '' }}>
                    </div>
                </div>

                <div class="col-md-12 url_wrapper">
                    <div class="mb-3">
                        <label class="form-label" for="publication">Publication URL Link <span class="text-danger link-required-asterisk" style="display: none;">*</span></label>
                        <input type="text" placeholder="URL Link" class="form-control url" id="publication"
                            name="link" value="{{ @$row->publication ?? old('publication') }}">
                        <small class="text-muted link-required-text" style="display: none;">Required when selecting External Link</small>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="year_published">Year of Publication
                        @if(($requiredFields['year_published'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <select class="form-control select2" name="year_published" id="year_published" {{ ($requiredFields['year_published'] ?? false) ? 'required' : '' }}>
                        @php $currentYear = intval(date('Y')); $start = $currentYear; $end = $currentYear - 20; @endphp
                        @for($y = $start; $y >= $end; $y--)
                            <option value="{{ $y }}" {{ ( (old('year_published') == $y) || (@$row->year_published == $y) || (!@$row->year_published && !old('year_published') && $y == $currentYear) ) ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    <small class="text-muted">Select the year this resource was published.</small>
                </div>

                <div class="col-md-6 mb-2">
                    <label>Category
                        @if(($requiredFields['data_category_id'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    @include('partials.datarecords.categories_dropdown', [
                        'field' => 'data_category_id',
                        'required' => ($requiredFields['data_category_id'] ?? true) ? 'required' : '',
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
                    <label class="form-label" for="publication">Thematic Area
                        @if(($requiredFields['theme'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                        @include('partials.publications.theme_dropdown', [
                            'field' => 'theme',
                            'class' => 'select2 theme',
                        'required' => ($requiredFields['theme'] ?? true) ? 'required' : '',
                        'selected' => @$row->sub_theme->thematic_area_id ? $row->sub_theme->thematic_area_id : old('theme'),
                        ])
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Sub Theme
                        @if(($requiredFields['sub_theme'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                        @include('partials.publications.subtheme_dropdown', [
                            'field' => 'sub_theme',
                            'class' => 'select2 subtheme',
                        'required' => ($requiredFields['sub_theme'] ?? true) ? 'required' : '',
                            'selected' => @$row->sub_thematic_area_id ? $row->sub_thematic_area_id : '',
                        ])
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Region</label>
                        @include('partials.regions.dropdown', [
                            'field' => 'rccs[]',
                            'class' => 'rcc select2',
                            'selected' => @$row->region_ids ?? (isset($row->geographical_coverage_id) ? [$row->geographical_coverage_id] : null),
                            'multiple' => 'multiple',
                            'allfield' => 'All',
                        ])
                        <small class="text-muted d-block mt-1">Tip: If this resource applies to every member state, choose <strong>All</strong>.</small>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Member States <span class="text-danger">*</span></label>
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
                    <label class="form-label" for="publication">Corporate Source or Member State
                        @if(($requiredFields['author'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                        <small class="text-muted">(If your source is missing, please contact the system admin)</small>
                    </label>
                            @include('partials.authors.dropdown', [
                                'field' => 'author',
                        'required' => ($requiredFields['author'] ?? false) ? 'required' : '',
                                'selected' => @$row->author_id ?? null,
                        'allfield' => 'Select Corporate Source or Member State',
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
            </div>
            <h3 class="mb-2" style="font-weight:600;">Attachments & Additional Details</h3>
            <div class="row" style="min-height: 0;">


                <!-- Attachments LEFT column -->
                <div class="col-md-6 attachment">
                    <div class="mb-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="publication">Publication Attachments</label>

                        @if (@$row && @$row->has_attachments)
                            <div class="mb-2">
                                <small class="text-muted d-block mb-1">Existing Files (check to remove)</small>
                                <ul class="list-group">
                                    @foreach ($row->attachments as $pub_file)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="{{ $pub_file->file }}" target="_blank"><i class="fa fa-paperclip text-muted"></i> {{ $pub_file->description ?? 'Attachment' }}</a>
                                            <label class="mb-0"><input type="checkbox" name="remove_attachments[]" value="{{ $pub_file->id }}"> Remove</label>
                                        </li>
                            @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="custom-file">
                            <input type="file" class="form-control" name="files" id="attachments" multiple
                                   accept="image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,audio/*,video/*">
                            <label class="form-label mt-2" for="attachments">
                                <small class="text-muted">Click to select files (you can add more files by clicking again)</small>
                            </label>
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

                <!-- Cover RIGHT column -->
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
            
            <!-- AI Description Loader -->
            <div id="ai-description-loader" style="display: none; margin-top: 10px; margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #119A48; border-radius: 4px;">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm text-success mr-2" role="status" style="width: 1.5rem; height: 1.5rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div>
                        <strong style="color: #119A48;"><i class="fa fa-robot mr-1"></i>AI is generating description...</strong>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Please wait while we extract the description from your uploaded document.</p>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="summernote">Abstract/ Publication Description
                            @if(($requiredFields['description'] ?? true) == true)
                                <span class="text-danger">*</span>
                            @endif
                        </label>
                        <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" {{ ($requiredFields['description'] ?? true) ? 'required=""' : '' }}>{!! $row->description ?? old('description') !!}</textarea>
                        @php
                            $minWords = settings()->publication_min_words ?? 150;
                            $minChars = $minWords * 5;
                        @endphp
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Minimum {{ $minWords }} words required (approximately {{ $minChars }} characters). Please provide a detailed description of your publication.</small>
                    </div>
                </div>
            </div>

            <h3 class="mb-2 mt-3" style="font-weight:600;">Publication Metadata</h3>
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label class="form-label" for="associated_authors">Associated Authors
                        @if(($requiredFields['associated_authors'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text" class="form-control" name="associated_authors" id="associated_authors"
                           placeholder="Associated Authors" {{ ($requiredFields['associated_authors'] ?? true) ? 'required' : '' }}
                           value="{{ @$publication->associated_authors ?? old('associated_authors') }}">
                    <small class="text-muted">List the individuals or organisations who authored or co-authored this publication or any attached documents. Separate multiple names with commas.</small>
                </div>
                
                <div class="col-md-12 mb-2">
                    <label class="form-label" for="author_affiliation">Author Affiliation/Institution <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="author_affiliation" id="author_affiliation"
                           placeholder="Author Affiliation/Institution" required
                           value="{{ @$publication->author_affiliation ?? old('author_affiliation') }}">
                    <small class="text-muted">Enter the institution, organization, or affiliation of the authors listed above.</small>
                </div>
                
                <div class="col-md-12 mb-2">
                    <label class="form-label" for="tags">Associated Tags/Health Topics
                        @if(($requiredFields['tags'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    @php
                        // Ensure we get tag_ids correctly
                        $selectedTags = [];
                        if ($publication && isset($publication->id)) {
                            try {
                                $selectedTags = $publication->tag_ids ?? [];
                                // Fallback: query directly if accessor returns empty
                                if (empty($selectedTags)) {
                                    $selectedTags = \Illuminate\Support\Facades\DB::table('publication_tags')
                                        ->where('publication_id', $publication->id)
                                        ->pluck('tag_id')
                                        ->toArray();
                                }
                            } catch (\Exception $e) {
                                \Log::error('Error loading tags in wizard: ' . $e->getMessage());
                                $selectedTags = [];
                            }
                        }
                    @endphp
                    @include('partials.tags.dropdown', [
                        'field' => 'tags[]',
                        'selected' => $selectedTags,
                        'required' => ($requiredFields['tags'] ?? true) ? 'required' : '',
                    ])
                    <small class="text-muted">Select relevant tags to help categorize this publication. At least one tag is required for content indexing.</small>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label class="form-label" for="doi">DOI (Digital Object Identifier)
                        @if(($requiredFields['doi'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text" class="form-control" name="doi" id="doi" 
                           placeholder="10.xxxx/xxxxx" 
                           {{ ($requiredFields['doi'] ?? false) ? 'required' : '' }}
                           value="{{ @$row->doi ?? old('doi') }}">
                    <small class="text-muted">
                        @if(!($requiredFields['doi'] ?? false))
                            Optional -
                        @endif
                        Format: 10.xxxx/xxxxx
                    </small>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label class="form-label" for="issn">ISSN (International Standard Serial Number)
                        @if(($requiredFields['issn'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text" class="form-control" name="issn" id="issn" 
                           placeholder="0000-0000" 
                           {{ ($requiredFields['issn'] ?? false) ? 'required' : '' }}
                           value="{{ @$row->issn ?? old('issn') }}">
                    <small class="text-muted">
                        @if(!($requiredFields['issn'] ?? false))
                            Optional -
                        @endif
                        Format: XXXX-XXXX
                    </small>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label class="form-label" for="isbn">ISBN (International Standard Book Number)
                        @if(($requiredFields['isbn'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <input type="text" class="form-control" name="isbn" id="isbn" 
                           placeholder="978-0-xxxxx-xxx-x" 
                           {{ ($requiredFields['isbn'] ?? false) ? 'required' : '' }}
                           value="{{ @$row->isbn ?? old('isbn') }}">
                    <small class="text-muted">
                        @if(!($requiredFields['isbn'] ?? false))
                            Optional -
                        @endif
                        Format: 978-0-xxxxx-xxx-x
                    </small>
                </div>
                
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="license_id">License/Copyright Information
                        @if(($requiredFields['license_id'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <select class="form-control select2" name="license_id" id="license_id" {{ ($requiredFields['license_id'] ?? false) ? 'required' : '' }}>
                        <option value="">Select License</option>
                        @php
                            $licenses = \App\Models\License::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
                            // Find Open Access license ID for default selection
                            $openAccessLicense = $licenses->firstWhere('short_name', 'Open Access');
                            $openAccessId = $openAccessLicense ? $openAccessLicense->id : null;
                            // Determine if Open Access should be selected (default when no license is set)
                            $shouldSelectOpenAccess = !@$row->license_id && !old('license_id') && $openAccessId;
                        @endphp
                        @foreach($licenses as $license)
                            <option value="{{ $license->id }}" {{ (@$row->license_id == $license->id || old('license_id') == $license->id || ($shouldSelectOpenAccess && $license->id == $openAccessId)) ? 'selected' : '' }}>
                                {{ $license->name }}
                                @if($license->short_name ?? false)
                                    ({{ $license->short_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        @if(!($requiredFields['license_id'] ?? false))
                            Optional -
                        @endif
                        Select the license for this publication
                    </small>
                </div>
                
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="funder">Funder</label>
                    <input type="text" class="form-control" name="funder" id="funder" 
                           placeholder="Funding organization or agency" 
                           value="{{ @$row->funder ?? old('funder') }}">
                    <small class="text-muted">Optional - Organization or agency that funded this research</small>
                </div>
                
                <div class="col-md-12 mb-2">
                    <label class="form-label" for="copyright_info">Copyright Information
                        @if(($requiredFields['copyright_info'] ?? false) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                    <textarea class="form-control" name="copyright_info" id="copyright_info" rows="2" 
                              {{ ($requiredFields['copyright_info'] ?? false) ? 'required' : '' }}
                              placeholder="Additional copyright information">{{ @$row->copyright_info ?? old('copyright_info') }}</textarea>
                    <small class="text-muted">
                        @if(!($requiredFields['copyright_info'] ?? false))
                            Optional -
                        @endif
                        Additional copyright details
                    </small>
                </div>
            </div>

            <h3 class="mb-2 mt-3" style="font-weight:600;">Journal Information <small class="text-muted">(For Journal Articles only)</small></h3>
            <div class="row mb-2">
                <div class="col-md-12" style="margin-left: 10px;">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_journal_article" name="is_journal_article" value="1" 
                               {{ (@$row->journal_name || @$row->journal_volume || @$row->journal_issue || @$row->journal_pages) ? 'checked' : '' }}
                               onclick="document.getElementById('journal-fields').style.display = this.checked ? 'block' : 'none';">
                        <label class="form-check-label" for="is_journal_article">
                            <strong>This is a Journal Article</strong>
                        </label>
                        <small class="text-muted d-block mt-1">Check this box to show journal-specific fields (Volume, Issue, Pages)</small>
                    </div>
                </div>
            </div>
            <div class="row journal-fields" id="journal-fields" style="display: none;">
                <div style="display: flex; flex-wrap: wrap; gap: 15px; width: 100%;">
                    <div style="flex: 1 1 50%; min-width: 0;">
                        <label class="form-label" for="journal_name">Journal Name</label>
                        <input type="text" class="form-control" name="journal_name" id="journal_name" 
                               placeholder="Name of the journal" 
                               value="{{ @$row->journal_name ?? old('journal_name') }}">
                    </div>
                    
                    <div style="flex: 1 1 25%; min-width: 0;">
                        <label class="form-label" for="journal_volume">Volume</label>
                        <input type="text" class="form-control" name="journal_volume" id="journal_volume" 
                               placeholder="Vol" 
                               value="{{ @$row->journal_volume ?? old('journal_volume') }}">
                    </div>
                    
                    <div style="flex: 1 1 12.5%; min-width: 0;">
                        <label class="form-label" for="journal_issue">Issue</label>
                        <input type="text" class="form-control" name="journal_issue" id="journal_issue" 
                               placeholder="Issue" 
                               value="{{ @$row->journal_issue ?? old('journal_issue') }}">
                    </div>
                    
                    <div style="flex: 1 1 12.5%; min-width: 0;">
                        <label class="form-label" for="journal_pages">Pages</label>
                        <input type="text" class="form-control" name="journal_pages" id="journal_pages" 
                               placeholder="e.g., 123-145" 
                               value="{{ @$row->journal_pages ?? old('journal_pages') }}">
                        <small class="text-muted" style="font-size: 0.75rem;">Page range</small>
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

@include('partials.search.fields_js')

<script type="text/javascript">
    $(document).ready(function() {

        $('.theme').on('change', function(e) {
            var themeValue = $(this).val();
            if (!themeValue) {
                $('.subtheme').html('<option value="">Select Sub-Theme</option>');
                return;
            }

            var themes = @json($themes ?? []);
            if (!themes || !Array.isArray(themes)) {
                console.error('Themes data not available');
                return;
            }

            const theme = themes.find((item) => item.id === parseFloat(themeValue));
            if (!theme || !theme.subthemes) {
                console.error('Theme or subthemes not found');
                $('.subtheme').html('<option value="">Select Sub-Theme</option>');
                return;
            }

            var theme_subs = theme.subthemes;
            $('.subtheme').html('<option value="">Select Sub-Theme</option>');

            if (Array.isArray(theme_subs)) {
                theme_subs.forEach(function(item) {
                $('.subtheme').append(
                        '<option value="' + item.id + '">' + item.description + '</option>'
                    );
                });
            }

            // Re-initialize Select2 after updating options
            if (typeof $.fn.select2 !== 'undefined') {
                $('.subtheme').trigger('change.select2');
            }
        });

        // File input with ability to add multiple files incrementally
        var $input = $('#attachments');
        var existingFiles = []; // Store existing files
        
        if ($input.length) {
            // Clear any existing preview icons from attachment_js.blade.php
            $input.closest('.mb-2').find('.preview').empty();
            
            // Load existing files on page load (for edit mode)
            if ($input[0].files && $input[0].files.length > 0) {
                existingFiles = Array.from($input[0].files);
            }
            
            $input.on('change', function() {
                var newFiles = Array.from(this.files);
                
                // Merge new files with existing files (avoid duplicates)
                var mergedFiles = [...existingFiles];
                newFiles.forEach(function(newFile) {
                    // Check if file already exists (by name and size)
                    var exists = mergedFiles.some(function(existingFile) {
                        return existingFile.name === newFile.name && existingFile.size === newFile.size;
                    });
                    if (!exists) {
                        mergedFiles.push(newFile);
                    }
                });
                
                // Update existing files list
                existingFiles = mergedFiles;
                
                // Update the input with merged files using DataTransfer
                const dT = new DataTransfer();
                mergedFiles.forEach(function(file) {
                    dT.items.add(file);
                });
                this.files = dT.files;
                
                // Clear any large icon previews and update with our custom preview
                var previewDiv = $input.closest('.mb-2').find('.preview');
                previewDiv.empty(); // Clear any existing preview content
                updateFilePreview();
            });
            
            // Function to update file preview
            function updateFilePreview() {
                var files = $input[0].files;
                var previewDiv = $input.closest('.mb-2').find('.preview');
                if (files && files.length > 0) {
                    var fileList = '<div class="mt-2"><small class="text-success"><strong>Selected files (' + files.length + '):</strong></small><ul class="list-unstyled mt-1">';
                    Array.from(files).forEach(function(file, index) {
                        fileList += '<li class="mb-1">';
                        fileList += '<i class="fa fa-file text-muted"></i> ';
                        fileList += '<span>' + file.name + '</span> ';
                        fileList += '<small class="text-muted">(' + (file.size / 1024).toFixed(2) + ' KB)</small> ';
                        fileList += '<button type="button" class="btn btn-sm btn-link text-danger p-0 ml-2" onclick="removeFile(' + index + ')"><i class="fa fa-times"></i></button>';
                        fileList += '</li>';
                    });
                    fileList += '</ul>';
                    fileList += '<small class="text-muted"><i class="fa fa-info-circle"></i> Click "Choose Files" again to add more files</small>';
                    fileList += '</div>';
                    previewDiv.html(fileList);
                } else {
                    previewDiv.html('');
                }
            }
            
            // Function to remove a file (accessible globally)
            window.removeFile = function(index) {
                var files = Array.from($input[0].files);
                files.splice(index, 1);
                
                // Update existing files list
                existingFiles = files;
                
                // Update the input with remaining files
                const dT = new DataTransfer();
                files.forEach(function(file) {
                    dT.items.add(file);
                });
                $input[0].files = dT.files;
                
                // Update preview
                updateFilePreview();
            };
            
            // Initial preview update
            if (existingFiles.length > 0) {
                updateFilePreview();
            }
        }

        // AI summary extraction from first selected file
        function extractSummaryFromFile(file){
            // Show loader
            $('#ai-description-loader').slideDown(300);
            
            var formData = new FormData();
            formData.append('file', file);
            formData.append('language', 'en');
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ route("ai.summarise.file") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){
                    // Hide loader
                    $('#ai-description-loader').slideUp(300);
                    
                    if(!resp || !resp.content) return;
                    if (!$('#summernote').next('.note-editor').length && typeof $.fn.summernote === 'function') {
                        $('#summernote').summernote({ height: 300 });
                    }
                    var current = $('#summernote').val();
                    if (!current || current.trim().length < 50) {
                        $('#summernote').summernote('code', resp.content);
                    } else if (confirm('Replace existing description with AI-extracted summary?')) {
                        $('#summernote').summernote('code', resp.content);
                    }
                },
                error: function(xhr, status, error){
                    // Hide loader on error
                    $('#ai-description-loader').slideUp(300);
                    console.error('AI extraction error:', error);
                }
            });
        }

        $('#attachments').on('change', function(){
            var f = this.files && this.files.length ? this.files[0] : null;
            if(!f) return;
            extractSummaryFromFile(f);
        });

        // Show/hide journal fields based on checkbox
        function toggleJournalFields() {
            var checkbox = $('#is_journal_article');
            var journalFields = $('#journal-fields');
            
            if (!checkbox.length || !journalFields.length) {
                console.log('Journal fields or checkbox not found');
                return;
            }
            
            var isChecked = checkbox.is(':checked');
            
            // Also check if journal fields already have values (for editing)
            var hasJournalData = $('#journal_name').val() || $('#journal_volume').val() || $('#journal_issue').val() || $('#journal_pages').val();
            
            console.log('Toggle journal fields:', { isChecked: isChecked, hasJournalData: hasJournalData });
            
            if (isChecked || hasJournalData) {
                // Remove inline style and show the fields
                journalFields.removeAttr('style').show();
                // Auto-check the checkbox if there's existing data
                if (hasJournalData && !isChecked) {
                    checkbox.prop('checked', true);
                }
            } else {
                journalFields.hide().attr('style', 'display: none;');
            }
        }
        
        // Check on page load (with delay to ensure select2 is initialized)
        setTimeout(function() {
            toggleJournalFields();
        }, 500);
        
        // Check when checkbox changes - use both change and click events for better compatibility
        $(document).on('change', '#is_journal_article', function() {
            console.log('Checkbox changed:', $(this).is(':checked'));
            toggleJournalFields();
        });
        
        $(document).on('click', '#is_journal_article', function() {
            setTimeout(function() {
                toggleJournalFields();
            }, 10);
        });
        
        // Also check when category changes (in case category name contains "journal" or "article")
        $('#data_category_id').on('change', function() {
            var categoryText = $(this).find('option:selected').text().toLowerCase();
            var isJournalArticle = categoryText.includes('journal') || categoryText.includes('article');
            
            if (isJournalArticle && !$('#is_journal_article').is(':checked')) {
                $('#is_journal_article').prop('checked', true);
                toggleJournalFields();
            }
        });

        $('input[name="upload_type"]').on('change', function() {
            var uploadType = $(this).val();
            if (uploadType == 'link') {
                $('.link-required-asterisk').show();
                $('.link-required-text').show();
                $('#publication').prop('required', true);
              } else {
                $('.link-required-asterisk').hide();
                $('.link-required-text').hide();
                $('#publication').prop('required', false);
            }
        });
        
        // Check on page load
        if ($('input[name="upload_type"]:checked').val() == 'link') {
            $('.link-required-asterisk').show();
            $('.link-required-text').show();
            $('#publication').prop('required', true);
        }

        // Smart Wizard
        $('#smartwizard').smartWizard({
            autoAdjustHeight: false,
            selected: 0,
            theme: 'dots',
            toolbarSettings: {
                toolbarPosition: 'both', // both bottom
            },
        });

        // Clear error message when tags are selected
        $('select[name="tags[]"]').on('change', function() {
            if ($(this).val() && $(this).val().length > 0) {
                $(this).closest('.form-group').removeClass('has-error');
                $(this).closest('.mb-2').find('.text-danger').remove();
            }
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
            // Validate required fields before proceeding
            var isValid = true;
            var errorMessage = '';
            
            // Check tags selection
            var tagsSelect = $('select[name="tags[]"]');
            if (tagsSelect.length && (!tagsSelect.val() || tagsSelect.val().length === 0)) {
                isValid = false;
                errorMessage = 'Please select at least one tag/health topic before proceeding.';
                tagsSelect.closest('.form-group').addClass('has-error');
                if (tagsSelect.closest('.mb-2').find('.text-danger').length === 0) {
                    tagsSelect.closest('.mb-2').append('<small class="text-danger d-block mt-1">' + errorMessage + '</small>');
                }
            } else {
                tagsSelect.closest('.form-group').removeClass('has-error');
                tagsSelect.closest('.mb-2').find('.text-danger').remove();
            }
            
            if (!isValid) {
                alert(errorMessage || 'Please fill in all required fields marked with a red asterisk (*) before proceeding.');
                return false;
            }

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
            .on('form:submit', function(e) {
                // Additional validation for tags before form submission
                var tagsSelect = $('select[name="tags[]"]');
                if (tagsSelect.length && (!tagsSelect.val() || tagsSelect.val().length === 0)) {
                    alert('Please select at least one tag/health topic to help categorize your publication.');
                    return false;
                }
                
                // Intercept form submission to manually add files
                var fileInput = $('#attachments');
                if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                    console.log('Files detected before submission:', fileInput[0].files.length);
                    
                    // Create FormData from form
                    var form = document.getElementById('publication_form');
                    var formData = new FormData(form);
                    
                    // Clear existing files from FormData and add our files manually
                    formData.delete('files'); // Remove any existing files entry
                    formData.delete('files[]'); // Remove any existing files[] entry
                    
                    // Add each file individually
                    Array.from(fileInput[0].files).forEach(function(file, index) {
                        formData.append('files[]', file);
                        console.log('Added file to FormData:', file.name, '(' + (file.size / 1024).toFixed(2) + ' KB)');
                    });
                    
                    // Prevent default form submission
                    e.preventDefault();
                    
                    // Show loading indicator
                    var submitBtn = form.querySelector('button[type="submit"]');
                    var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
                    }
                    
                    // Submit via AJAX with FormData
                    $.ajax({
                        url: form.action,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            console.log('Form submitted successfully:', response);
                            
                            // Handle response
                            if (response.status === 200 && response.alert_class === 'success') {
                                // Show success message
                                if (response.message) {
                                    alert(response.message);
                                }
                                // Redirect to publications page
                                window.location.href = '{{ route("account.publications") }}';
                            } else {
                                // Show error message
                                alert(response.message || 'An error occurred. Please try again.');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                    submitBtn.innerHTML = originalBtnText;
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Form submission error:', xhr);
                            var errorMsg = 'An error occurred while submitting the form.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    var errorResponse = JSON.parse(xhr.responseText);
                                    if (errorResponse.message) {
                                        errorMsg = errorResponse.message;
                                    }
                                } catch(e) {
                                    // Use default error message
                                }
                            }
                            alert(errorMsg);
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnText;
                            }
                        }
                    });
                    
                    return false; // Prevent default form submission
                } else {
                    console.log('No files attached to input - submitting normally');
                    // No files, submit normally
                    return true;
                }
            });
    });
    
    // Add direct form submit handler like admin form (more reliable than Parsley)
    $(document).ready(function() {
        var form = $('#publication_form');
        if (form.length === 0) {
            // Fallback: try alternate form selector
            form = $('form.publications');
        }
        
        if (form.length > 0) {
            form.off('submit').on('submit', function(e) {
                e.preventDefault();
                
                var formEl = $(this);
                var formData = new FormData(formEl[0]);
                
                // Manually add files if they exist (handles DataTransfer-set files)
                var fileInput = $('#attachments');
                if (!fileInput.length || !fileInput[0].files || fileInput[0].files.length === 0) {
                    // Try alternative selector
                    fileInput = $('input[name="files"]');
                }
                
                if (fileInput.length && fileInput[0].files && fileInput[0].files.length > 0) {
                    console.log('Frontend form: Files detected before submission:', fileInput[0].files.length);
                    
                    // Clear existing files from FormData and add our files manually
                    formData.delete('files'); // Remove any existing files entry
                    formData.delete('files[]'); // Remove any existing files[] entry
                    
                    // Add each file individually
                    Array.from(fileInput[0].files).forEach(function(file, index) {
                        formData.append('files[]', file);
                        console.log('Frontend form: Added file to FormData:', file.name, '(' + (file.size / 1024).toFixed(2) + ' KB)');
                    });
                } else {
                    console.log('Frontend form: No files detected in input');
                }
                
                var url = formEl.attr('action');
                
                // Show loading indicator
                var submitBtn = formEl.find('button[type="submit"]');
                var originalBtnText = submitBtn.length ? submitBtn.html() : '';
                if (submitBtn.length) {
                    submitBtn.prop('disabled', true);
                    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
                }
                
                $.ajax({
                    url: url,
                    type: 'post',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        console.log('Frontend form: Submission response:', response);
                        
                        // Check for success using alert_class or status
                        var isSuccess = (response.alert_class === 'success' || response.status === 'success');
                        
                        if (isSuccess) {
                            // Show success message using LobiBox
                            var successMsg = response.message || 'Publication submitted successfully!';
                            
                            if (typeof Lobibox !== 'undefined') {
                                Lobibox.notify('success', {
                                    size: 'mini',
                                    sound: false,
                                    delay: 3000,
                                    title: 'Success',
                                    pauseDelayOnHover: true,
                                    position: 'top right',
                                    msg: successMsg,
                                    callback: function() {
                                        // Redirect after notification closes
                                        window.location.href = '{{ route("account.publications") }}';
                                    }
                                });
                                // Also redirect after delay in case callback doesn't fire
                                setTimeout(function(){
                                    window.location.href = '{{ route("account.publications") }}';
                                }, 3000);
                            } else {
                                // Fallback to alert if LobiBox not loaded
                                alert(successMsg);
                                setTimeout(function(){
                                    window.location.href = '{{ route("account.publications") }}';
                                }, 2000);
                            }
                        } else {
                            // Show error message
                            var errorMsg = response.message || 'An error occurred. Please try again.';
                            if (typeof Lobibox !== 'undefined') {
                                Lobibox.notify('error', {
                                    size: 'mini',
                                    sound: false,
                                    delay: 5000,
                                    title: 'Error',
                                    pauseDelayOnHover: true,
                                    position: 'top right',
                                    msg: errorMsg
                                });
                            } else {
                                alert(errorMsg);
                            }
                            if (submitBtn.length) {
                                submitBtn.prop('disabled', false);
                                submitBtn.html(originalBtnText);
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Frontend form submission error:', xhr);
                        
                        var errorMsg = 'An error occurred while submitting the form.';
                        var errorMessages = [];
                        
                        // Handle validation errors (422 status)
                        if (xhr.status === 422 && xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                // Laravel validation errors
                                var errors = xhr.responseJSON.errors;
                                for (var field in errors) {
                                    if (errors.hasOwnProperty(field)) {
                                        if (Array.isArray(errors[field])) {
                                            errorMessages = errorMessages.concat(errors[field]);
                                        } else {
                                            errorMessages.push(errors[field]);
                                        }
                                    }
                                }
                                if (errorMessages.length > 0) {
                                    errorMsg = errorMessages.join('<br>');
                                }
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                if (errorResponse.message) {
                                    errorMsg = errorResponse.message;
                                } else if (errorResponse.errors) {
                                    var errors = errorResponse.errors;
                                    for (var field in errors) {
                                        if (errors.hasOwnProperty(field)) {
                                            if (Array.isArray(errors[field])) {
                                                errorMessages = errorMessages.concat(errors[field]);
                                            } else {
                                                errorMessages.push(errors[field]);
                                            }
                                        }
                                    }
                                    if (errorMessages.length > 0) {
                                        errorMsg = errorMessages.join('<br>');
                                    }
                                }
                            } catch(e) {
                                // Use default error message
                            }
                        }
                        
                        // Display error using LobiBox
                        if (typeof Lobibox !== 'undefined') {
                            Lobibox.notify('error', {
                                size: 'normal',
                                sound: false,
                                delay: 6000,
                                title: 'Validation Error',
                                pauseDelayOnHover: true,
                                position: 'top right',
                                msg: errorMsg
                            });
                        } else {
                            alert(errorMsg);
                        }
                        
                        // Re-enable submit button
                        if (submitBtn.length) {
                            submitBtn.prop('disabled', false);
                            submitBtn.html(originalBtnText);
                        }
                    }
                });
                
                return false;
            });
        }
    });
</script>
