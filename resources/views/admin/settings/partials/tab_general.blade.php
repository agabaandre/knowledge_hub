<div class="tab-pane fade show active" id="general" role="tabpanel">
    <div class="form-section-title">
        <i class="fa fa-bullhorn"></i>
        {{ __('admin_configure.basic_information') }}
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('admin_configure.site_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="site_name" value="{{ $settings->site_name }}" class="form-control" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('admin_configure.title') }}</label>
                <input type="text" name="title" value="{{ $settings->title }}" class="form-control">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                @php
                    $siteLanguageOptions = $siteLanguageOptions ?? \App\Models\SiteLanguage::selectorMap();
                    $systemDefaultLocale = \App\Models\SiteLanguage::systemDefaultLocale();
                    // DB is source of truth (never prefer flashed old() after a successful save).
                    $selectedLanguage = \App\Models\SiteLanguage::storedSiteLanguage()
                        ?? \App\Models\SiteLanguage::normalizeLocaleCode(
                            is_string($settings->language ?? null) ? $settings->language : null
                        )
                        ?? $systemDefaultLocale;
                @endphp
                <label for="setting_language">{{ __('admin_configure.default_language') }}</label>
                {{-- Hidden carries the value: native <select> inside display:contents forms / Select2 can be dropped from POST.
                     Use classic selected= (Laravel 8 has no @selected) — same pattern as permissions/profile lang select. --}}
                <input type="hidden" name="language" id="setting_language_value" value="{{ $selectedLanguage }}" form="settings-main-form">
                <select id="setting_language" class="form-control no-select2" form="settings-main-form"
                        data-sync-language-hidden="#setting_language_value">
                    @forelse($siteLanguageOptions as $code => $meta)
                        <option value="{{ $code }}" {{ (string) $code === (string) $selectedLanguage ? 'selected' : '' }}>
                            {{ ($meta['flag'] ?? '') ? $meta['flag'].' ' : '' }}{{ $meta['name'] ?? $code }} ({{ strtoupper($code) }})
                        </option>
                    @empty
                        <option value="en" {{ (string) $selectedLanguage === 'en' ? 'selected' : '' }}>English (EN)</option>
                    @endforelse
                </select>
                <small class="info-text d-block">
                    {!! __('admin_configure.default_language_help', [
                        'link' => '<a href="'.e(route('admin.site-languages.index')).'">'.e(__('admin_configure.site_languages_link')).'</a>',
                    ]) !!}
                </small>
            </div>
        </div>
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-6">
            <div class="form-group">
                <label>{{ __('admin_configure.slogan') }}</label>
                <input type="text" name="slogan" value="{{ $settings->slogan }}" class="form-control" placeholder="{{ __('admin_configure.slogan_placeholder') }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>{{ __('admin_configure.config_name') }}</label>
                <input type="text" name="config_name" class="form-control" value="{{ settings()->config_name ?? 'Default' }}" placeholder="{{ __('admin_configure.config_name_placeholder') }}">
                <small class="info-text">{{ __('admin_configure.config_name_help') }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>{{ __('admin_configure.site_theme') }}</label>
                <select class="form-control" name="site_theme">
                    <option value="" @if ((settings()->site_theme ?? '') == '') selected @endif>{{ __('admin_configure.default_theme') }}</option>
                    <option value="theme1." @if ((settings()->site_theme ?? '') == 'theme1.') selected @endif>{{ __('admin_configure.theme1') }}</option>
                    <option value="et" @if ((settings()->site_theme ?? '') == 'et') selected @endif>{{ __('admin_configure.theme_et') }}</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label>{{ __('admin_configure.site_description') }}</label>
        <textarea name="site_description" rows="4" class="form-control" placeholder="{{ __('admin_configure.site_description_placeholder') }}">{{ $settings->site_description }}</textarea>
        <small class="info-text">{{ __('admin_configure.site_description_help') }}</small>
    </div>

    <div class="form-group">
        <label>{{ __('admin_configure.seo_keywords') }}</label>
        <textarea name="seo_keywords" rows="3" class="form-control" placeholder="{{ __('admin_configure.seo_keywords_placeholder') }}">{{ $settings->seo_keywords }}</textarea>
        <small class="info-text">{{ __('admin_configure.seo_keywords_help') }}</small>
    </div>

    <div class="form-group">
        <label class="d-block">{{ __('admin_configure.seo_friendly_urls') }}</label>
        <input type="hidden" name="use_seo_friendly_urls" value="0">
        <label class="mb-0">
            <input type="checkbox" name="use_seo_friendly_urls" value="1" {{ ($settings->use_seo_friendly_urls ?? true) ? 'checked' : '' }}>
            {{ __('admin_configure.seo_friendly_urls_label') }}
        </label>
        <small class="info-text d-block">{{ __('admin_configure.seo_friendly_urls_help') }}</small>
    </div>

    <div class="form-section-title mt-4">
        <i class="fa fa-spinner"></i>
        {{ __('admin_configure.page_preloader') }}
    </div>

    <div class="row settings-grid-row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="d-block">{{ __('admin_configure.enable_preloader') }}</label>
                <input type="hidden" name="preloader_enabled" value="0">
                <label class="mb-0">
                    <input type="checkbox" name="preloader_enabled" value="1"
                        {{ ($settings->preloader_enabled ?? true) ? 'checked' : '' }}>
                    {{ __('admin_configure.enable_preloader_label') }}
                </label>
                <small class="info-text d-block">{{ __('admin_configure.enable_preloader_help') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('admin_configure.preloader_text') }}</label>
                <input type="text" name="preloader_text" class="form-control" maxlength="120"
                       value="{{ $settings->preloader_text ?? 'Loading' }}" placeholder="{{ __('admin_configure.preloader_text_placeholder') }}">
                <small class="info-text">{{ __('admin_configure.preloader_text_help') }}</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>{{ __('admin_configure.preloader_min_seconds') }}</label>
                <input type="number" name="preloader_min_seconds" class="form-control" min="0" max="30" step="1"
                       value="{{ (int) ($settings->preloader_min_seconds ?? 3) }}">
                <small class="info-text">{{ __('admin_configure.preloader_min_seconds_help') }}</small>
            </div>
        </div>
    </div>
</div>
