<div class="tab-pane fade show active" id="general" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-bullhorn"></i>
        Basic Information
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Site Name <span class="text-danger">*</span></label>
                <input type="text" name="site_name" value="{{ $settings->site_name }}" class="form-control" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="{{ $settings->title }}" class="form-control">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Language</label>
                <input type="text" name="language" value="{{ $settings->language }}" class="form-control" placeholder="en">
            </div>
        </div>
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Slogan</label>
                <input type="text" name="slogan" value="{{ $settings->slogan }}" class="form-control" placeholder="Your site slogan">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Config name</label>
                <input type="text" name="config_name" class="form-control" value="{{ settings()->config_name ?? 'Default' }}" placeholder="e.g. Default, Theme1">
                <small class="info-text">Identifier for this theme configuration.</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Site Theme</label>
                <select class="form-control" name="site_theme">
                    <option value="" @if ((settings()->site_theme ?? '') == '') selected @endif>Default Theme</option>
                    <option value="theme1." @if ((settings()->site_theme ?? '') == 'theme1.') selected @endif>Theme1</option>
                    <option value="et" @if ((settings()->site_theme ?? '') == 'et') selected @endif>ET</option>
                </select>
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

    <div class="form-group">
        <label class="d-block">SEO-friendly URLs</label>
        <input type="hidden" name="use_seo_friendly_urls" value="0">
        <label class="mb-0">
            <input type="checkbox" name="use_seo_friendly_urls" value="1" {{ ($settings->use_seo_friendly_urls ?? true) ? 'checked' : '' }}>
            Use title-based URLs for publications, forums, communities, tags, health topics, and member states
        </label>
        <small class="info-text d-block">When enabled, links use readable slugs and legacy ID URLs redirect to the slug URL.</small>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-spinner"></i>
        Page preloader
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="d-block">Enable preloader</label>
                <input type="hidden" name="preloader_enabled" value="0">
                <label class="mb-0">
                    <input type="checkbox" name="preloader_enabled" value="1"
                        {{ ($settings->preloader_enabled ?? true) ? 'checked' : '' }}>
                    Show loading indicator while page content loads
                </label>
                <small class="info-text d-block">Header and footer stay visible; the spinner is centered in the main content area.</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Preloader text</label>
                <input type="text" name="preloader_text" class="form-control" maxlength="120"
                       value="{{ $settings->preloader_text ?? 'Loading' }}" placeholder="Loading">
                <small class="info-text">Short label shown under the spinner (e.g. Loading, Please wait).</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Minimum display time (seconds)</label>
                <input type="number" name="preloader_min_seconds" class="form-control" min="0" max="30" step="1"
                       value="{{ (int) ($settings->preloader_min_seconds ?? 3) }}">
                <small class="info-text">How long the preloader stays visible at minimum (0–30). Default: 3 seconds.</small>
            </div>
        </div>
    </div>
</div>
