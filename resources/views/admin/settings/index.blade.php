@extends('admin.layouts.main')
@section('styles')
    <!-- Bootstrap Colorpicker CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/css/bootstrap-colorpicker.min.css"
        rel="stylesheet">
    <style>
        .settings-container {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .settings-header {
            background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #16c653 100%);
            padding: 2rem;
            color: white;
        }

        .settings-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }

        .settings-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .settings-tabs {
            border-bottom: 2px solid #e2e8f0;
            background: #f8f9fa;
            padding: 0 2rem;
        }

        .settings-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #64748b;
            font-weight: 500;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .settings-tabs .nav-link:hover {
            color: var(--theme-color-primary, #119A48);
            background: transparent;
            border-bottom-color: rgba(17, 154, 72, 0.3);
        }

        .settings-tabs .nav-link.active {
            color: var(--theme-color-primary, #119A48);
            background: #ffffff;
            border-bottom-color: var(--theme-color-primary, #119A48);
        }

        .settings-content {
            padding: 2rem;
        }

        .settings-section {
            display: none;
        }

        .settings-section.active {
            display: block;
        }

        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-section-title i {
            color: var(--theme-color-primary, #119A48);
            font-size: 1.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.9375rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: #2d3748;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: var(--theme-color-primary, #119A48);
            box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
            outline: none;
        }

        /* Select dropdown styling */
        .form-control select,
        select.form-control {
            color: #2d3748 !important;
            background-color: #ffffff !important;
        }

        select.form-control option {
            color: #2d3748 !important;
            background-color: #ffffff !important;
            padding: 0.5rem;
        }

        .colorPicker {
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            display: flex;
        }

        /* Remove border from form-control inside colorPicker to avoid doubled border */
        .colorPicker .form-control {
            border: none !important;
            border-radius: 0;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .colorPicker:focus-within {
            border-color: var(--theme-color-primary, #119A48);
            box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
        }

        .colorPicker .input-group-append {
            display: flex;
        }

        .colorPicker .input-group-text {
            width: 60px;
            border: none !important;
            border-left: 1px solid #e2e8f0 !important;
            padding: 0;
            background-color: #ffffff;
        }

        .colorPicker:focus-within .input-group-text {
            border-left-color: var(--theme-color-primary, #119A48) !important;
        }

        .color-preview {
            width: 100%;
            height: 100%;
            min-height: 48px;
            border-radius: 0;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
            cursor: pointer;
        }

        .gradient-preview {
            height: 80px;
            border-radius: 8px;
            margin-top: 0.5rem;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .gradient-preview:hover {
            border-color: var(--theme-color-primary, #119A48);
        }

        .image-preview {
            margin-top: 0.75rem;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            display: inline-block;
            max-width: 150px;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
        }

        .btn-save {
            background: var(--theme-color-primary, #119A48);
            border: none;
            border-radius: 8px;
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            background: color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
            color: white;
        }

        .btn-outline-secondary {
            border: 2px solid #64748b;
            border-radius: 8px;
            padding: 0.875rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
            background: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);
        }

        .info-text {
            font-size: 0.875rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .terminal-output {
            background: #1e1e1e;
            color: #d4d4d4;
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            font-size: 0.875rem;
            padding: 1rem;
            border-radius: 4px;
            min-height: 300px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.6;
        }

        .terminal-line {
            margin-bottom: 0.25rem;
        }

        .terminal-prompt {
            color: #4ec9b0;
            font-weight: bold;
            margin-right: 0.5rem;
        }

        .terminal-text {
            color: #d4d4d4;
        }

        .terminal-success {
            color: #4ec9b0;
        }

        .terminal-error {
            color: #f48771;
        }

        .terminal-info {
            color: #569cd6;
        }

        #cache-output-terminal::-webkit-scrollbar {
            width: 8px;
        }

        #cache-output-terminal::-webkit-scrollbar-track {
            background: #252526;
        }

        #cache-output-terminal::-webkit-scrollbar-thumb {
            background: #424242;
            border-radius: 4px;
        }

        #cache-output-terminal::-webkit-scrollbar-thumb:hover {
            background: #4e4e4e;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1 class="page-title">System Configuration</h1>
        <div>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">System Configuration</li>
            </ol>
        </div>
    </div>

    @if(Session::has('alert-success') || Session::has('alert-danger'))
        <div class="alert alert-{{ Session::has('alert-success') ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            {{ Session::get('alert-success') ?? Session::get('alert-danger') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.config.save') }}" method="post" enctype="multipart/form-data">
            @csrf
        <div class="settings-container">
            <div class="settings-header">
                <h2><i class="fa fa-cog me-2"></i>System Configuration</h2>
                <p>Manage your site settings and preferences</p>
            </div>

            <ul class="nav nav-tabs settings-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-tab="general" type="button" role="tab" aria-controls="general" aria-selected="true">
                        <i class="fa fa-info-circle me-2"></i>General
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appearance-tab" data-tab="appearance" type="button" role="tab" aria-controls="appearance" aria-selected="false">
                        <i class="fa fa-palette me-2"></i>Appearance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contact-tab" data-tab="contact" type="button" role="tab" aria-controls="contact" aria-selected="false">
                        <i class="fa fa-address-book me-2"></i>Contact
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="advanced-tab" data-tab="advanced" type="button" role="tab" aria-controls="advanced" aria-selected="false">
                        <i class="fa fa-sliders me-2"></i>Advanced
                    </button>
                </li>
            </ul>

            <div class="tab-content settings-content" id="settingsTabContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-bullhorn"></i>
                        Basic Information
                </div>

                    <div class="row">
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Site Name <span class="text-danger">*</span></label>
                                <input type="text" name="site_name" value="{{ $settings->site_name }}" class="form-control" required>
                </div>
                </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" value="{{ $settings->title }}" class="form-control">
                </div>
                </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Slogan</label>
                                <input type="text" name="slogan" value="{{ $settings->slogan }}" class="form-control" placeholder="Your site slogan">
                </div>
            </div>
                        <div class="col-md-6">
                <div class="form-group">
                                <label>Language</label>
                                <input type="text" name="language" value="{{ $settings->language }}" class="form-control" placeholder="en">
                </div>
                </div>
                </div>

                <div class="form-group">
                        <label>Site Description</label>
                        <textarea name="site_description" rows="4" class="form-control" placeholder="Brief description of your site">{{ $settings->site_description }}</textarea>
                        <small class="info-text">This description may be used by search engines.</small>
                </div>

                <div class="form-group">
                        <label>SEO Keywords</label>
                        <textarea name="seo_keywords" rows="3" class="form-control" placeholder="keyword1, keyword2, keyword3">{{ $settings->seo_keywords }}</textarea>
                        <small class="info-text">Separate keywords with commas.</small>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-images"></i>
                        Branding
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Site Logo</label>
                                <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                                <small class="info-text">Recommended: 500x230 pixels</small>
                                @if(settings()->logo)
                                    <div class="image-preview">
                                        <img src="{{ settings()->logo }}" alt="Logo Preview">
                                    </div>
                                @endif
                </div>
            </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Favicon</label>
                                <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*">
                                <small class="info-text">Recommended: 350x350 pixels</small>
                                @if(settings()->favicon)
                                    <div class="image-preview">
                                        <img src="{{ settings()->favicon }}" alt="Favicon Preview">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Appearance Tab -->
                <div class="tab-pane fade" id="appearance" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-paint-brush"></i>
                        Color Scheme
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Primary Color</label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="primary_color" value="{{ $settings->primary_color ?? '#119A48' }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->primary_color ?? '#119A48' }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Secondary Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="secondary_color" value="{{ $settings->secondary_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->secondary_color }}"></span>
                                    </div>
                    </div>
                </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Primary Text Color</label>
                    <div class="input-group colorPicker">
                                    <input type="text" name="primary_text_color" value="{{ $settings->primary_text_color }}" class="form-control" />
                        <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->primary_text_color }}"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Links Active Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="links_active_color" value="{{ $settings->links_active_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->links_active_color }}"></span>
                                    </div>
                    </div>
                </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Icon Font Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="icon_font_color" value="{{ $settings->icon_font_color }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->icon_font_color }}"></span>
                                    </div>
                    </div>
                </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Show Icons in Main Menu</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="menu_icons_enabled" name="menu_icons_enabled" value="1" @if($settings->menu_icons_enabled) checked @endif>
                                    <label class="form-check-label" for="menu_icons_enabled">Enable subtle icons next to top navigation items</label>
                                </div>
                                <small class="info-text">Turn this on to display icons in the header menu (Home, Browse, Health Emergencies, etc.).</small>
                </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Homepage Sections</label>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_featured" name="show_featured" value="1" @if(!empty($settings->show_featured)) checked @endif>
                                    <label class="form-check-label" for="show_featured">Show Featured content</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_events" name="show_events" value="1" @if(!empty($settings->show_events)) checked @endif>
                                    <label class="form-check-label" for="show_events">Show Events slider</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_top_searches" name="show_top_searches" value="1" @if(!empty($settings->show_top_searches)) checked @endif>
                                    <label class="form-check-label" for="show_top_searches">Show Top Searches</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_tags" name="show_tags" value="1" @if(!empty($settings->show_tags)) checked @endif>
                                    <label class="form-check-label" for="show_tags">Show Tags strip</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_quotes" name="show_quotes" value="1" @if(!empty($settings->show_quotes)) checked @endif>
                                    <label class="form-check-label" for="show_quotes">Show Quotes banner</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="show_quiz" name="show_quiz" value="1" @if(!empty($settings->show_quiz)) checked @endif>
                                    <label class="form-check-label" for="show_quiz">Show Quiz button on search results</label>
                                </div>
                                <small class="info-text">Toggle which sections appear on the homepage.</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-image"></i>
                        Background & Gradient
                </div>

                    <div class="form-group">
                        <label>Spotlight Banner Image</label>
                        <input type="file" name="spotlight_banner" id="spotlight_banner" class="form-control" accept="image/*">
                        <small class="info-text">Recommended: 1894x658 pixels. This image will be used for the search area background.</small>
                        @if(settings()->spotlight_banner)
                            <div class="image-preview">
                                <img src="{{ settings()->spotlight_banner }}" alt="Banner Preview">
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gradient Start Color</label>
                                <div class="input-group colorPicker" id="gradientStartPicker">
                                    <input type="text" name="gradient_start_color" value="{{ $settings->gradient_start_color ?? '#119A48' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview gradient-start-preview" style="background-color: {{ $settings->gradient_start_color ?? '#119A48' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Used when no banner image is set</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gradient End Color</label>
                                <div class="input-group colorPicker" id="gradientEndPicker">
                                    <input type="text" name="gradient_end_color" value="{{ $settings->gradient_end_color ?? '#16c653' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview gradient-end-preview" style="background-color: {{ $settings->gradient_end_color ?? '#16c653' }}"></span>
                                    </div>
                                </div>
                                <small class="info-text">Used when no banner image is set</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Gradient Preview</label>
                        <div class="gradient-preview" id="gradientPreview"></div>
                        <small class="info-text">Preview of the gradient (shown when no banner image is set)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Banner Text Color</label>
                                <div class="input-group colorPicker">
                                    <input type="text" name="banner_text" value="{{ $settings->banner_text ?? '#FFFFFF' }}" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text color-preview" style="background-color: {{ $settings->banner_text ?? '#FFFFFF' }}"></span>
                                    </div>
                        </div>
                    </div>
                </div>
                        <div class="col-md-6">
                    <div class="form-group">
                        <label>Footer Style</label>
                        <select class="form-control" name="footer_style">
                            <option value="light-footer" @if (settings()->footer_style == 'light-footer') selected @endif>Light</option>
                            <option value="dark-footer" @if (settings()->footer_style == 'dark-footer') selected @endif>Dark</option>
                        </select>
                            </div>
                    </div>
                </div>

                    <div class="form-group">
                        <label>Site Theme</label>
                        <select class="form-control" name="site_theme">
                            <option value="" @if (settings()->site_theme == '') selected @endif>Default Theme</option>
                            <option value="theme1." @if (settings()->site_theme == 'theme1.') selected @endif>Theme1</option>
                        </select>
                    </div>
                </div>

                <!-- Contact Tab -->
                <div class="tab-pane fade" id="contact" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-envelope"></i>
                        Contact Information
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" value="{{ $settings->email }}" class="form-control" placeholder="contact@example.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" value="{{ $settings->phone }}" class="form-control" placeholder="+1 234 567 8900">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="3" class="form-control" placeholder="Enter site address">{{ $settings->address }}</textarea>
                </div>

                <div class="form-group">
                        <label>Timezone</label>
                        @include('partials.general.timezones', ['selected' => $settings->timezone])
                    </div>
                </div>

                <!-- Advanced Tab -->
                <div class="tab-pane fade" id="advanced" role="tabpanel">
                    <div class="form-section-title">
                        <i class="fa fa-code"></i>
                        Advanced Settings
                    </div>

                    <div class="form-group">
                        <label>Google Analytics Script</label>
                        <textarea name="analytics_script" rows="6" class="form-control" placeholder="Paste your Google Analytics script here">{{ $settings->analytics_script }}</textarea>
                        <small class="info-text">Paste the complete Google Analytics tracking code.</small>
                    </div>

                    <div class="form-group">
                        <label>Content Disclaimer</label>
                        <textarea name="content_disclaimer" rows="5" class="form-control" placeholder="Enter content disclaimer text">{{ $settings->content_disclaimer }}</textarea>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-file-alt"></i>
                        Publication Form Settings
                    </div>

                    <div class="form-group">
                        <label>Minimum Publication Description Words</label>
                        <input type="number" name="publication_min_words" value="{{ $settings->publication_min_words ?? 150 }}" class="form-control" min="10" max="1000" step="10">
                        <small class="info-text">Set the minimum number of words required for publication descriptions. Default is 150 words.</small>
                    </div>

                    <div class="form-group">
                        <label>Required Fields on Publication Form</label>
                        <small class="info-text d-block mb-3">Select which fields should be required when users submit publications:</small>
                        
                        @php
                            $requiredFields = json_decode($settings->publication_required_fields ?? '{}', true);
                            if (empty($requiredFields)) {
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
                        @endphp

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[title]" value="1" id="req_title" @if($requiredFields['title'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_title">Title</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[description]" value="1" id="req_description" @if($requiredFields['description'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_description">Description</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[associated_authors]" value="1" id="req_associated_authors" @if($requiredFields['associated_authors'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_associated_authors">Associated Authors</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[tags]" value="1" id="req_tags" @if($requiredFields['tags'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_tags">Tags/Health Topics</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[theme]" value="1" id="req_theme" @if($requiredFields['theme'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_theme">Theme</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[sub_theme]" value="1" id="req_sub_theme" @if($requiredFields['sub_theme'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_sub_theme">Sub Theme</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[data_category_id]" value="1" id="req_data_category_id" @if($requiredFields['data_category_id'] ?? true) checked @endif>
                                    <label class="form-check-label" for="req_data_category_id">Category</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[year_published]" value="1" id="req_year_published" @if($requiredFields['year_published'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_year_published">Year Published</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[author]" value="1" id="req_author" @if($requiredFields['author'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_author">Source/Author</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[doi]" value="1" id="req_doi" @if($requiredFields['doi'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_doi">DOI</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[issn]" value="1" id="req_issn" @if($requiredFields['issn'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_issn">ISSN</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[isbn]" value="1" id="req_isbn" @if($requiredFields['isbn'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_isbn">ISBN</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[license_id]" value="1" id="req_license_id" @if($requiredFields['license_id'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_license_id">License</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input type="checkbox" class="form-check-input required-field-checkbox" name="required_fields[copyright_info]" value="1" id="req_copyright_info" @if($requiredFields['copyright_info'] ?? false) checked @endif>
                                    <label class="form-check-label" for="req_copyright_info">Copyright Info</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section-title mt-4">
                        <i class="fa fa-sign-in-alt"></i>
                        Social Login Configuration
                    </div>

                    <div class="form-group">
                        <label>Social Login Providers</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_microsoft_login" name="enable_microsoft_login" value="1" @if(!empty($settings->enable_microsoft_login)) checked @endif>
                            <label class="form-check-label" for="enable_microsoft_login">
                                <i class="lni lni-microsoft me-2" style="color: #00a1f1;"></i>Enable Microsoft Login
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_google_login" name="enable_google_login" value="1" @if(!empty($settings->enable_google_login)) checked @endif>
                            <label class="form-check-label" for="enable_google_login">
                                <i class="lni lni-google me-2" style="color: #db4437;"></i>Enable Google Login
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="enable_linkedin_login" name="enable_linkedin_login" value="1" @if(!empty($settings->enable_linkedin_login)) checked @endif>
                            <label class="form-check-label" for="enable_linkedin_login">
                                <i class="fab fa-linkedin me-2" style="color: #0077b5;"></i>Enable LinkedIn Login
                            </label>
                        </div>
                        <small class="info-text">Toggle which social login providers are available to users. Make sure the corresponding credentials are configured in your .env file.</small>
                    </div>
                </div>
                </div>

            <div class="settings-content" style="border-top: 2px solid #e2e8f0; padding: 1.5rem 2rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-save">
                            <i class="fa fa-save me-2"></i>Save All Changes
                        </button>
                    </div>
                    <div>
                        <button type="button" 
                           class="btn btn-outline-secondary" 
                           onclick="clearCache()"
                           id="clear-cache-btn"
                           title="Clear all cached data including settings">
                            <i class="fa fa-broom me-2"></i>Clear Cache
                        </button>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">
                    <i class="fa fa-info-circle"></i> Settings are cached for 24 hours for better performance. Use "Clear Cache" to refresh settings immediately after making changes.
                </small>
            </div>
        </div>
    </form>

    <!-- Cache Clear Output Modal -->
    <div class="modal fade" id="cacheOutputModal" tabindex="-1" aria-labelledby="cacheOutputModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cacheOutputModalLabel">
                        <i class="fa fa-terminal me-2"></i>Cache Clear Output
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="cache-output-terminal" class="terminal-output">
                        <div class="terminal-line">
                            <span class="terminal-prompt">$</span>
                            <span class="terminal-text">Waiting for output...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="copyCacheOutput()">
                        <i class="fa fa-copy me-2"></i>Copy Output
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('common.select2')
    @include('common.attachment_js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.2.0/js/bootstrap-colorpicker.min.js"></script>

    <script>
        function clearCache() {
            const btn = document.getElementById('clear-cache-btn');
            const originalText = btn.innerHTML;
            
            // Disable button and show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Clearing...';
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('cacheOutputModal'));
            document.getElementById('cache-output-terminal').innerHTML = '<div class="terminal-line"><span class="terminal-prompt">$</span><span class="terminal-text">Initializing cache clear...</span></div>';
            modal.show();
            
            // Make AJAX request
            fetch('{{ route("admin.config.clear-cache") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Display output
                let outputHtml = '';
                if (data.output) {
                    const lines = data.output.split('\n');
                    lines.forEach(line => {
                        if (line.trim() === '') return;
                        
                        let className = 'terminal-text';
                        if (line.includes('✓') || line.includes('successfully')) {
                            className = 'terminal-success';
                        } else if (line.includes('✗') || line.includes('Error')) {
                            className = 'terminal-error';
                        } else if (line.includes('===') || line.includes('Clearing')) {
                            className = 'terminal-info';
                        }
                        
                        outputHtml += `<div class="terminal-line"><span class="terminal-text">${escapeHtml(line)}</span></div>`;
                    });
                } else {
                    outputHtml = '<div class="terminal-line"><span class="terminal-success">Cache cleared successfully!</span></div>';
                }
                
                document.getElementById('cache-output-terminal').innerHTML = outputHtml;
                
                // Scroll to bottom
                const terminal = document.getElementById('cache-output-terminal');
                terminal.scrollTop = terminal.scrollHeight;
                
                // Show success message
                if (data['alert-success']) {
                    showAlert('success', data['alert-success']);
                } else if (data['alert-danger']) {
                    showAlert('danger', data['alert-danger']);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('cache-output-terminal').innerHTML = 
                    '<div class="terminal-line"><span class="terminal-error">Error: ' + escapeHtml(error.message) + '</span></div>';
                showAlert('danger', 'Error clearing cache: ' + error.message);
            })
            .finally(() => {
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function copyCacheOutput() {
            const terminal = document.getElementById('cache-output-terminal');
            const text = terminal.innerText || terminal.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fa fa-check me-2"></i>Copied!';
                btn.classList.add('btn-success');
                btn.classList.remove('btn-primary');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                }, 2000);
            }).catch(err => {
                alert('Failed to copy: ' + err);
            });
        }

        function showAlert(type, message) {
            // Remove existing alerts
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());
            
            // Create new alert
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Insert at top of content
            const content = document.querySelector('.settings-container');
            if (content) {
                content.insertBefore(alertDiv, content.firstChild);
            }
        }

        $(function() {
            // Tab switching functionality
            $('.settings-tabs .nav-link').on('click', function(e) {
                e.preventDefault();
                
                var targetTab = $(this).data('tab');
                
                // Remove active class from all tabs and panes
                $('.settings-tabs .nav-link').removeClass('active').attr('aria-selected', 'false');
                $('.tab-pane').removeClass('show active');
                
                // Add active class to clicked tab
                $(this).addClass('active').attr('aria-selected', 'true');
                
                // Show corresponding tab pane
                $('#' + targetTab).addClass('show active');
            });

            // Initialize color pickers
            $('.colorPicker').each(function() {
                var $picker = $(this);
                var $input = $picker.find('input[type="text"]');
                var $preview = $picker.find('.color-preview');

                $picker.colorpicker({
                    format: 'hex',
                    color: $input.val() || '#119A48'
                }).on('colorpickerChange colorpickerCreate', function(e) {
                    $preview.css('background-color', e.color.toString());
                    $input.val(e.color.toString());
                    
                    // Update gradient preview if this is a gradient color picker
                    if ($picker.attr('id') === 'gradientStartPicker' || $picker.attr('id') === 'gradientEndPicker') {
                        updateGradientPreview();
                    }
                });
            });

            // Update gradient preview function
            function updateGradientPreview() {
                var startColor = $('#gradientStartPicker input').val() || '#119A48';
                var endColor = $('#gradientEndPicker input').val() || '#16c653';
                $('#gradientPreview').css('background', 'linear-gradient(135deg, ' + startColor + ' 0%, ' + endColor + ' 100%)');
            }

            // Initialize gradient preview on page load
            updateGradientPreview();

            // Update gradient preview when inputs change
            $('#gradientStartPicker input, #gradientEndPicker input').on('change', function() {
                updateGradientPreview();
            });

            // Image preview on file selection
            $('#logo, #favicon, #spotlight_banner').on('change', function(e) {
                var file = e.target.files[0];
                if (file) {
                    var reader = new FileReader();
                    var $input = $(this);
                    reader.onload = function(e) {
                        var previewHtml = '<div class="image-preview mt-2"><img src="' + e.target.result + '" alt="Preview"></div>';
                        $input.siblings('.image-preview').remove();
                        $input.after(previewHtml);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
