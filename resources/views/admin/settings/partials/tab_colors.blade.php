<div class="tab-pane fade" id="colors" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-palette"></i>
        {{ __('admin_configure.tab_colors') }}
    </div>
    <small class="info-text mb-3 d-block">{{ __('admin_configure.colors_intro') }}</small>

    <div class="form-section-subtitle mb-2">{{ __('admin_configure.brand_colors') }}</div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'primary_color',
                'label' => __('admin_configure.primary_color'),
                'value' => $settings->primary_color ?? '#119A48',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'secondary_color',
                'label' => __('admin_configure.secondary_color'),
                'value' => $settings->secondary_color,
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'primary_text_color',
                'label' => __('admin_configure.primary_text_color'),
                'value' => $settings->primary_text_color,
            ])
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'links_active_color',
                'label' => __('admin_configure.links_active_color'),
                'value' => $settings->links_active_color,
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'icon_font_color',
                'label' => 'Icon font color',
                'value' => $settings->icon_font_color,
                'hint' => 'File-type icons and accents. Default Agenda 2063 green <code>#007749</code>.',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'banner_text',
                'label' => 'Banner text color',
                'value' => $settings->banner_text ?? '#FFFFFF',
            ])
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-flag"></i>
        AU Official Colors
    </div>
    <small class="info-text mb-3 d-block">{{ __('admin_configure.au_colors_intro') }}</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_red',
                'label' => 'AU Red (PANTONE 7420 C)',
                'value' => $settings->au_red ?? '#9F2241',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_gold',
                'label' => 'AU Gold (PANTONE 4515 C)',
                'value' => $settings->au_gold ?? '#B4A269',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_corporate_green',
                'label' => 'AU Corporate Green',
                'value' => $settings->au_corporate_green ?? '#1A5632',
            ])
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_green',
                'label' => 'AU Green (PANTONE 7740 C)',
                'value' => $settings->au_green ?? '#1A5632',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_plum',
                'label' => 'Agenda 2063 Plum',
                'value' => $settings->au_plum ?? '#522B39',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_grey_text',
                'label' => 'Grey text (PANTONE 425 C)',
                'value' => $settings->au_grey_text ?? '#58595B',
            ])
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'au_white',
                'label' => 'Bright white',
                'value' => $settings->au_white ?? '#FFFFFF',
            ])
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-font"></i>
        Typography
    </div>
    <small class="info-text mb-3 d-block">{{ __('admin_configure.typography_intro') }}</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>{{ __('admin_configure.primary_font') }}</label>
                <select name="primary_font" id="primary_font" class="form-control">
                    <option value="">— Default (system) —</option>
                    <option value="univers_45_light" @if((settings()->primary_font ?? '') === 'univers_45_light') selected @endif>Univers 45 Light</option>
                    <option value="arial" @if((settings()->primary_font ?? '') === 'arial') selected @endif>Arial</option>
                    <option value="times_new_roman" @if((settings()->primary_font ?? '') === 'times_new_roman') selected @endif>Times New Roman</option>
                    <option value="montserrat" @if((settings()->primary_font ?? '') === 'montserrat') selected @endif>Montserrat</option>
                    <option value="brandon_text" @if((settings()->primary_font ?? '') === 'brandon_text') selected @endif>Brandon Text</option>
                    @if(isset($customFonts) && $customFonts->count() > 0)
                        <option disabled>— {{ __('admin_configure.custom_fonts') }} —</option>
                        @foreach($customFonts as $cf)
                            <option value="custom_{{ $cf->id }}" @if((settings()->primary_font ?? '') === 'custom_' . $cf->id) selected @endif>{{ $cf->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'default_font_color',
                'label' => 'Default font color',
                'value' => $settings->default_font_color ?? '#212529',
                'placeholder' => '#212529',
            ])
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>{{ __('admin_configure.nav_font_size') }}</label>
                <input type="number" name="nav_font_size" value="{{ $settings->nav_font_size ?? '11' }}" class="form-control" min="9" max="16" step="1" placeholder="11">
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>{{ __('admin_configure.front_body_font_size') }}</label>
                <input type="number" name="front_body_font_size" value="{{ $settings->front_body_font_size ?? '14' }}" class="form-control" min="10" max="24" step="1" placeholder="14">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>{{ __('admin_configure.admin_body_font_size') }}</label>
                <input type="number" name="admin_body_font_size" value="{{ $settings->admin_body_font_size ?? '14' }}" class="form-control" min="10" max="24" step="1" placeholder="14">
            </div>
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-upload"></i>
        Custom Fonts
    </div>
    <p class="text-muted small mb-3">Upload font files to add them to the Primary font dropdown. Formats: .woff2, .woff, .ttf, .otf</p>

    <div class="branding-asset-card branding-asset-card--feature mb-4">
        <div class="branding-asset-card__body">
            <div class="row settings-grid-row align-items-end">
                <div class="col-md-3">
                    <label class="branding-field-label" for="font_name">{{ __('admin_configure.display_name') }}</label>
                    <input type="text" name="font_name" id="font_name" class="form-control" placeholder="{{ __('admin_configure.optional') }}" form="settings-custom-font-upload-form">
                </div>
                <div class="col-md-3">
                    <label class="branding-field-label" for="font_family">{{ __('admin_configure.css_family_name') }}</label>
                    <input type="text" name="font_family" id="font_family" class="form-control" placeholder="{{ __('admin_configure.optional') }}" form="settings-custom-font-upload-form">
                </div>
                <div class="col-md-4">
                    <label class="branding-field-label" for="font_files">{{ __('admin_configure.font_files') }}</label>
                    <div class="branding-upload-row">
                        <input type="file" name="font_files[]" id="font_files" accept=".woff,.woff2,.ttf,.otf" form="settings-custom-font-upload-form">
                        <button type="button" class="branding-upload-btn js-branding-upload-trigger" data-target="font_files">
                            <i class="fa fa-upload mr-1"></i>Choose fonts
                        </button>
                        <span class="branding-upload-filename js-branding-filename" id="font_files-filename">No file chosen</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-block" form="settings-custom-font-upload-form">Add font</button>
                </div>
            </div>
        </div>
    </div>

    @if(isset($customFonts) && $customFonts->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Family</th>
                        <th>Files</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customFonts as $cf)
                        <tr>
                            <td>{{ $cf->name }}</td>
                            <td><code>{{ $cf->font_family }}</code></td>
                            <td>{{ $cf->font_files ? implode(', ', array_keys($cf->font_files)) : '—' }}</td>
                            <td class="text-right">
                                <button type="submit" class="btn btn-sm btn-outline-danger" form="custom-font-delete-{{ $cf->id }}" onclick="return confirm('Remove this font?');">{{ ui_t('general.remove', 'Remove') }}</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
