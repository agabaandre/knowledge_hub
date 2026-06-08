@php
    $hostsValue = is_array($site['hosts'] ?? null) ? implode("\n", $site['hosts']) : (string) ($site['hosts'] ?? '');
@endphp
<div class="integration-row allowed-site-row">
    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-allowed-site btn-remove" title="{{ __('admin_nav.ai_remove_allowed_site') }}">
        <i class="fa fa-times"></i>
    </button>
    <input type="hidden" name="ai_search_allowed_sites[{{ $index }}][id]" value="{{ $site['id'] ?? '' }}">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_label') }}</label>
            <input type="text" name="ai_search_allowed_sites[{{ $index }}][label]" class="form-control form-control-sm"
                   value="{{ $site['label'] ?? '' }}" placeholder="{{ __('admin_nav.ai_allowed_site_label_placeholder') }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_serper_mode') }}</label>
            <select name="ai_search_allowed_sites[{{ $index }}][serper_mode]" class="form-select form-select-sm">
                <option value="web" @selected(($site['serper_mode'] ?? 'web') === 'web')>{{ __('admin_nav.ai_allowed_site_mode_web') }}</option>
                <option value="scholar" @selected(($site['serper_mode'] ?? '') === 'scholar')>{{ __('admin_nav.ai_allowed_site_mode_scholar') }}</option>
                <option value="none" @selected(($site['serper_mode'] ?? '') === 'none')>{{ __('admin_nav.ai_allowed_site_mode_none') }}</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <input type="hidden" name="ai_search_allowed_sites[{{ $index }}][enabled]" value="0">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="ai_search_allowed_sites[{{ $index }}][enabled]" value="1"
                       id="allowed_site_enabled_{{ $index }}" @checked($site['enabled'] ?? true)>
                <label class="form-check-label" for="allowed_site_enabled_{{ $index }}">{{ __('admin_nav.ai_allowed_site_enabled') }}</label>
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_hosts') }}</label>
            <textarea name="ai_search_allowed_sites[{{ $index }}][hosts]" class="form-control form-control-sm" rows="3"
                      placeholder="{{ __('admin_nav.ai_allowed_site_hosts_placeholder') }}" required>{{ $hostsValue }}</textarea>
            <small class="text-muted">{{ __('admin_nav.ai_allowed_site_hosts_hint') }}</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_search_url') }}</label>
            <input type="url" name="ai_search_allowed_sites[{{ $index }}][search_url]" class="form-control form-control-sm"
                   value="{{ $site['search_url'] ?? '' }}" placeholder="https://example.com/search?q={query}" required>
            <small class="text-muted">{{ __('admin_nav.ai_allowed_site_search_url_hint') }}</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_site_search') }}</label>
            <input type="text" name="ai_search_allowed_sites[{{ $index }}][site_search]" class="form-control form-control-sm"
                   value="{{ $site['site_search'] ?? '' }}" placeholder="site:pubmed.ncbi.nlm.nih.gov">
            <small class="text-muted">{{ __('admin_nav.ai_allowed_site_site_search_hint') }}</small>
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_icon') }}</label>
            <input type="text" name="ai_search_allowed_sites[{{ $index }}][icon]" class="form-control form-control-sm"
                   value="{{ $site['icon'] ?? 'fa-link' }}" placeholder="fa-link">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('admin_nav.ai_allowed_site_snippet') }}</label>
            <input type="text" name="ai_search_allowed_sites[{{ $index }}][snippet]" class="form-control form-control-sm"
                   value="{{ $site['snippet'] ?? '' }}" placeholder="{{ __('admin_nav.ai_allowed_site_snippet_placeholder') }}">
        </div>
    </div>
</div>
