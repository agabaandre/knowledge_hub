@php
    $fields = $aiPage['fields'] ?? [];
@endphp
<input type="hidden" name="custom_integrations_submitted" value="1">

<div class="alert alert-light border mb-4">
    <i class="fa fa-info-circle text-primary me-2"></i>
    <strong>{{ __('admin_nav.ai_defaults_title') }}</strong>
    {{ __('admin_nav.ai_defaults_body') }}
    {{ __('admin_nav.ai_env_override_note') }}
</div>

{{-- Feature routing --}}
<div class="ai-section">
    <div class="ai-section-header">
        <h2><i class="fa fa-route me-2 text-primary"></i>{{ __('admin_nav.ai_feature_routing') }}</h2>
        <p>{{ __('admin_nav.ai_feature_routing_intro') }}</p>
    </div>
    <div class="ai-section-body">
        @foreach($aiPage['features'] as $feature)
            <div class="ai-feature-row">
                <div>
                    <div class="ai-feature-title">{{ $feature['label'] }}</div>
                    <p class="ai-feature-desc">{{ $feature['description'] }}</p>
                </div>
                <div>
                    <select name="ai_feature_routing[{{ $feature['key'] }}]" class="form-select form-select-sm">
                        @foreach($feature['provider_options'] as $providerId => $providerLabel)
                            <option value="{{ $providerId }}" @selected($feature['assigned_provider'] === $providerId)>
                                {{ $providerLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <span class="ai-status-pill {{ $feature['status'] === 'ready' ? 'ready' : 'unavailable' }}">
                        {{ $feature['status'] === 'ready' ? __('admin_nav.ai_status_ready') : __('admin_nav.ai_status_unavailable') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Built-in providers --}}
<div class="ai-section">
    <div class="ai-section-header">
        <h2><i class="fa fa-plug me-2 text-primary"></i>{{ __('admin_nav.ai_builtin_providers') }}</h2>
        <p>{{ __('admin_nav.ai_builtin_providers_intro') }}</p>
    </div>
    <div class="ai-section-body">
        @foreach($aiPage['builtin_providers'] as $provider)
            <div class="ai-provider-card">
                <div class="ai-provider-head">
                    <div class="ai-provider-brand">
                        <div class="ai-provider-icon" style="background: {{ $provider['color'] }};">
                            <i class="fa {{ $provider['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="ai-provider-name">{{ $provider['label'] }}</div>
                            <p class="ai-provider-desc">{{ $provider['description'] }}</p>
                            @if(!empty($provider['capabilities']))
                                <div class="ai-cap-tags">
                                    @foreach($provider['capabilities'] as $cap)
                                        @php $capMeta = config('ai.features.'.$cap); @endphp
                                        <span class="ai-cap-tag">{{ $capMeta['label'] ?? $cap }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="ai-status-pill {{ $provider['status'] }}">
                            @if($provider['status'] === 'ready')
                                {{ __('admin_nav.ai_status_ready') }}
                            @elseif($provider['status'] === 'incomplete')
                                {{ __('admin_nav.ai_status_incomplete') }}
                            @else
                                {{ __('admin_nav.ai_status_disabled') }}
                            @endif
                        </span>
                        <div class="form-check form-switch mb-0">
                            <input type="hidden" name="ai_{{ $provider['id'] }}_enabled" value="0">
                            <input type="checkbox" class="form-check-input" id="ai_{{ $provider['id'] }}_enabled"
                                   name="ai_{{ $provider['id'] }}_enabled" value="1"
                                   @checked($fields['ai_'.$provider['id'].'_enabled']['form_value'] ?? ($provider['id'] === 'openai' || $provider['id'] === 'chatpdf'))>
                        </div>
                    </div>
                </div>
                <div class="card-body border-top-0 pt-0 pb-3 px-3">
                    @if($provider['id'] === 'openai')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ai_openai_api_key">API key</label>
                                <input type="password" name="ai_openai_api_key" id="ai_openai_api_key" class="form-control form-control-sm" autocomplete="new-password"
                                       placeholder="{{ !empty($fields['ai_openai_api_key']['value']) ? '••••••••' : 'sk-…' }}">
                                <small class="text-muted">Env: <code>OPEN_API_KEY</code></small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ai_openai_model">Model</label>
                                <input type="text" name="ai_openai_model" id="ai_openai_model" class="form-control form-control-sm"
                                       value="{{ $fields['ai_openai_model']['form_value'] ?? 'gpt-3.5-turbo' }}">
                            </div>
                        </div>
                    @elseif($provider['id'] === 'chatpdf')
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="ai_chatpdf_api_key">API key</label>
                                <input type="password" name="ai_chatpdf_api_key" id="ai_chatpdf_api_key" class="form-control form-control-sm" autocomplete="new-password"
                                       placeholder="{{ !empty($fields['ai_chatpdf_api_key']['value']) ? '••••••••' : 'Paste key to set or update' }}">
                                <small class="text-muted">Env: <code>CHAT_PDF_API_KEY</code></small>
                            </div>
                        </div>
                    @elseif($provider['id'] === 'gemini')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ai_gemini_api_key">API key</label>
                                <input type="password" name="ai_gemini_api_key" id="ai_gemini_api_key" class="form-control form-control-sm" autocomplete="new-password"
                                       placeholder="{{ !empty($fields['ai_gemini_api_key']['value']) ? '••••••••' : 'Paste key' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ai_gemini_model">Model</label>
                                <input type="text" name="ai_gemini_model" id="ai_gemini_model" class="form-control form-control-sm"
                                       value="{{ $fields['ai_gemini_model']['form_value'] ?? 'gemini-1.5-flash' }}">
                            </div>
                        </div>
                    @elseif($provider['id'] === 'deepseek')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="ai_deepseek_api_key">API key</label>
                                <input type="password" name="ai_deepseek_api_key" id="ai_deepseek_api_key" class="form-control form-control-sm" autocomplete="new-password"
                                       placeholder="{{ !empty($fields['ai_deepseek_api_key']['value']) ? '••••••••' : 'Paste key' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ai_deepseek_model">Model</label>
                                <input type="text" name="ai_deepseek_model" id="ai_deepseek_model" class="form-control form-control-sm"
                                       value="{{ $fields['ai_deepseek_model']['form_value'] ?? 'deepseek-chat' }}">
                            </div>
                        </div>
                    @elseif($provider['id'] === 'custom')
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label" for="ai_custom_base_url">Base URL</label>
                                <input type="url" name="ai_custom_base_url" id="ai_custom_base_url" class="form-control form-control-sm"
                                       value="{{ $fields['ai_custom_base_url']['form_value'] ?? '' }}"
                                       placeholder="https://api.example.com/v1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ai_custom_api_key">API key</label>
                                <input type="password" name="ai_custom_api_key" id="ai_custom_api_key" class="form-control form-control-sm" autocomplete="new-password"
                                       placeholder="{{ !empty($fields['ai_custom_api_key']['value']) ? '••••••••' : 'Paste key' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="ai_custom_model">Model</label>
                                <input type="text" name="ai_custom_model" id="ai_custom_model" class="form-control form-control-sm"
                                       value="{{ $fields['ai_custom_model']['form_value'] ?? '' }}">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Custom integrations --}}
<div class="ai-section">
    <div class="ai-section-header d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h2><i class="fa fa-plus-circle me-2 text-primary"></i>{{ __('admin_nav.ai_custom_integrations') }}</h2>
            <p class="mb-0">{{ __('admin_nav.ai_custom_integrations_intro') }}</p>
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-custom-integration">
            <i class="fa fa-plus me-1"></i>{{ __('admin_nav.ai_add_integration') }}
        </button>
    </div>
    <div class="ai-section-body">
        <div id="custom-integrations-list">
            @forelse($aiPage['custom_integrations'] as $idx => $integration)
                @include('admin.courses.partials.ai_custom_integration_row', ['integration' => $integration, 'index' => $idx])
            @empty
                <p class="text-muted small mb-0" id="no-integrations-msg">{{ __('admin_nav.ai_no_custom_integrations') }}</p>
            @endforelse
        </div>
    </div>
</div>

<template id="custom-integration-template">
    @include('admin.courses.partials.ai_custom_integration_row', [
        'integration' => [
            'id' => '',
            'name' => '',
            'driver' => 'openai_compatible',
            'base_url' => '',
            'api_key' => '',
            'model' => '',
            'enabled' => true,
            'has_stored_key' => false,
        ],
        'index' => '__INDEX__',
    ])
</template>
