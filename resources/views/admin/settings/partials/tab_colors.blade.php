<div class="tab-pane fade" id="colors" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-palette"></i>
        Brand Colors
    </div>
    <small class="info-text mb-3 d-block">Primary palette and text accents. Type any hex value (e.g. <code>#119A48</code>) or use the color swatch. Saved per theme.</small>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'primary_color',
                'label' => 'Primary color',
                'value' => $settings->primary_color ?? '#119A48',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'secondary_color',
                'label' => 'Secondary color',
                'value' => $settings->secondary_color,
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'primary_text_color',
                'label' => 'Primary text color',
                'value' => $settings->primary_text_color,
            ])
        </div>
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'links_active_color',
                'label' => 'Links active color',
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
    <small class="info-text mb-3 d-block">African Union brand palette (PANTONE references).</small>

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
</div>
