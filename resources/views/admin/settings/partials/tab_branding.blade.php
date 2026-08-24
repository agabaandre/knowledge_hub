@php
    $currentLogoFile = settings()->logo ? basename(parse_url(settings()->logo, PHP_URL_PATH)) : '';
    $currentFaviconFile = settings()->favicon ? basename(parse_url(settings()->favicon, PHP_URL_PATH)) : '';
    $currentBannerFile = settings()->spotlight_banner ? basename(parse_url(settings()->spotlight_banner, PHP_URL_PATH)) : '';
@endphp
<div class="tab-pane fade" id="branding" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-images"></i>
        Branding &amp; Spotlight
    </div>

    <div class="branding-section-intro">
        <i class="fa fa-info-circle"></i>
        Manage logo, favicon, and homepage spotlight imagery. Pick from the config gallery or upload new files. Settings are saved per theme when using Theme1.
    </div>

    <div class="row">
        @include('admin.settings.partials.asset_upload_card', [
            'galleryImages' => $configGalleryImages ?? [],
            'assetKey' => 'logo',
            'title' => 'Site Logo',
            'spec' => '500 × 230 px',
            'description' => 'Displayed in the site header and footer.',
            'fieldName' => 'logo',
            'existingFieldName' => 'logo_existing',
            'inputId' => 'logo',
            'existingId' => 'logo_existing',
            'currentUrl' => settings()->logo,
            'currentFile' => $currentLogoFile,
            'colClass' => 'col-lg-6',
        ])
        @include('admin.settings.partials.asset_upload_card', [
            'galleryImages' => $configGalleryImages ?? [],
            'assetKey' => 'favicon',
            'title' => 'Favicon',
            'spec' => '350 × 350 px',
            'description' => 'Browser tab icon shown across the site.',
            'fieldName' => 'favicon',
            'existingFieldName' => 'favicon_existing',
            'inputId' => 'favicon',
            'existingId' => 'favicon_existing',
            'currentUrl' => settings()->favicon,
            'currentFile' => $currentFaviconFile,
            'previewClass' => 'branding-asset-preview--square',
            'colClass' => 'col-lg-6',
        ])
    </div>

    <div class="branding-options-panel">
        <h4 class="branding-options-panel__title">
            <i class="fa fa-sliders"></i>Logo display options
        </h4>
        <div class="row settings-grid-row">
            <div class="col-md-4">
                <div class="branding-toggle-card h-100">
                    <label class="branding-field-label" for="logo_scale">Logo size</label>
                    <select name="logo_scale" id="logo_scale" class="form-control">
                        @foreach([40, 50, 60, 70, 80, 100, 120] as $px)
                            <option value="{{ $px }}" @if((settings()->logo_scale ?? 80) == $px) selected @endif>{{ $px }}px height</option>
                        @endforeach
                    </select>
                    <small class="info-text d-block mt-2">Header and footer logo height.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="branding-toggle-card h-100">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="header_logo_inverse" name="header_logo_inverse" value="1" @if(settings()->header_logo_inverse ?? false) checked @endif>
                        <label class="form-check-label" for="header_logo_inverse">Inverse logo in header</label>
                    </div>
                    <small class="info-text">Light/inverted logo in the header.</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="branding-toggle-card h-100">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="footer_logo_inverse" name="footer_logo_inverse" value="1" @if(settings()->footer_logo_inverse ?? false) checked @endif>
                        <label class="form-check-label" for="footer_logo_inverse">Inverse logo in footer</label>
                    </div>
                    <small class="info-text">Light/inverted logo in the footer.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-handshake"></i>
        Partner logos
    </div>
    <p class="info-text mb-3">Shown as a centered row on a white bar above the public footer. Upload square or landscape logos (PNG or SVG with a transparent background works best).</p>
    <div class="branding-toggle-card mb-3">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="show_partner_names" name="show_partner_names" value="1" @if(settings()->show_partner_names ?? false) checked @endif>
            <label class="form-check-label" for="show_partner_names">Show partner names below logos</label>
        </div>
        <small class="info-text">Off by default. When enabled, the organisation name is shown under each logo.</small>
    </div>

    @php
        $partnerLogos = \App\Support\FooterPartners::items(settings()->partner_logos ?? []);
    @endphp
    <div id="partner-logos-list">
        @forelse($partnerLogos as $index => $partner)
            @include('admin.settings.partials.partner_logo_row', ['index' => $index, 'partner' => $partner])
        @empty
            @include('admin.settings.partials.partner_logo_row', ['index' => 0, 'partner' => ['file' => '', 'name' => '', 'url' => '', 'image' => '']])
        @endforelse
    </div>
    <button type="button" class="btn btn-outline-secondary btn-sm" id="js-add-partner-logo">
        <i class="fa fa-plus mr-1"></i>Add partner logo
    </button>
    <template id="partner-logo-row-template">
        @include('admin.settings.partials.partner_logo_row', ['index' => '__INDEX__', 'partner' => ['file' => '', 'name' => '', 'url' => '', 'image' => '']])
    </template>

    <div class="form-section-title mt-4">
        <i class="fa fa-image"></i>
        Homepage Spotlight
    </div>

    <div class="row">
        @include('admin.settings.partials.asset_upload_card', [
            'galleryImages' => $configGalleryImages ?? [],
            'assetKey' => 'spotlight_banner',
            'title' => 'Spotlight Banner Image',
            'spec' => '1894 × 658 px',
            'description' => 'Background image behind the homepage search area.',
            'fieldName' => 'spotlight_banner',
            'existingFieldName' => 'spotlight_banner_existing',
            'inputId' => 'spotlight_banner',
            'existingId' => 'spotlight_banner_existing',
            'currentUrl' => settings()->spotlight_banner,
            'currentFile' => $currentBannerFile,
            'previewClass' => 'branding-asset-preview--banner',
            'cardClass' => 'branding-asset-card--feature',
            'colClass' => 'col-lg-12',
            'infoText' => 'Recommended wide hero image. When unset, a color gradient is used instead.',
        ])
    </div>

    <div class="row settings-grid-row mt-2">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'gradient_start_color',
                'id' => 'gradient_start_color',
                'pickerId' => 'gradientStartPicker',
                'label' => 'Gradient start color',
                'value' => $settings->gradient_start_color ?? '#119A48',
                'hint' => 'Used when no banner image is set.',
            ])
        </div>
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'gradient_end_color',
                'id' => 'gradient_end_color',
                'pickerId' => 'gradientEndPicker',
                'label' => 'Gradient end color',
                'value' => $settings->gradient_end_color ?? '#16c653',
                'hint' => 'Used when no banner image is set.',
            ])
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Gradient preview</label>
                <div class="gradient-preview" id="gradientPreview"></div>
            </div>
        </div>
    </div>

    <div class="row settings-grid-row mt-3">
        <div class="col-md-4">
            @include('admin.settings.partials.color_field', [
                'name' => 'spotlight_overlay_color',
                'label' => 'Spotlight overlay color',
                'value' => $settings->spotlight_overlay_color ?? '#000000',
                'hint' => 'Tint applied on top of the banner image.',
            ])
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Spotlight overlay darkness (%)</label>
                <input type="number"
                       name="spotlight_overlay_opacity"
                       min="0"
                       max="100"
                       step="1"
                       class="form-control"
                       value="{{ (int) ($settings->spotlight_overlay_opacity ?? 35) }}" />
                <small class="info-text">0 = no overlay, 100 = fully solid.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group settings-field--compact mb-0">
                <label>Footer style</label>
                <select class="form-control" name="footer_style">
                    <option value="light-footer" @if (settings()->footer_style == 'light-footer') selected @endif>Light</option>
                    <option value="dark-footer" @if (settings()->footer_style == 'dark-footer') selected @endif>Dark</option>
                </select>
            </div>
        </div>
    </div>

    <div class="branding-options-panel mt-4">
        <h4 class="branding-options-panel__title">
            <i class="fa fa-magic"></i> Spotlight motion
        </h4>
        <p class="info-text mb-3">Give the homepage hero a live feel used on modern marketing sites. Motion pauses automatically when the visitor prefers reduced motion.</p>
        <div class="row settings-grid-row">
            <div class="col-md-6">
                <div class="form-group settings-field--compact mb-0">
                    <label for="spotlight_animation">Animation type</label>
                    <select class="form-control" name="spotlight_animation" id="spotlight_animation">
                        @foreach(\App\Support\SpotlightBackground::animationOptions() as $option)
                            <option value="{{ $option['value'] }}"
                                    data-hint="{{ $option['hint'] }}"
                                    @if(($settings->spotlight_animation ?? 'none') === $option['value']) selected @endif>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                    <small class="info-text d-block mt-2" id="spotlight_animation_hint">
                        {{ collect(\App\Support\SpotlightBackground::animationOptions())->firstWhere('value', $settings->spotlight_animation ?? 'none')['hint'] ?? 'No motion. Best for accessibility and low-power devices.' }}
                    </small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group settings-field--compact mb-0">
                    <label for="spotlight_animation_speed">Motion speed</label>
                    <select class="form-control" name="spotlight_animation_speed" id="spotlight_animation_speed">
                        <option value="slow" @if(($settings->spotlight_animation_speed ?? 'medium') === 'slow') selected @endif>Slow</option>
                        <option value="medium" @if(($settings->spotlight_animation_speed ?? 'medium') === 'medium') selected @endif>Medium</option>
                        <option value="fast" @if(($settings->spotlight_animation_speed ?? 'medium') === 'fast') selected @endif>Fast</option>
                    </select>
                    <small class="info-text d-block mt-2">How quickly the chosen effect plays.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var select = document.getElementById('spotlight_animation');
        var hint = document.getElementById('spotlight_animation_hint');
        if (!select || !hint) return;
        select.addEventListener('change', function () {
            var option = select.options[select.selectedIndex];
            hint.textContent = option && option.getAttribute('data-hint') ? option.getAttribute('data-hint') : '';
        });
    })();
</script>
<script>
    (function () {
        var list = document.getElementById('partner-logos-list');
        var addBtn = document.getElementById('js-add-partner-logo');
        var template = document.getElementById('partner-logo-row-template');
        if (!list || !addBtn || !template) return;

        function nextIndex() {
            return Date.now();
        }

        function bindRow(row) {
            var file = row.querySelector('.js-partner-file');
            if (file && !file.dataset.bound) {
                file.dataset.bound = '1';
                file.addEventListener('change', function () {
                    var preview = row.querySelector('.js-partner-preview');
                    if (!file.files || !file.files[0] || !preview) return;
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        preview.innerHTML = '<img src="' + event.target.result + '" alt="">';
                    };
                    reader.readAsDataURL(file.files[0]);
                });
            }
            var remove = row.querySelector('.js-remove-partner-logo');
            if (remove && !remove.dataset.bound) {
                remove.dataset.bound = '1';
                remove.addEventListener('click', function () {
                    if (list.querySelectorAll('[data-partner-row]').length < 2) {
                        row.querySelectorAll('input').forEach(function (input) {
                            if (input.type === 'file') {
                                input.value = '';
                            } else {
                                input.value = '';
                            }
                        });
                        var preview = row.querySelector('.js-partner-preview');
                        if (preview) {
                            preview.innerHTML = '<span class="branding-asset-preview__placeholder">Logo</span>';
                        }
                        return;
                    }
                    row.remove();
                });
            }
        }

        list.querySelectorAll('[data-partner-row]').forEach(bindRow);

        addBtn.addEventListener('click', function () {
            var html = template.innerHTML.replace(/__INDEX__/g, String(nextIndex()));
            var wrap = document.createElement('div');
            wrap.innerHTML = html.trim();
            var row = wrap.firstElementChild;
            list.appendChild(row);
            bindRow(row);
        });
    })();
</script>
