<div class="tab-pane fade" id="typography" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-font"></i>
        Typography
    </div>
    <small class="info-text mb-3 d-block">Primary font and text sizing. Saved per theme.</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Primary font</label>
                <select name="primary_font" id="primary_font" class="form-control">
                    <option value="">— Default (system) —</option>
                    <option value="univers_45_light" @if((settings()->primary_font ?? '') === 'univers_45_light') selected @endif>Univers 45 Light</option>
                    <option value="arial" @if((settings()->primary_font ?? '') === 'arial') selected @endif>Arial</option>
                    <option value="times_new_roman" @if((settings()->primary_font ?? '') === 'times_new_roman') selected @endif>Times New Roman</option>
                    <option value="montserrat" @if((settings()->primary_font ?? '') === 'montserrat') selected @endif>Montserrat</option>
                    <option value="brandon_text" @if((settings()->primary_font ?? '') === 'brandon_text') selected @endif>Brandon Text</option>
                    @if(isset($customFonts) && $customFonts->count() > 0)
                        <option disabled>— Custom fonts —</option>
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
                <label>Main navigation font size (px)</label>
                <input type="number" name="nav_font_size" value="{{ $settings->nav_font_size ?? '11' }}" class="form-control" min="9" max="16" step="1" placeholder="11">
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Front-end body font size (px)</label>
                <input type="number" name="front_body_font_size" value="{{ $settings->front_body_font_size ?? '14' }}" class="form-control" min="10" max="24" step="1" placeholder="14">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Admin body font size (px)</label>
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
            <form action="{{ route('admin.config.custom-font.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row settings-grid-row align-items-end">
                    <div class="col-md-3">
                        <label class="branding-field-label" for="font_name">Display name</label>
                        <input type="text" name="font_name" id="font_name" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-3">
                        <label class="branding-field-label" for="font_family">CSS family name</label>
                        <input type="text" name="font_family" id="font_family" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-4">
                        <label class="branding-field-label" for="font_files">Font files</label>
                        <div class="branding-upload-row">
                            <input type="file" name="font_files[]" id="font_files" accept=".woff,.woff2,.ttf,.otf">
                            <button type="button" class="branding-upload-btn js-branding-upload-trigger" data-target="font_files">
                                <i class="fa fa-upload mr-1"></i>Choose fonts
                            </button>
                            <span class="branding-upload-filename js-branding-filename" id="font_files-filename">No file chosen</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">Add font</button>
                    </div>
                </div>
            </form>
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
                                <form action="{{ route('admin.config.custom-font.delete', $cf->id) }}" method="post" class="d-inline" onsubmit="return confirm('Remove this font?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
