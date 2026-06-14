<div class="tab-pane fade" id="navigation" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-bars"></i>
        Navigation
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Front nav style <span class="text-muted">(public)</span></label>
                <select name="nav_style" class="form-control">
                    <option value="colored" @if(($settings->nav_style ?? 'colored') === 'colored') selected @endif>Colored</option>
                    <option value="light" @if(($settings->nav_style ?? '') === 'light') selected @endif>Light</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Admin nav style</label>
                <select name="admin_nav_style" class="form-control">
                    <option value="colored" @if(($settings->admin_nav_style ?? 'colored') === 'colored') selected @endif>Colored</option>
                    <option value="light" @if(($settings->admin_nav_style ?? '') === 'light') selected @endif>Light</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Nav font weight</label>
                <select name="nav_font_weight" class="form-control">
                    @foreach([['400', 'Normal'], ['500', 'Medium'], ['600', 'Semibold'], ['700', 'Bold']] as $opt)
                        <option value="{{ $opt[0] }}" @if(($settings->nav_font_weight ?? '500') == $opt[0]) selected @endif>{{ $opt[1] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-3">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'nav_link_color',
                'label' => 'Nav link color',
                'value' => $settings->nav_link_color ?? '',
                'placeholder' => ($settings->nav_style ?? 'colored') === 'light' ? '#334155' : '#ffffff',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'nav_link_hover_color',
                'label' => 'Nav link hover',
                'value' => $settings->nav_link_hover_color ?? '',
                'placeholder' => ($settings->nav_style ?? 'colored') === 'light' ? '#119A48' : '#e2e8f0',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'nav_link_active_color',
                'label' => 'Nav link active',
                'value' => $settings->nav_link_active_color ?? '',
                'placeholder' => $settings->primary_color ?? '#119A48',
            ])
        </div>
    </div>

    <div class="row settings-grid-row mt-3">
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="menu_icons_enabled" name="menu_icons_enabled" value="1" @if($settings->menu_icons_enabled) checked @endif>
                    <label class="form-check-label" for="menu_icons_enabled">Show icons in main menu</label>
                </div>
                <small class="info-text">Icons above nav labels on front header and admin sidebar.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <input type="hidden" name="translate_button_filled" value="0">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="translate_button_filled" name="translate_button_filled" value="1" @if(settings()->translate_button_filled ?? true) checked @endif>
                    <label class="form-check-label" for="translate_button_filled">Filled translate button</label>
                </div>
                <small class="info-text">Primary color background on language selector.</small>
            </div>
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'translate_button_text_color',
                'label' => 'Translate button text color',
                'value' => settings()->translate_button_text_color ?? '#ffffff',
            ])
        </div>
    </div>
</div>
