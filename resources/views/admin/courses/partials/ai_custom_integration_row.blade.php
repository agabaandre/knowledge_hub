<div class="integration-row">
    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-integration btn-remove" title="Remove">
        <i class="fa fa-times"></i>
    </button>
    <input type="hidden" name="custom_integrations[{{ $index }}][id]" value="{{ $integration['id'] ?? '' }}">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Integration name</label>
            <input type="text" name="custom_integrations[{{ $index }}][name]" class="form-control form-control-sm"
                   value="{{ $integration['name'] ?? '' }}" placeholder="e.g. Azure OpenAI, Local Ollama" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Driver</label>
            <select name="custom_integrations[{{ $index }}][driver]" class="form-select form-select-sm">
                <option value="openai_compatible" @selected(($integration['driver'] ?? '') === 'openai_compatible')>OpenAI-compatible</option>
                <option value="gemini" @selected(($integration['driver'] ?? '') === 'gemini')>Google Gemini</option>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <input type="hidden" name="custom_integrations[{{ $index }}][enabled]" value="0">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="custom_integrations[{{ $index }}][enabled]" value="1"
                       id="custom_enabled_{{ $index }}" @checked($integration['enabled'] ?? true)>
                <label class="form-check-label" for="custom_enabled_{{ $index }}">Enabled</label>
            </div>
        </div>
        <div class="col-md-6 integration-base-url-wrap">
            <label class="form-label">Base URL</label>
            <input type="url" name="custom_integrations[{{ $index }}][base_url]" class="form-control form-control-sm"
                   value="{{ $integration['base_url'] ?? '' }}" placeholder="https://api.example.com/v1">
            <small class="text-muted">Required for OpenAI-compatible APIs. Omit /chat/completions.</small>
        </div>
        <div class="col-md-6">
            <label class="form-label">API key</label>
            <input type="password" name="custom_integrations[{{ $index }}][api_key]" class="form-control form-control-sm" autocomplete="new-password"
                   placeholder="{{ !empty($integration['has_stored_key']) ? '••••••••' : 'Paste key to set or update' }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Model</label>
            <input type="text" name="custom_integrations[{{ $index }}][model]" class="form-control form-control-sm"
                   value="{{ $integration['model'] ?? '' }}" placeholder="model-name">
        </div>
    </div>
</div>
