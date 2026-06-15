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

    $publicationMinWords = (int) (settings()->publication_min_words ?? 150);
    $publicationMinChars = $publicationMinWords * 5;
    $adminMustSelectAuthor = is_admin() && ! optional(current_user())->author_id;
    $publicationLanguageOptions = \App\Models\SiteLanguage::selectorMap();
    $defaultPublicationLanguage = old(
        'publication_language',
        optional($publication)->publication_language
            ?? optional(auth()->user())->langauge
            ?? (settings()->language ?? 'en')
    );

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
        /* Step 1: wide + narrow field pairs (80% / 20%) */
        #smartwizard .wizard-inline-80-20 {
            display: grid;
            grid-template-columns: minmax(0, 4fr) minmax(0, 1fr);
            gap: 12px 16px;
            align-items: start;
        }
        /* Equal-width pairs (e.g. Category / Sub Category) */
        #smartwizard .wizard-inline-50-50 {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 12px 16px;
            align-items: start;
        }
        #smartwizard .wizard-field-wrap.has-error .select2-container .select2-selection {
            border-color: #dc3545 !important;
        }
        #smartwizard .wizard-step-error-summary {
            display: none;
        }
        #smartwizard .wizard-step-error-summary.is-visible {
            display: block;
        }
        #publication-processing-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 10050;
            background: rgba(15, 23, 42, 0.55);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #fff;
            text-align: center;
            padding: 1.5rem;
        }
        #publication-processing-overlay.is-visible {
            display: flex;
        }
        #publication-processing-overlay .processing-card {
            background: #fff;
            color: #0f172a;
            border-radius: 8px;
            padding: 1.5rem 2rem;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        #publication-processing-overlay .processing-spinner {
            font-size: 2rem;
            color: #119A48;
            margin-bottom: 0.75rem;
        }
        body.publication-form-busy {
            overflow: hidden;
        }
        #ai-description-loader.is-processing {
            display: block !important;
        }
        .attachment.wizard-ai-busy {
            position: relative;
        }
        .attachment.wizard-ai-busy::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.75);
            z-index: 5;
            border-radius: 4px;
        }
        @media (max-width: 767.98px) {
            #smartwizard .wizard-inline-80-20,
            #smartwizard .wizard-inline-50-50 {
                grid-template-columns: 1fr;
            }
        }
        /* SmartWizard v6 layout stability */
        #smartwizard.sw {
            width: 100%;
            max-width: 100%;
        }
        #smartwizard > .nav {
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        #smartwizard .tab-content {
            width: 100%;
            clear: both;
            overflow: visible;
        }
        #smartwizard .tab-content > .tab-pane {
            width: 100%;
        }
        #smartwizard .tab-content > .tab-pane > .row {
            margin-left: -12px;
            margin-right: -12px;
        }
        #smartwizard .sw-toolbar-elm {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            padding: 0.75rem 16px;
            margin-top: 0.5rem;
            clear: both;
            width: 100%;
            box-sizing: border-box;
        }
        #smartwizard .sw-toolbar-elm .sw-btn {
            min-width: 6rem;
        }
        /* Legacy progress bar duplicate below wizard */
        #smartwizard + .progress {
            display: none !important;
        }
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
            <div id="wizard-step-1-errors" class="alert alert-danger wizard-step-error-summary mb-3" role="alert"></div>

            <div class="alert mb-3" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #119A48; padding: 14px 18px; border-radius: 4px;">
                <div class="mb-3">
                    <strong class="d-block mb-1" style="color: #0f172a;"><i class="fa fa-file-pdf-o text-danger mr-1"></i>Documents</strong>
                    <p class="mb-0 text-muted small" style="line-height: 1.55;">
                        <strong class="text-body">PDF is the recommended format</strong> for reports, strategies, manuals, and similar materials. It gives the most reliable in-browser preview, downloads, accessibility, and text tools on the Hub. You can still upload Word, Excel, PowerPoint, or images when needed.
                    </p>
                </div>
                <div class="mb-0">
                    <strong class="d-block mb-1" style="color: #0f172a;"><i class="fa fa-youtube-play text-danger mr-1"></i>Videos (e.g. YouTube, Vimeo)</strong>
                    <ol class="mb-0 text-muted small pl-3" style="line-height: 1.55;">
                        <li class="mb-1">Choose <strong class="text-body">External Link</strong> — do not use Attachment for a video that is already hosted online.</li>
                        <li class="mb-1">Paste the <strong class="text-body">full video URL</strong> from your browser’s address bar, for example <code class="small">https://www.youtube.com/watch?v=…</code> or a <code class="small">youtu.be/…</code> short link.</li>
                        <li class="mb-1">Turn on <strong class="text-body">Embedded On Page</strong> so visitors can play the video on the resource page.</li>
                        <li class="mb-0">Add a clear title and description (and the rest of the metadata) so the resource is easy to find and understand.</li>
                    </ol>
                </div>
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
                                    <small class="text-muted d-block"><strong>Attachment</strong>: Upload a file from your device. <strong>PDF is recommended</strong> for documents; Word, Excel, PowerPoint, images, audio, and video are supported. Scripts and executables (e.g. .php, .js, .exe) are not allowed.</small>
                                    <small class="text-muted d-block"><strong>External Link</strong>: Paste a URL to content hosted elsewhere (e.g. a YouTube or Vimeo page). Required when you are not uploading a file.</small>
                                    <small class="text-muted d-block"><strong>Embedded On Page</strong>: Show the linked content inside the resource page — use this for videos and other embeddable content so users can view it without leaving the Hub.</small>
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
                        <input placeholder="Resource Title" class="form-control newform js-format-title-case" id="title" name="title"
                            value="{{ @$publication->title ?? old('title') }}" {{ ($requiredFields['title'] ?? true) ? 'required=""' : '' }}>
                        <small class="text-muted d-block mt-1">Title is formatted automatically to title case when you leave this field.</small>
                    </div>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="wizard-inline-80-20">
                        <div class="mb-0 url_wrapper">
                        <label class="form-label" for="publication">Publication URL Link <span class="text-danger link-required-asterisk" style="display: none;">*</span></label>
                        <input type="text" placeholder="URL Link" class="form-control url" id="publication"
                            name="link" value="{{ @$row->publication ?? old('publication') }}">
                        <small class="text-muted link-required-text" style="display: none;">Required when selecting External Link</small>
                    </div>
                        <div class="mb-0 wizard-field-year">
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
                            <small class="text-muted d-block">Year published</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication_language">Publication language <span class="text-danger">*</span></label>
                    <select class="form-control select2" name="publication_language" id="publication_language" required>
                        @foreach($publicationLanguageOptions as $code => $langRow)
                            <option value="{{ $code }}" {{ (string) $defaultPublicationLanguage === (string) $code ? 'selected' : '' }}>
                                {{ $langRow['name'] ?? $code }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block">Primary language of this resource. AI summary extraction uses this language by default.</small>
                </div>

                <div class="col-md-12 mb-2">
                    <div class="wizard-inline-50-50">
                        <div class="mb-0 wizard-field-wrap" data-wizard-field="data_category_id">
                            <label class="form-label">Category
                        @if(($requiredFields['data_category_id'] ?? true) == true)
                            <span class="text-danger">*</span>
                        @endif
                    </label>
                        @include('partials.datarecords.categories_dropdown', [
                            'field' => 'data_category_id',
                        'required' => ($requiredFields['data_category_id'] ?? true) ? 'required' : '',
                            'exclude_special' => true,
                                'selected' => old('data_category_id', optional($publication)->publication_catgory_id ?? ''),
                        ])
                    </div>
                        <div class="mb-0 wizard-field-wrap" data-wizard-field="category_id">
                            <label class="form-label" for="category_id">Sub Category <span class="text-danger">*</span></label>
                        @include('partials.publications.filecategory_dropdown', [
                            'field' => 'category_id',
                                'required' => 'required',
                                'selected' => old('category_id', optional($publication)->data_category_id ?? ''),
                        ])
                        </div>
                    </div>
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

                <div class="col-md-6 mb-2 wizard-field-wrap" data-wizard-field="rccs">
                    <label class="form-label" for="publication">Region <span class="text-danger">*</span></label>
                        @include('partials.regions.dropdown', [
                            'field' => 'rccs[]',
                            'required' => 'required',
                            'class' => 'rcc select2',
                            'selected' => @$row->region_ids ?? (isset($row->geographical_coverage_id) ? [$row->geographical_coverage_id] : (hub_owner_region_id() ? [hub_owner_region_id()] : null)),
                            'multiple' => 'multiple',
                            'allfield' => 'All',
                        ])
                        <small class="text-muted d-block mt-1">Tip: If this resource applies to every member state, choose <strong>All</strong>.</small>
                    </div>
                <div class="col-md-6 mb-2 wizard-field-wrap" data-wizard-field="countries">
                    <label class="form-label" for="publication">Member States <span class="text-danger">*</span></label>
                        @include('partials.countries.dropdown', [
                            'field' => 'countries[]',
                            'required' => 'required',
                            'class' => 'country select2',
                            'selected' => $row->country_ids ?? (hub_owner_country_id() ? [hub_owner_country_id()] : null),
                            'multiple' => 'multiple',
                            'all_option' => true,
                            'allfield' => 'All',
                        ])
                        <small class="text-muted d-block mt-1">After choosing region(s), pick <strong>All</strong> or select specific member states from those regions only.</small>
            </div>

            @include('partials.publications.public_availability_toggle', ['row' => $row ?? null])

            @if (is_admin())
                <div class="col-md-6 mb-2">
                    <label class="form-label" for="publication">Corporate Source or Member State
                        @if(($requiredFields['author'] ?? false) == true || $adminMustSelectAuthor)
                            <span class="text-danger">*</span>
                        @endif
                        <small class="text-muted">(If your source is missing, please contact the system admin)</small>
                    </label>
                            @include('partials.authors.dropdown', [
                                'field' => 'author',
                        'required' => (($requiredFields['author'] ?? false) || $adminMustSelectAuthor) ? 'required' : '',
                                'selected' => @$row->author_id ?? null,
                        'allfield' => 'Select Corporate Source or Member State',
                            ])
                        </div>
            @else
                {{-- Hidden field for non-admin users - Parsley will ignore it --}}
                <input type="hidden" name="author" value="" data-parsley-excluded="true">
            @endif
                    </div>
        </div>

        <div id="step-2" class="tab-pane" role="tabpanel" aria-labelledby="step-2">
            <div id="wizard-step-2-errors" class="alert alert-danger wizard-step-error-summary mb-3" role="alert"></div>
            <input type="hidden" name="id" id="id" class="newform" value="{{ @$row->id ?? old('id') }}">
            <h3 class="mb-2 mt-2" style="font-weight:600;">Attachments & Additional Details</h3>
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
                                            <a href="{{ $pub_file->file }}" target="_blank" title="{{ e($pub_file->original_filename ?? $pub_file->description ?? '') }}"><i class="fa fa-paperclip text-muted"></i> {{ Str::limit($pub_file->original_filename ?? $pub_file->description ?? 'Attachment', 90) }}</a>
                                            <label class="mb-0"><input type="checkbox" name="remove_attachments[]" value="{{ $pub_file->id }}"> Remove</label>
                                        </li>
                            @endforeach
                                </ul>
                </div>
                        @endif

                        <div class="custom-file">
                            <input type="file" class="form-control" name="files" id="attachments" multiple
                                   accept="{{ \App\Support\PublicationAttachmentSecurity::htmlAcceptAttribute() }}">
                            <label class="form-label mt-2" for="attachments">
                                <small class="text-muted">Click to select files (you can add more files by clicking again). Executable and script files are blocked for security.</small>
                            </label>
                            <div id="attachment-security-error" class="alert alert-danger mt-2 py-2 px-3 small" style="display:none;" role="alert"></div>
                        </div>
                        <div class="preview py-2" style="min-height: 24px;"></div>
            </div>

                    <div class="form-group mt-2 p-2" style="background:#ffffff;">
                        <label class="form-label" for="communities">Target Audience/Communities of Practice</label>
                        <!-- <a href="#" class="btn btn-sm btn-dark btn-outline mb-2"><i class="fa fa-plus"></i> Add Community Of Practice</a> -->
                        @include('partials.publications.publication_communities_dropdown', [
                            'field' => 'communities[]',
                            'selected' => @$row->communities ? $row->communities->pluck('id')->toArray() : [],
                            'show_hub_cop_options' => true,
                            'also_public_on_hub' => (isset($row) && ! empty($row->id)) ? (int) ($row->also_public_on_hub ?? 0) : 1,
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

            <div class="row" style="display: none;">
                <div class="col-md-6 justify-content-center video">
                <label class="form-label" for="publication">Video</label>
                <div class="mb-3">
                        <iframe width="450" height="260" class="vid" src=""></iframe>
                    </div>
                </div>
            </div>

            <h3 class="mb-2 mt-3" style="font-weight:600;">Publication Details</h3>

            <div class="mb-3 p-2" style="background:#ffffff; border:1px solid #e8edf2; border-radius:4px;" id="ai-summary-language-choice">
                <label class="form-label d-block mb-2">AI summary language</label>
                <div class="d-flex flex-wrap align-items-center" style="gap: 1rem;">
                    <label class="form-check-inline mb-0">
                        <input type="radio" class="form-check-input" name="ai_summary_language_mode" value="publication" checked>
                        Use publication language (<span id="ai-summary-pub-lang-label">{{ publication_language_label($defaultPublicationLanguage) }}</span>)
                    </label>
                    <label class="form-check-inline mb-0">
                        <input type="radio" class="form-check-input" name="ai_summary_language_mode" value="english">
                        Extract in English
                    </label>
                </div>
                <small class="text-muted d-block mt-1">When you upload an attachment, the AI draft description follows this choice. Default matches the publication language from Step 1.</small>
            </div>
            
            <!-- AI Description Loader -->
            <div id="ai-description-loader" style="display: none; margin-top: 10px; margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #119A48; border-radius: 4px;">
                <div class="d-flex align-items-center">
                    <div class="mr-3 text-center" style="min-width: 2.5rem;">
                        <i class="fa fa-spinner fa-spin processing-spinner" style="font-size: 1.75rem; color: #119A48;" aria-hidden="true"></i>
                        <span class="sr-only">Processing</span>
                    </div>
                    <div>
                        <strong style="color: #119A48;"><i class="fa fa-robot mr-1"></i>AI is extracting data from your document…</strong>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Please wait while we extract the description, authors, and affiliation. This is only a draft to help you publish faster—always review and edit before you submit.</p>
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
                        <div class="alert alert-warning py-2 px-3 mb-2 small" role="alert" style="border-left: 4px solid #f0ad4e;">
                            <strong><i class="fa fa-exclamation-triangle mr-1"></i>AI-assisted summary:</strong>
                            If you use automatically generated text, it is only there to help you publish faster. You are responsible for the final content—<strong>please read it carefully, correct any errors, and add anything missing</strong>. Inaccurate, incomplete, or generic descriptions are a common reason resources are <strong>rejected</strong> during review.
                        </div>
                        <textarea placeholder="Descripion" class="form-control newform" id="summernote" name="description" {{ ($requiredFields['description'] ?? true) ? 'required=""' : '' }}>{!! $row->description ?? old('description') !!}</textarea>
                        <small class="text-muted"><i class="fa fa-info-circle"></i> Minimum {{ $publicationMinWords }} words required (approximately {{ $publicationMinChars }} characters). Please provide a detailed description of your publication.</small>
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
                    <textarea class="form-control" name="associated_authors" id="associated_authors" rows="3"
                              maxlength="1000" placeholder="Associated Authors"
                              {{ ($requiredFields['associated_authors'] ?? true) ? 'required' : '' }}>{{ old('associated_authors', @$publication->associated_authors ?? '') }}</textarea>
                    <small class="text-muted">List the individuals or organisations who authored or co-authored this publication or any attached documents. Separate multiple names with commas. Maximum 1,000 characters.</small>
                </div>
                
                <div class="col-md-12 mb-2">
                    <label class="form-label" for="author_affiliation">Author Affiliation/Institution <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="author_affiliation" id="author_affiliation" rows="3"
                              maxlength="1000" placeholder="Author Affiliation/Institution" required>{{ old('author_affiliation', @$publication->author_affiliation ?? '') }}</textarea>
                    <small class="text-muted">Enter the institution, organization, or affiliation of the authors listed above. Maximum 1,000 characters.</small>
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
                
                <div class="col-md-4 mb-2">
                    <label class="form-label" for="publisher">Publisher</label>
                    <input type="text" class="form-control" name="publisher" id="publisher" 
                           placeholder="Publisher name" 
                           value="{{ @$row->publisher ?? old('publisher') }}">
                    <small class="text-muted">Optional - Name of the publisher or publishing organization</small>
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

        <div class="row mt-3 mb-3 submit">
                <div class="col-lg-8 mt-5 float-end"></div>
                <div class="col-lg-3 mt-5 float-end">
                <button class="btn btn-dark col-lg-12 savebtn" type="submit" id="submit">
                    {{ @$row ? 'Save Changes' : 'Submit' }}
                </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include optional progressbar HTML -->
<div class="progress">
    <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0"
        aria-valuemax="100"></div>
</div>

<div id="publication-processing-overlay" role="status" aria-live="polite" aria-busy="false">
    <div class="processing-card">
        <div class="processing-spinner"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></div>
        <strong class="d-block processing-message">Submitting your publication…</strong>
        <p class="mb-0 mt-2 small text-muted">Please do not close this page.</p>
    </div>
</div>

@include('partials.search.fields_js')

<script type="text/javascript">
    $(document).ready(function() {
        // Get regions data with countries (from ViewComposer)
        var regionsData = @json($regions ?? []);
        var allCountries = [];
        
        // Build a map of all countries with their region_id for quick lookup
        if (regionsData && Array.isArray(regionsData)) {
            regionsData.forEach(function(region) {
                if (region.countries && Array.isArray(region.countries)) {
                    region.countries.forEach(function(country) {
                        allCountries.push({
                            id: parseInt(country.id, 10),
                            name: country.name,
                            region_id: parseInt(region.id, 10)
                        });
                    });
                }
            });
        }

        function wizardBootstrapAllCountriesFromDom() {
            if (allCountries.length) {
                return;
            }
            wizardStep1Field('select[name="countries[]"] option').each(function() {
                var id = parseInt($(this).val(), 10);
                var regionId = parseInt($(this).attr('data-region-id'), 10);
                if (id > 0 && regionId > 0) {
                    allCountries.push({
                        id: id,
                        name: $.trim($(this).text()),
                        region_id: regionId
                    });
                }
            });
        }

        wizardBootstrapAllCountriesFromDom();

        function wizardNormalizeRegionIds(selectedValues) {
            var list = Array.isArray(selectedValues) ? selectedValues : (selectedValues ? [selectedValues] : []);
            return list.filter(function(val) {
                return val !== 'all' && val !== '' && val !== null && val !== undefined && !isNaN(parseInt(val, 10));
            }).map(function(id) {
                return parseInt(id, 10);
            });
        }

        function wizardCountriesForRegionIds(regionIds) {
            if (!regionIds.length) {
                return [];
            }
            return allCountries.filter(function(country) {
                return regionIds.indexOf(parseInt(country.region_id, 10)) !== -1;
            });
        }

        function wizardNormalizeCountrySelection(values) {
            var list = Array.isArray(values) ? values.slice() : (values ? [values] : []);
            list = list.filter(function(v) {
                return v !== '' && v !== null && v !== undefined;
            });
            var hasAll = list.some(function(v) {
                return String(v).toLowerCase() === 'all';
            });
            var specifics = list.filter(function(v) {
                return String(v).toLowerCase() !== 'all';
            });
            if (hasAll && specifics.length) {
                return specifics;
            }
            if (hasAll) {
                return ['all'];
            }
            return specifics;
        }

        function wizardApplyRegionToCountries(selectedValues, options) {
            options = options || {};
            var preserveSelection = options.preserveSelection !== false;
            var countrySelect = wizardStep1Field('select[name="countries[]"]');
            if (!countrySelect.length) {
                return;
            }

            countrySelect.prop('disabled', false);

            if (!Array.isArray(selectedValues)) {
                selectedValues = selectedValues ? [selectedValues] : [];
            }

            if (wizardRegionSelectionIncludesAll(selectedValues)) {
                userManuallyChangedCountries = false;
                wizardRebuildCountryOptions(countrySelect, allCountries, true);
                wizardSetCountrySelection(countrySelect, ['all']);
                return;
            }

            var regionIds = wizardNormalizeRegionIds(selectedValues);
            if (!regionIds.length) {
                return;
            }

            var regionCountries = wizardCountriesForRegionIds(regionIds);
            wizardRebuildCountryOptions(countrySelect, regionCountries, true);

            var nextSelection = ['all'];
            if (preserveSelection) {
                var currentVal = wizardGetSelectValue(countrySelect);
                var currentValues = wizardNormalizeCountrySelection(currentVal);
                var validSpecific = currentValues.filter(function(v) {
                    if (String(v).toLowerCase() === 'all') {
                        return false;
                    }
                    return regionCountries.some(function(c) {
                        return String(c.id) === String(v);
                    });
                });
                if (validSpecific.length) {
                    nextSelection = validSpecific;
                    userManuallyChangedCountries = true;
                } else if (currentValues.indexOf('all') !== -1 || String(currentVal).toLowerCase() === 'all') {
                    nextSelection = ['all'];
                    userManuallyChangedCountries = false;
                } else if (!options.fromUserRegionChange) {
                    nextSelection = ['all'];
                    userManuallyChangedCountries = false;
                }
            }

            wizardSetCountrySelection(countrySelect, nextSelection);
        }

        // Track if user has manually changed countries to prevent auto-override on subsequent region changes
        var userManuallyChangedCountries = false;
        var lastAutoSelectedCountries = null;

        function wizardRebuildCountryOptions($select, countryList, includeAllOption) {
            if (!$select.length) {
                return;
            }
            $select.prop('disabled', false);
            var hadSelect2 = typeof $.fn.select2 !== 'undefined' && $select.hasClass('select2-hidden-accessible');
            if (hadSelect2) {
                $select.select2('destroy');
            }
            $select.empty();
            if (includeAllOption) {
                $select.append($('<option>', { value: 'all', text: 'All' }));
            }
            (countryList || []).forEach(function(country) {
                $select.append($('<option>', { value: String(country.id), text: country.name }));
            });
            if (typeof $.fn.select2 !== 'undefined') {
                $select.select2({
                    width: '100%',
                    dir: 'ltr',
                    minimumResultsForSearch: 0,
                    placeholder: $select.data('placeholder') || 'Select Country'
                });
            }
        }

        function wizardEnsureCountriesAllOption($select) {
            if (!$select || !$select.length) {
                return;
            }
            $select.prop('disabled', false);
            if ($select.find('option[value="all"]').length === 0) {
                $select.prepend($('<option>', { value: 'all', text: 'All' }));
                if (typeof $.fn.select2 !== 'undefined' && $select.hasClass('select2-hidden-accessible')) {
                    $select.trigger('change.select2');
                }
            }
        }

        function wizardRegionSelectionIncludesAll(selectedValues) {
            if (!selectedValues) {
                return false;
            }
            var list = Array.isArray(selectedValues) ? selectedValues : [selectedValues];
            return list.some(function(v) {
                return String(v).toLowerCase() === 'all';
            });
        }

        function wizardSetCountrySelection($select, values) {
            if (!$select.length || !values || !values.length) {
                return;
            }
            lastAutoSelectedCountries = values.slice();
            if (typeof $.fn.select2 !== 'undefined' && $select.data('select2')) {
                $select.val(values).trigger('change.select2');
            } else {
                $select.val(values).trigger('change');
            }
        }

        // Publication wizard: search fields_js must not bind region → country (it disables multi-region picks).
        $('.rcc').off('change');

        setTimeout(function() {
            wizardBootstrapAllCountriesFromDom();
            $('#step-1 select.select2').each(function() {
                wizardSyncSelect2FromDom($(this));
            });
            var $region = wizardStep1Field('select[name="rccs[]"]');
            var $countries = wizardStep1Field('select[name="countries[]"]');
            wizardEnsureCountriesAllOption($countries);

            if (!$region.length) {
                return;
            }

            var regionVal = wizardGetSelectValue($region);
            var regionValues = Array.isArray(regionVal) ? regionVal : (regionVal ? [regionVal] : []);

            if (!regionValues.length) {
                return;
            }

            var countryVal = wizardGetSelectValue($countries);
            var countryValues = wizardNormalizeCountrySelection(countryVal);
            var hasSavedCountries = countryValues.some(function(v) {
                return v && String(v).toLowerCase() !== 'all' && parseInt(v, 10) > 0;
            });

            wizardApplyRegionToCountries(regionValues, {
                preserveSelection: hasSavedCountries,
                fromUserRegionChange: false
            });
        }, 600);
        
        // Region → member states: filter options to selected region(s); user can pick All or specific countries.
        $(document).on('change', '#step-1 .rcc.select2, #step-1 select[name="rccs[]"]', function() {
            wizardApplyRegionToCountries($(this).val(), {
                preserveSelection: true,
                fromUserRegionChange: true
            });
        });
        
        // Member states: "All" and specific countries are mutually exclusive.
        $(document).on('change', '#step-1 .country.select2, #step-1 select[name="countries[]"]', function(e) {
            var $select = $(this);
            var normalized = wizardNormalizeCountrySelection(wizardGetSelectValue($select));
            var currentRaw = wizardGetSelectValue($select);
            var currentArr = Array.isArray(currentRaw) ? currentRaw : (currentRaw ? [currentRaw] : []);

            if (JSON.stringify(normalized.slice().sort()) !== JSON.stringify(currentArr.slice().sort())) {
                wizardSetCountrySelection($select, normalized.length ? normalized : ['all']);
                return;
            }

            if (e.originalEvent) {
                userManuallyChangedCountries = true;
                if (normalized.length && normalized[0] !== 'all') {
                    lastAutoSelectedCountries = normalized.slice();
                }
            }
        });
        
        // Handle communities dropdown: Auto-deselect "All" when specific communities are selected
        // This prevents confusion - either "All" OR specific communities, not both
        $(document).on('change', '.communities-select, select[name="communities[]"]', function(e, data) {
            // Skip if this is a programmatic change to prevent recursion
            if (data && data.skipAutoDeselect) {
                return;
            }
            
            var $select = $(this);
            var selectedValues = $select.val() || [];
            
            // Normalize to array
            if (!Array.isArray(selectedValues)) {
                selectedValues = [selectedValues];
            }
            
            var hasEmptyValue = selectedValues.includes('') || selectedValues.includes(null);
            var hasSpecificCommunities = selectedValues.some(function(val) {
                return val !== '' && val !== null && val !== undefined;
            });
            
            // If "All" (empty value) is selected along with specific communities, deselect "All"
            if (hasEmptyValue && hasSpecificCommunities) {
                // Remove empty value from selection
                selectedValues = selectedValues.filter(function(val) {
                    return val !== '' && val !== null && val !== undefined;
                });
                
                // Update selection without triggering change event recursively
                if (typeof $.fn.select2 !== 'undefined' && $select.data('select2')) {
                    $select.val(selectedValues).trigger('change.select2', [{skipAutoDeselect: true}]);
                } else {
                    $select.val(selectedValues).trigger('change', [{skipAutoDeselect: true}]);
                }
            }
            // If specific communities are selected but not "All", that's fine
            // If only "All" is selected, that's also fine (default state)
        });

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

        function escapeHtml(str) {
            if (str == null) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function formatFileSize(bytes) {
            if (bytes == null || isNaN(bytes)) return '';
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // File input with ability to add multiple files incrementally
        var $input = $('#attachments');
        var existingFiles = []; // Store existing files
        var blockedAttachmentExt = @json(\App\Support\PublicationAttachmentSecurity::blockedExtensionsForJs());
        var allowedAttachmentExt = @json(\App\Support\PublicationAttachmentSecurity::allowedExtensionsForJs());

        function attachmentExtension(name) {
            var parts = String(name || '').toLowerCase().split('.');
            return parts.length > 1 ? parts.pop() : '';
        }

        function isBlockedAttachmentName(name) {
            var lower = String(name || '').toLowerCase();
            if (!lower || lower.indexOf('.') === -1) return true;
            for (var i = 0; i < blockedAttachmentExt.length; i++) {
                var re = new RegExp('\\.' + blockedAttachmentExt[i].replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '(\\.|$)', 'i');
                if (re.test(lower)) return true;
            }
            return false;
        }

        function isAllowedAttachmentFile(file) {
            if (!file || !file.name) return false;
            if (isBlockedAttachmentName(file.name)) return false;
            var ext = attachmentExtension(file.name);
            return ext && allowedAttachmentExt.indexOf(ext) !== -1;
        }

        function showAttachmentSecurityError(message) {
            var $box = $('#attachment-security-error');
            if (!$box.length) return;
            if (message) {
                $box.text(message).show();
                $box[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                $box.hide().text('');
            }
        }

        function findInvalidAttachments(fileList) {
            var rejected = [];
            if (!fileList || !fileList.length) {
                return rejected;
            }
            Array.from(fileList).forEach(function(file) {
                if (!isAllowedAttachmentFile(file)) {
                    rejected.push(file.name || 'Unknown file');
                }
            });
            return rejected;
        }

        function validateSelectedAttachments() {
            var input = document.getElementById('attachments');
            if (!input || !input.files || !input.files.length) {
                showAttachmentSecurityError('');
                return { ok: true, rejected: [] };
            }
            var rejected = findInvalidAttachments(input.files);
            if (rejected.length) {
                var msg = 'These file types are not allowed and were not added (scripts and executables are blocked): ' + rejected.join(', ');
                showAttachmentSecurityError(msg);
                return { ok: false, rejected: rejected, message: msg };
            }
            showAttachmentSecurityError('');
            return { ok: true, rejected: [] };
        }

        function goToAttachmentsStep() {
            if ($('#smartwizard').length && typeof $('#smartwizard').smartWizard === 'function') {
                $('#smartwizard').smartWizard('goToStep', 1);
            }
            var $step2 = $('#step-2');
            if ($step2.length) {
                $('html, body').animate({ scrollTop: $step2.offset().top - 80 }, 300);
            }
        }

        function handleAttachmentValidationFailure(result) {
            var msg = (result && result.message) ? result.message : 'One or more attachments use a file type that is not allowed.';
            showAttachmentSecurityError(msg);
            goToAttachmentsStep();
            alert(msg);
            return false;
        }

        window.validateSelectedAttachments = validateSelectedAttachments;
        window.isAllowedAttachmentFile = isAllowedAttachmentFile;
        window.handleAttachmentValidationFailure = handleAttachmentValidationFailure;
        window.goToAttachmentsStep = goToAttachmentsStep;
        window.showAttachmentSecurityError = showAttachmentSecurityError;

        window.wizardShowProcessingOverlay = function(show, message) {
            var $overlay = $('#publication-processing-overlay');
            if ($overlay.length && !$overlay.parent().is('body')) {
                $overlay.appendTo('body');
            }
            if (!$overlay.length) {
                return;
            }
            if (message) {
                $overlay.find('.processing-message').text(message);
            }
            if (show) {
                $overlay.attr('aria-busy', 'true').addClass('is-visible');
                $('body').addClass('publication-form-busy');
            } else {
                $overlay.attr('aria-busy', 'false').removeClass('is-visible');
                $('body').removeClass('publication-form-busy');
            }
        };

        window.wizardSetSubmittingState = function(isSubmitting, message) {
            var $form = $('#publication_form');
            var $submitBtn = $('#submit, .savebtn[type="submit"]');
            $form.data('wizard-submitting', !!isSubmitting);
            if (isSubmitting) {
                if (!$submitBtn.data('wizard-original-html')) {
                    $submitBtn.data('wizard-original-html', $submitBtn.first().html());
                }
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Submitting…');
                window.wizardShowProcessingOverlay(true, message || 'Submitting your publication…');
            } else {
                var original = $submitBtn.data('wizard-original-html');
                $submitBtn.prop('disabled', false);
                if (original) {
                    $submitBtn.html(original);
                }
                window.wizardShowProcessingOverlay(false);
            }
        };

        window.wizardIsSubmitting = function() {
            return !!$('#publication_form').data('wizard-submitting');
        };

        window.wizardSetAiProcessingState = function(isProcessing) {
            var $loader = $('#ai-description-loader');
            var $attachmentCol = $('.attachment');
            var $fileInput = $('#attachments');
            if (isProcessing) {
                $loader.addClass('is-processing').slideDown(250);
                $attachmentCol.addClass('wizard-ai-busy');
                $fileInput.prop('disabled', true);
                if ($loader.length && $loader[0].scrollIntoView) {
                    $loader[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else {
                $loader.removeClass('is-processing').slideUp(200);
                $attachmentCol.removeClass('wizard-ai-busy');
                $fileInput.prop('disabled', false);
            }
        };
        
        if ($input.length) {
            // Clear any existing preview icons from attachment_js.blade.php
            $input.closest('.mb-2').find('.preview').empty();
            
            // Load existing files on page load (for edit mode)
            if ($input[0].files && $input[0].files.length > 0) {
                existingFiles = Array.from($input[0].files);
            }
            
            $input.on('change', function() {
                var newFiles = Array.from(this.files);
                var rejected = [];
                var acceptedNew = [];
                newFiles.forEach(function(newFile) {
                    if (!isAllowedAttachmentFile(newFile)) {
                        rejected.push(newFile.name);
                        return;
                    }
                    acceptedNew.push(newFile);
                });
                if (rejected.length) {
                    showAttachmentSecurityError('These file types are not allowed and were not added (scripts and executables are blocked): ' + rejected.join(', '));
                } else {
                    showAttachmentSecurityError('');
                }

                if (rejected.length && acceptedNew.length === 0) {
                    // Clear disallowed selection from the native picker
                    const emptyDt = new DataTransfer();
                    existingFiles.forEach(function(file) {
                        emptyDt.items.add(file);
                    });
                    this.files = emptyDt.files;
                    updateFilePreview();
                    return;
                }
                
                // Merge new files with existing files (avoid duplicates)
                var mergedFiles = [...existingFiles];
                acceptedNew.forEach(function(newFile) {
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
                        var label = (file.name && String(file.name).trim() !== '') ? file.name : ('File ' + (index + 1));
                        fileList += '<li class="mb-1">';
                        fileList += '<i class="fa fa-file text-muted"></i> ';
                        fileList += '<span>' + escapeHtml(label) + '</span> ';
                        fileList += '<small class="text-muted">(' + formatFileSize(file.size) + ')</small> ';
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
        function getAiSummaryExtractLanguage() {
            var mode = $('input[name="ai_summary_language_mode"]:checked').val() || 'publication';
            if (mode === 'english') {
                return 'en';
            }
            var pubLang = ($('#publication_language').val() || '').trim();
            return pubLang || 'document';
        }

        function updateAiSummaryPublicationLanguageLabel() {
            var $select = $('#publication_language');
            if (!$select.length) {
                return;
            }
            var label = $select.find('option:selected').text() || 'publication language';
            $('#ai-summary-pub-lang-label').text(label.trim());
        }

        $(document).on('change', '#publication_language', updateAiSummaryPublicationLanguageLabel);

        function extractSummaryFromFile(file){
            window.wizardSetAiProcessingState(true);
            
            var formData = new FormData();
            formData.append('file', file);
            formData.append('language', getAiSummaryExtractLanguage());
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $.ajax({
                url: '{{ route("ai.summarise.file") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp){
                    window.wizardSetAiProcessingState(false);
                    
                    if(!resp || !resp.content) return;
                    
                    // Update description
                    if (!$('#summernote').next('.note-editor').length && typeof $.fn.summernote === 'function') {
                        $('#summernote').summernote({ height: 300 });
                    }
                    var current = $('#summernote').val();
                    if (!current || current.trim().length < 50) {
                        $('#summernote').summernote('code', resp.content);
                    } else if (confirm('Replace existing description with AI-extracted summary?')) {
                        $('#summernote').summernote('code', resp.content);
                    }
                    
                    // Update metadata fields if available
                    if (resp.metadata) {
                        // Update Associated Authors
                        if (resp.metadata.authors && resp.metadata.authors.trim() !== '') {
                            var currentAuthors = $('#associated_authors').val();
                            if (!currentAuthors || currentAuthors.trim().length === 0) {
                                $('#associated_authors').val(resp.metadata.authors.trim());
                            } else if (confirm('Replace existing authors with AI-extracted authors?')) {
                                $('#associated_authors').val(resp.metadata.authors.trim());
                            }
                        }
                        
                        // Update Author Affiliation/Institution
                        if (resp.metadata.affiliation && resp.metadata.affiliation.trim() !== '') {
                            var currentAffiliation = $('#author_affiliation').val();
                            if (!currentAffiliation || currentAffiliation.trim().length === 0) {
                                $('#author_affiliation').val(resp.metadata.affiliation.trim());
                            } else if (confirm('Replace existing affiliation with AI-extracted affiliation?')) {
                                $('#author_affiliation').val(resp.metadata.affiliation.trim());
                            }
                        }
                    }
                },
                error: function(xhr, status, error){
                    window.wizardSetAiProcessingState(false);
                    console.error('AI extraction error:', error);
                    if (typeof Lobibox !== 'undefined') {
                        Lobibox.notify('warning', {
                            size: 'mini',
                            sound: false,
                            delay: 5000,
                            title: 'AI extraction',
                            position: 'top right',
                            msg: 'We could not extract data from this file automatically. You can still fill in the form manually.'
                        });
                    }
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

        // Smart Wizard (v5+ API: toolbar.anchor, events leaveStep/showStep)
        $('#smartwizard').smartWizard({
            autoAdjustHeight: false,
            selected: 0,
            theme: 'dots',
            toolbar: {
                position: 'bottom',
                showNextButton: true,
                showPreviousButton: true,
            },
            anchor: {
                enableNavigation: true,
                enableNavigationAlways: false,
            },
        });

        var publicationMinChars = {{ $publicationMinChars }};
        var wizardRequiredFields = @json($requiredFields);

        function wizardEscapeHtml(text) {
            return $('<div>').text(text || '').html();
        }

        function wizardClearFieldError($wrap) {
            if (!$wrap || !$wrap.length) return;
            $wrap.removeClass('has-error is-invalid');
            $wrap.find('.wizard-field-error').remove();
        }

        function wizardShowFieldError($wrap, message) {
            if (!$wrap || !$wrap.length) return;
            wizardClearFieldError($wrap);
            $wrap.addClass('has-error');
            if ($wrap.find('.wizard-field-error').length === 0) {
                $wrap.append('<small class="text-danger d-block mt-1 wizard-field-error">' + wizardEscapeHtml(message) + '</small>');
            }
        }

        function wizardGetSelectValue($select) {
            if (!$select || !$select.length) {
                return '';
            }
            var val = $select.val();
            if (val !== null && val !== undefined && val !== '') {
                if (Array.isArray(val)) {
                    val = val.filter(function(v) { return v !== '' && v !== null && v !== undefined; });
                    if (val.length) {
                        return val;
                    }
                } else {
                    return val;
                }
            }
            var fromSelectedOptions = [];
            $select.find('option:selected').each(function() {
                var v = $(this).val();
                if (v !== '' && v !== null && v !== undefined) {
                    fromSelectedOptions.push(v);
                }
            });
            if (fromSelectedOptions.length) {
                return fromSelectedOptions.length === 1 ? fromSelectedOptions[0] : fromSelectedOptions;
            }
            if (typeof $.fn.select2 !== 'undefined' && $select.hasClass('select2-hidden-accessible')) {
                try {
                    var s2data = $select.select2('data');
                    if (s2data && s2data.length) {
                        var ids = s2data.map(function(item) { return item.id; })
                            .filter(function(id) { return id !== '' && id !== null && id !== undefined; });
                        if (ids.length) {
                            return ids.length === 1 ? ids[0] : ids;
                        }
                    }
                } catch (e) { /* ignore */ }
            }
            return '';
        }

        function wizardSyncSelect2FromDom($select) {
            if (!$select.length || typeof $.fn.select2 === 'undefined' || !$select.hasClass('select2-hidden-accessible')) {
                return;
            }
            var current = wizardGetSelectValue($select);
            if (current !== '' && current !== null && !(Array.isArray(current) && !current.length)) {
                $select.val(current).trigger('change.select2');
                return;
            }
            var selected = [];
            $select.find('option[selected]').each(function() {
                var v = $(this).attr('value');
                if (v !== '' && v != null) {
                    selected.push(v);
                }
            });
            if (!selected.length) {
                $select.find('option:selected').each(function() {
                    var v = $(this).val();
                    if (v !== '' && v != null) {
                        selected.push(v);
                    }
                });
            }
            if (selected.length) {
                $select.val(selected.length === 1 ? selected[0] : selected).trigger('change.select2');
            }
        }

        function wizardSelectHasValue($select) {
            if (!$select.length) {
                return true;
            }
            var val = wizardGetSelectValue($select);
            if (val === null || val === undefined || val === '') {
                return false;
            }
            if (Array.isArray(val)) {
                return val.length > 0;
            }
            return String(val).trim() !== '';
        }

        function wizardStep1Field(selector) {
            return $('#step-1').find(selector).first();
        }

        function wizardSelectionIncludesAll(values) {
            var list = Array.isArray(values) ? values : (values !== '' && values != null ? [values] : []);
            return list.some(function(v) {
                return String(v).toLowerCase() === 'all';
            });
        }

        function wizardValuesAreNumericIds(values) {
            var list = Array.isArray(values) ? values : (values !== '' && values != null ? [values] : []);
            return list.some(function(v) {
                return !isNaN(parseInt(v, 10)) && parseInt(v, 10) > 0;
            });
        }

        window.wizardCountriesHasValue = function($select) {
            if (!$select || !$select.length) {
                return false;
            }
            $select.prop('disabled', false);
            var val = wizardGetSelectValue($select);
            if (val === '' || val === null) {
                return false;
            }
            var values = Array.isArray(val) ? val : [val];
            values = values.filter(function(v) { return v !== '' && v != null; });
            if (!values.length) {
                return false;
            }
            return wizardSelectionIncludesAll(values) || wizardValuesAreNumericIds(values);
        };

        window.wizardPrepareCountriesForSubmit = function() {
            var $countries = wizardStep1Field('select[name="countries[]"]');
            var $region = wizardStep1Field('select[name="rccs[]"]');
            if (!$countries.length) {
                return;
            }
            $countries.prop('disabled', false);
            $('#wizard-countries-all-hidden, #wizard-rccs-all-hidden').remove();

            var regionVal = wizardGetSelectValue($region);
            var regionValues = Array.isArray(regionVal) ? regionVal : (regionVal ? [regionVal] : []);
            var regionHasAll = wizardSelectionIncludesAll(regionValues);

            var countryVal = wizardGetSelectValue($countries);
            var countryValues = Array.isArray(countryVal) ? countryVal : (countryVal ? [countryVal] : []);
            var countryHasAll = wizardSelectionIncludesAll(countryValues);

            if (regionHasAll) {
                if ($countries.find('option[value="all"]').length === 0) {
                    $countries.prepend($('<option>', { value: 'all', text: 'All' }));
                }
                wizardSetCountrySelection($countries, ['all']);
                $('#publication_form').append(
                    '<input type="hidden" name="countries[]" value="all" id="wizard-countries-all-hidden">'
                );
                if (!$region.length || !wizardGetSelectValue($region)) {
                    $('#publication_form').append(
                        '<input type="hidden" name="rccs[]" value="all" id="wizard-rccs-all-hidden">'
                    );
                }
            } else if (countryHasAll) {
                if ($countries.find('option[value="all"]').length === 0) {
                    $countries.prepend($('<option>', { value: 'all', text: 'All' }));
                }
                wizardSetCountrySelection($countries, ['all']);
                $('#publication_form').append(
                    '<input type="hidden" name="countries[]" value="all" id="wizard-countries-all-hidden">'
                );
            }
        };

        window.wizardMemberStatesValidForStep1 = function() {
            var $region = wizardStep1Field('select[name="rccs[]"]');
            var $countries = wizardStep1Field('select[name="countries[]"]');
            if (typeof window.wizardPrepareCountriesForSubmit === 'function') {
                window.wizardPrepareCountriesForSubmit();
            }
            if (!wizardRegionHasValue($region)) {
                return false;
            }
            var regionVal = wizardGetSelectValue($region);
            var regionValues = Array.isArray(regionVal) ? regionVal : (regionVal ? [regionVal] : []);
            if (wizardSelectionIncludesAll(regionValues)) {
            return true;
            }
            return window.wizardCountriesHasValue($countries);
        };

        function wizardRegionHasValue($select) {
            if (!$select.length) return false;
            var val = wizardGetSelectValue($select);
            if (!val || (Array.isArray(val) && val.length === 0)) return false;
            var values = Array.isArray(val) ? val : [val];
            return values.some(function(v) {
                if (v === '' || v === null || v === undefined) return false;
                if (String(v).toLowerCase() === 'all') return true;
                return !isNaN(parseInt(v, 10)) && parseInt(v, 10) > 0;
            });
        }

        function wizardShowStepSummary(stepNumber, messages) {
            var $box = stepNumber === 0 ? $('#wizard-step-1-errors') : $('#wizard-step-2-errors');
            if (!messages || !messages.length) {
                $box.removeClass('is-visible').empty();
                return;
            }
            var html = '<strong>Please fix the following before continuing:</strong><ul class="mb-0 mt-2 pl-3">';
            messages.forEach(function(msg) {
                html += '<li>' + wizardEscapeHtml(msg) + '</li>';
            });
            html += '</ul>';
            $box.html(html).addClass('is-visible');
            if ($box.length && $box[0].scrollIntoView) {
                $box[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function wizardGoToStep(stepIndex) {
            if ($('#smartwizard').length && typeof $('#smartwizard').smartWizard === 'function') {
                $('#smartwizard').smartWizard('goToStep', stepIndex);
            }
        }

        function validateWizardStep1() {
            var errors = [];
            var firstInvalid = null;

            function fail($wrap, message) {
                errors.push(message);
                wizardShowFieldError($wrap, message);
                if (!firstInvalid && $wrap && $wrap.length) firstInvalid = $wrap;
            }

            $('#step-1 .wizard-field-error').remove();
            $('#step-1 .has-error').removeClass('has-error');
            wizardShowStepSummary(0, []);

            $('#step-1 select.select2').each(function() {
                wizardSyncSelect2FromDom($(this));
            });

            if (wizardRequiredFields.title !== false) {
                var $title = wizardStep1Field('#title');
                var titleWrap = $title.closest('.mb-3');
                if (!$title.val() || !String($title.val()).trim()) {
                    fail(titleWrap, 'Resource title is required.');
                }
            }

            if ($('input[name="upload_type"]:checked').val() === 'link') {
                var $link = $('#publication');
                var linkWrap = $link.closest('.url_wrapper');
                if (!$link.val() || !String($link.val()).trim()) {
                    fail(linkWrap, 'Publication URL is required when using External Link.');
                }
            }

            if (wizardRequiredFields.year_published === true) {
                var $year = wizardStep1Field('#year_published');
                if (!wizardSelectHasValue($year)) {
                    fail($year.closest('.wizard-field-year'), 'Year of publication is required.');
                }
            }

            if (wizardRequiredFields.data_category_id !== false) {
                var $cat = wizardStep1Field('select[name="data_category_id"]');
                var catWrap = wizardStep1Field('[data-wizard-field="data_category_id"]');
                if (!wizardSelectHasValue($cat)) {
                    fail(catWrap, 'Please select a category.');
                }
            }

            var $subCat = wizardStep1Field('#category_id');
            var subWrap = wizardStep1Field('[data-wizard-field="category_id"]');
            if (!wizardSelectHasValue($subCat)) {
                fail(subWrap, 'Please select a sub category.');
            }

            if (wizardRequiredFields.theme !== false) {
                var $theme = wizardStep1Field('select[name="theme"]');
                if (!wizardSelectHasValue($theme)) {
                    fail($theme.closest('.col-md-6'), 'Please select a thematic area.');
                }
            }

            if (wizardRequiredFields.sub_theme !== false) {
                var $subTheme = wizardStep1Field('select[name="sub_theme"]');
                if (!wizardSelectHasValue($subTheme)) {
                    fail($subTheme.closest('.col-md-6'), 'Please select a sub theme.');
                }
            }

            var $region = wizardStep1Field('select[name="rccs[]"]');
            var regionWrap = wizardStep1Field('[data-wizard-field="rccs"]');
            if (!wizardRegionHasValue($region)) {
                fail(regionWrap, 'Please select at least one region (or choose All).');
            }

            var countriesWrap = wizardStep1Field('[data-wizard-field="countries"]');
            var countriesValid = typeof window.wizardMemberStatesValidForStep1 === 'function'
                ? window.wizardMemberStatesValidForStep1()
                : (typeof window.wizardCountriesHasValue === 'function'
                    ? window.wizardCountriesHasValue(wizardStep1Field('select[name="countries[]"]'))
                    : wizardSelectHasValue(wizardStep1Field('select[name="countries[]"]')));
            if (!countriesValid) {
                fail(countriesWrap, 'Please select at least one member state (or choose All).');
            }

            @if(is_admin())
            if (wizardRequiredFields.author === true || {{ $adminMustSelectAuthor ? 'true' : 'false' }}) {
                var $author = $('select[name="author"]');
                if ($author.length && !wizardSelectHasValue($author)) {
                    fail($author.closest('.col-md-6, .mb-2').first(), 'Please select a corporate source or member state.');
                }
            }
            @endif

            if (errors.length) {
                wizardShowStepSummary(0, errors);
                if (firstInvalid && firstInvalid[0].scrollIntoView) {
                    firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return { ok: errors.length === 0, errors: errors };
        }

        function validateWizardStep2() {
            var errors = [];
            var firstInvalid = null;

            function fail($wrap, message) {
                errors.push(message);
                wizardShowFieldError($wrap, message);
                if (!firstInvalid && $wrap && $wrap.length) firstInvalid = $wrap;
            }

            $('#step-2 .wizard-field-error').remove();
            $('#step-2 .has-error').removeClass('has-error');
            wizardShowStepSummary(1, []);

            if (wizardRequiredFields.description !== false) {
                var descText = $('#summernote').val() || '';
                var plain = $('<div>').html(descText).text().replace(/\s+/g, ' ').trim();
                var descWrap = $('#summernote').closest('.mb-2');
                if (!plain) {
                    fail(descWrap, 'Publication description is required.');
                } else if (plain.length < publicationMinChars) {
                    fail(descWrap, 'Description must be at least approximately ' + Math.ceil(publicationMinChars / 5) + ' words (' + publicationMinChars + ' characters).');
                }
            }

            if (wizardRequiredFields.associated_authors !== false) {
                var $authors = $('#associated_authors');
                if (!$authors.val() || !String($authors.val()).trim()) {
                    fail($authors.closest('.mb-2'), 'Associated authors are required.');
                }
            }

            var $affiliation = $('#author_affiliation');
            if (!$affiliation.val() || !String($affiliation.val()).trim()) {
                fail($affiliation.closest('.mb-2'), 'Author affiliation/institution is required.');
            }

            var $tags = $('select[name="tags[]"]');
            if (wizardRequiredFields.tags !== false && !wizardSelectHasValue($tags)) {
                fail($tags.closest('.mb-2'), 'Please select at least one tag/health topic.');
            }

            ['doi', 'issn', 'isbn', 'license_id', 'copyright_info'].forEach(function(field) {
                if (wizardRequiredFields[field] !== true) return;
                var $el = $('[name="' + field + '"]');
                if ($el.length && (!$el.val() || !String($el.val()).trim())) {
                    fail($el.closest('.mb-2'), 'This field is required.');
                }
            });

            if (errors.length) {
                wizardShowStepSummary(1, errors);
                if (firstInvalid && firstInvalid[0].scrollIntoView) {
                    firstInvalid[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }

            return { ok: errors.length === 0, errors: errors };
        }

        function validatePublicationWizardFull() {
            if (typeof window.applyTitleCaseToForm === 'function') {
                window.applyTitleCaseToForm('#publication_form');
            }
            if (typeof window.wizardPrepareCountriesForSubmit === 'function') {
                window.wizardPrepareCountriesForSubmit();
            }
            var step1 = validateWizardStep1();
            var step2 = validateWizardStep2();
            if (!step1.ok) {
                wizardGoToStep(0);
                return false;
            }
            if (!step2.ok) {
                wizardGoToStep(1);
                return false;
            }
            return true;
        }

        window.validateWizardStep1 = validateWizardStep1;
        window.validateWizardStep2 = validateWizardStep2;
        window.validatePublicationWizardFull = validatePublicationWizardFull;

        function applyServerValidationErrors(errors) {
            if (!errors || typeof errors !== 'object') {
                return;
            }
            var step1Keys = ['title', 'link', 'publication', 'year_published', 'data_category_id', 'category_id', 'theme', 'sub_theme', 'rccs', 'countries', 'author'];
            var step2Keys = ['description', 'associated_authors', 'author_affiliation', 'tags', 'doi', 'issn', 'isbn', 'license_id', 'copyright_info', 'files'];
            var onStep1 = false;
            var onStep2 = false;
            var summary1 = [];
            var summary2 = [];

            Object.keys(errors).forEach(function(key) {
                var base = key.split('.')[0];
                var msgs = errors[key];
                var msg = Array.isArray(msgs) ? msgs[0] : String(msgs);
                if (step1Keys.indexOf(base) !== -1) {
                    onStep1 = true;
                    summary1.push(msg);
                } else if (step2Keys.indexOf(base) !== -1) {
                    onStep2 = true;
                    summary2.push(msg);
                }
                var $wrap = $('[data-wizard-field="' + base + '"]');
                if (!$wrap.length && base === 'title') {
                    $wrap = $('#title').closest('.mb-3');
                }
                if (!$wrap.length && (base === 'link' || base === 'publication')) {
                    $wrap = $('#publication').closest('.url_wrapper');
                }
                if ($wrap.length) {
                    wizardShowFieldError($wrap, msg);
                }
            });

            if (onStep1) {
                wizardShowStepSummary(0, summary1);
                wizardGoToStep(0);
            } else if (onStep2) {
                wizardShowStepSummary(1, summary2);
                wizardGoToStep(1);
            }
        }
        window.applyServerValidationErrors = applyServerValidationErrors;

        $('#step-1 select, #step-1 input').on('change input', function() {
            var $wrap = $(this).closest('.wizard-field-wrap, .mb-3, .url_wrapper, .wizard-field-year');
            wizardClearFieldError($wrap);
            if ($('#wizard-step-1-errors').hasClass('is-visible')) {
                validateWizardStep1();
                if ($('#wizard-step-1-errors ul li').length === 0) {
                    wizardShowStepSummary(0, []);
                }
            }
        });

        $('#step-2 select, #step-2 input, #summernote').on('change input', function() {
            var $wrap = $(this).closest('.mb-2');
            wizardClearFieldError($wrap);
        });

        function wizardGetCurrentStepIndex() {
            var sw = $('#smartwizard').data('smartWizard');
            if (sw && typeof sw.getStepInfo === 'function') {
                return sw.getStepInfo().currentStep;
            }
            return 0;
        }

        function wizardSetNavButtons(stepPosition) {
            var $prev = $('#smartwizard .sw-btn-prev');
            var $next = $('#smartwizard .sw-btn-next');
            $prev.removeClass('disabled anchor-disabled');
            $next.removeClass('disabled anchor-disabled');
            $("#submit").addClass('disabled');
            $(".submit").hide();

            if (stepPosition === 'first') {
                $prev.addClass('disabled');
            } else if (stepPosition === 'last') {
                $next.addClass('disabled');
                $("#submit").removeClass('disabled');
                $(".submit").show();
            }
        }

        // leaveStep: (anchor, currentStep, nextStep, direction) — NOT (step, direction)
        $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStep, nextStep, direction) {
            if (direction === 'forward' && currentStep === 0) {
                if (!validateWizardStep1().ok) {
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        });

        // Backup guard on Next (capture phase, before SmartWizard's handler)
        var smartWizardEl = document.getElementById('smartwizard');
        if (smartWizardEl) {
            smartWizardEl.addEventListener('click', function(e) {
                var nextBtn = e.target.closest('.sw-btn-next');
                if (!nextBtn || nextBtn.classList.contains('disabled')) {
                    return;
                }
                if (wizardGetCurrentStepIndex() === 0 && !validateWizardStep1().ok) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }
            }, true);
        }

        // Block clicking Step 2 in the nav until Step 1 validates
        $('#smartwizard').on('click', '.nav-link', function(e) {
            if (wizardGetCurrentStepIndex() !== 0) {
                return true;
            }
            var clickedIndex = $('#smartwizard .nav-link').index(this);
            if (clickedIndex > 0 && !validateWizardStep1().ok) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        });

        // Step show event
        $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, direction, stepPosition) {
            wizardSetNavButtons(stepPosition);
        });

    });


    $(function() {
        // Initialize Parsley with configuration to exclude hidden fields
        var parsleyInstance = $('#publication_form').parsley({
            excluded: 'input[type=hidden], input[data-parsley-excluded="true"]'
        });
        
        // For non-admin users, ensure author field is excluded from Parsley validation
        @if(!is_admin())
            parsleyInstance.removeItem('input[name="author"]');
            // Also remove required attribute if it exists
            $('select[name="author"]').removeAttr('required');
            $('select[name="author"]').removeAttr('data-parsley-required');
        @endif
        
        parsleyInstance.on('field:validated', function() {

                var ok = $('.parsley-error').length === 0;
                $('.bs-callout-info').toggleClass('hidden', !ok);
                $('.bs-callout-warning').toggleClass('hidden', ok);

            })
            .on('form:submit', function(e) {
                if (!window.validatePublicationWizardFull()) {
                    e.preventDefault();
                    return false;
                }

                var attachmentCheck = window.validateSelectedAttachments();
                if (!attachmentCheck.ok) {
                    e.preventDefault();
                    return window.handleAttachmentValidationFailure(attachmentCheck);
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
                    
                    // Add each allowed file only
                    Array.from(fileInput[0].files).forEach(function(file, index) {
                        if (!window.isAllowedAttachmentFile(file)) {
                            return;
                        }
                        formData.append('files[]', file);
                        console.log('Added file to FormData:', file.name, '(' + (file.size / 1024).toFixed(2) + ' KB)');
                    });
                    
                    // Prevent default form submission
                    e.preventDefault();
                    window.wizardSetSubmittingState(true);
                    
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
                                // Redirect based on context: admin or frontend
                                var formAction = form.action || '';
                                if (formAction.includes('/admin/publications') || formAction.includes('admin/publications/save')) {
                                    // Admin context - redirect to admin publications list
                                    window.location.href = '{{ url("admin/publications") }}';
                                } else {
                                    // Frontend context - redirect to account publications
                                    window.location.href = '{{ route("account.publications") }}';
                                }
                            } else {
                                alert(response.message || 'An error occurred. Please try again.');
                                window.wizardSetSubmittingState(false);
                            }
                        },
                        error: function(xhr) {
                            console.error('Form submission error:', xhr);
                            var errorMsg = 'An error occurred while submitting the form.';
                            var validationErrors = null;
                            if (xhr.status === 422 && xhr.responseJSON) {
                                validationErrors = xhr.responseJSON.errors || null;
                                if (validationErrors && validationErrors.files) {
                                    var fileErrs = validationErrors.files;
                                    errorMsg = Array.isArray(fileErrs) ? fileErrs.join(' ') : String(fileErrs);
                                    window.showAttachmentSecurityError(errorMsg);
                                    window.goToAttachmentsStep();
                                } else if (xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseText) {
                                try {
                                    var errorResponse = JSON.parse(xhr.responseText);
                                    validationErrors = errorResponse.errors || null;
                                    if (validationErrors && validationErrors.files) {
                                        var fe = validationErrors.files;
                                        errorMsg = Array.isArray(fe) ? fe.join(' ') : String(fe);
                                        window.showAttachmentSecurityError(errorMsg);
                                        window.goToAttachmentsStep();
                                    } else if (errorResponse.message) {
                                        errorMsg = errorResponse.message;
                                    }
                                } catch(e) {
                                    // Use default error message
                                }
                            }
                            if (validationErrors && typeof window.applyServerValidationErrors === 'function') {
                                window.applyServerValidationErrors(validationErrors);
                            }
                            alert(errorMsg);
                            window.wizardSetSubmittingState(false);
                        },
                        complete: function() {
                            // Success path redirects; errors reset above
                        }
                    });
                    
                    return false; // Prevent default form submission
                } else {
                    e.preventDefault();
                    window.wizardSetSubmittingState(true);
                    var formNoFiles = document.getElementById('publication_form');
                    var formDataNoFiles = new FormData(formNoFiles);
                    $.ajax({
                        url: formNoFiles.action,
                        method: 'POST',
                        data: formDataNoFiles,
                        processData: false,
                        contentType: false,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        success: function(response) {
                            if (response.status === 200 && response.alert_class === 'success') {
                                if (response.message) {
                                    alert(response.message);
                                }
                                window.location.href = '{{ route("account.publications") }}';
                            } else {
                                alert(response.message || 'An error occurred. Please try again.');
                                window.wizardSetSubmittingState(false);
                            }
                        },
                        error: function(xhr) {
                            var errorMsg = 'An error occurred while submitting the form.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            }
                            if (xhr.responseJSON && xhr.responseJSON.errors && typeof window.applyServerValidationErrors === 'function') {
                                window.applyServerValidationErrors(xhr.responseJSON.errors);
                            }
                            alert(errorMsg);
                            window.wizardSetSubmittingState(false);
                        }
                    });
                    return false;
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
            // Skip wizard handler if this is an admin form (admin has its own handler)
            var formAction = form.attr('action') || '';
            if (formAction.includes('/admin/publications') || formAction.includes('admin/publications/save')) {
                // This is an admin form, let the admin handler take care of it
                return;
            }
            
            form.off('submit').on('submit', function(e) {
                e.preventDefault();

                if (window.wizardIsSubmitting && window.wizardIsSubmitting()) {
                    return false;
                }

                if (!window.validatePublicationWizardFull()) {
                    return false;
                }

                var attachmentCheck = window.validateSelectedAttachments();
                if (!attachmentCheck.ok) {
                    return window.handleAttachmentValidationFailure(attachmentCheck);
                }
                
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
                    
                    // Add each allowed file individually (never upload blocked types)
                    Array.from(fileInput[0].files).forEach(function(file, index) {
                        if (!window.isAllowedAttachmentFile(file)) {
                            return;
                        }
                        formData.append('files[]', file);
                        console.log('Frontend form: Added file to FormData:', file.name, '(' + (file.size / 1024).toFixed(2) + ' KB)');
                    });
                } else {
                    console.log('Frontend form: No files detected in input');
                }
                
                var url = formEl.attr('action');
                window.wizardSetSubmittingState(true);
                
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
                                        var formAction = formEl.attr('action') || '';
                                        var redirectUrl = (formAction.includes('/admin/publications') || formAction.includes('admin/publications/save')) 
                                            ? '{{ url("admin/publications") }}' 
                                            : '{{ route("account.publications") }}';
                                        window.location.href = redirectUrl;
                                    }
                                });
                                // Also redirect after delay in case callback doesn't fire
                                setTimeout(function(){
                                    var formAction = formEl.attr('action') || '';
                                    var redirectUrl = (formAction.includes('/admin/publications') || formAction.includes('admin/publications/save')) 
                                        ? '{{ url("admin/publications") }}' 
                                        : '{{ route("account.publications") }}';
                                    window.location.href = redirectUrl;
                                }, 3000);
                            } else {
                                // Fallback to alert if LobiBox not loaded
                                alert(successMsg);
                                setTimeout(function(){
                                    var formAction = formEl.attr('action') || '';
                                    var redirectUrl = (formAction.includes('/admin/publications') || formAction.includes('admin/publications/save')) 
                                        ? '{{ url("admin/publications") }}' 
                                        : '{{ route("account.publications") }}';
                                    window.location.href = redirectUrl;
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
                            window.wizardSetSubmittingState(false);
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
                                if (errors.files) {
                                    var fileErrs = errors.files;
                                    errorMsg = Array.isArray(fileErrs) ? fileErrs.join(' ') : String(fileErrs);
                                    window.showAttachmentSecurityError(errorMsg);
                                    window.goToAttachmentsStep();
                                }
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

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors
                            && typeof window.applyServerValidationErrors === 'function') {
                            window.applyServerValidationErrors(xhr.responseJSON.errors);
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
                        
                        window.wizardSetSubmittingState(false);
                    }
                });
                
                return false;
            });
        }
    });
</script>
