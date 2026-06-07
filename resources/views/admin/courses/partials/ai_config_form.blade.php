@if(!empty($aiFields))
    <p class="text-muted mb-4">
        Configure AI providers for chat, search, and document features. Enable only the providers you use.
        Database values <strong>override</strong> matching <code>.env</code> settings.
        If a provider is disabled or missing credentials, features that need it degrade gracefully.
    </p>

    <div class="row mb-4">
        <div class="col-md-6">
            <label for="ai_primary_provider" class="form-label">Primary chat provider</label>
            <select name="ai_primary_provider" id="ai_primary_provider" class="form-select">
                @foreach(['openai' => 'OpenAI', 'gemini' => 'Google Gemini', 'deepseek' => 'DeepSeek', 'custom' => 'Custom (OpenAI-compatible)'] as $key => $label)
                    <option value="{{ $key }}" @selected(($aiFields['ai_primary_provider']['form_value'] ?? 'openai') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <small class="text-muted">Used when multiple chat providers are enabled. Falls back to the first available provider.</small>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-robot me-2"></i>OpenAI</h5>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="ai_openai_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="ai_openai_enabled" name="ai_openai_enabled" value="1"
                       @checked($aiFields['ai_openai_enabled']['form_value'] ?? true)>
                <label class="form-check-label" for="ai_openai_enabled">Enabled</label>
            </div>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label for="ai_openai_api_key" class="form-label">API key</label>
                <input type="password" name="ai_openai_api_key" id="ai_openai_api_key" class="form-control" autocomplete="new-password"
                       placeholder="{{ !empty($aiFields['ai_openai_api_key']['value']) ? '••••••••' : 'sk-…' }}">
                <small class="text-muted">Env: <code>OPEN_API_KEY</code></small>
            </div>
            <div class="col-md-6">
                <label for="ai_openai_model" class="form-label">Model</label>
                <input type="text" name="ai_openai_model" id="ai_openai_model" class="form-control"
                       value="{{ $aiFields['ai_openai_model']['form_value'] ?? 'gpt-3.5-turbo' }}">
                <small class="text-muted">Env: <code>OPENAI_MODEL</code></small>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-file-pdf me-2"></i>ChatPDF</h5>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="ai_chatpdf_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="ai_chatpdf_enabled" name="ai_chatpdf_enabled" value="1"
                       @checked($aiFields['ai_chatpdf_enabled']['form_value'] ?? true)>
                <label class="form-check-label" for="ai_chatpdf_enabled">Enabled</label>
            </div>
        </div>
        <div class="card-body">
            <label for="ai_chatpdf_api_key" class="form-label">API key</label>
            <input type="password" name="ai_chatpdf_api_key" id="ai_chatpdf_api_key" class="form-control" autocomplete="new-password"
                   placeholder="{{ !empty($aiFields['ai_chatpdf_api_key']['value']) ? '••••••••' : 'Paste key to set or update' }}">
            <small class="text-muted">Env: <code>CHAT_PDF_API_KEY</code>. PDF chat features require this provider.</small>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-gem me-2"></i>Google Gemini</h5>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="ai_gemini_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="ai_gemini_enabled" name="ai_gemini_enabled" value="1"
                       @checked($aiFields['ai_gemini_enabled']['form_value'] ?? false)>
                <label class="form-check-label" for="ai_gemini_enabled">Enabled</label>
            </div>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label for="ai_gemini_api_key" class="form-label">API key</label>
                <input type="password" name="ai_gemini_api_key" id="ai_gemini_api_key" class="form-control" autocomplete="new-password"
                       placeholder="{{ !empty($aiFields['ai_gemini_api_key']['value']) ? '••••••••' : 'Paste key to set or update' }}">
                <small class="text-muted">Env: <code>GEMINI_API_KEY</code></small>
            </div>
            <div class="col-md-6">
                <label for="ai_gemini_model" class="form-label">Model</label>
                <input type="text" name="ai_gemini_model" id="ai_gemini_model" class="form-control"
                       value="{{ $aiFields['ai_gemini_model']['form_value'] ?? 'gemini-1.5-flash' }}">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-bolt me-2"></i>DeepSeek</h5>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="ai_deepseek_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="ai_deepseek_enabled" name="ai_deepseek_enabled" value="1"
                       @checked($aiFields['ai_deepseek_enabled']['form_value'] ?? false)>
                <label class="form-check-label" for="ai_deepseek_enabled">Enabled</label>
            </div>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label for="ai_deepseek_api_key" class="form-label">API key</label>
                <input type="password" name="ai_deepseek_api_key" id="ai_deepseek_api_key" class="form-control" autocomplete="new-password"
                       placeholder="{{ !empty($aiFields['ai_deepseek_api_key']['value']) ? '••••••••' : 'Paste key to set or update' }}">
                <small class="text-muted">Env: <code>DEEPSEEK_API_KEY</code></small>
            </div>
            <div class="col-md-6">
                <label for="ai_deepseek_model" class="form-label">Model</label>
                <input type="text" name="ai_deepseek_model" id="ai_deepseek_model" class="form-control"
                       value="{{ $aiFields['ai_deepseek_model']['form_value'] ?? 'deepseek-chat' }}">
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fa fa-code me-2"></i>Custom (OpenAI-compatible)</h5>
            <div class="form-check form-switch mb-0">
                <input type="hidden" name="ai_custom_enabled" value="0">
                <input type="checkbox" class="form-check-input" id="ai_custom_enabled" name="ai_custom_enabled" value="1"
                       @checked($aiFields['ai_custom_enabled']['form_value'] ?? false)>
                <label class="form-check-label" for="ai_custom_enabled">Enabled</label>
            </div>
        </div>
        <div class="card-body row g-3">
            <div class="col-md-12">
                <label for="ai_custom_base_url" class="form-label">Base URL</label>
                <input type="url" name="ai_custom_base_url" id="ai_custom_base_url" class="form-control"
                       value="{{ $aiFields['ai_custom_base_url']['form_value'] ?? '' }}"
                       placeholder="https://api.example.com/v1">
                <small class="text-muted">Env: <code>AI_CUSTOM_BASE_URL</code> — OpenAI-compatible API root (without <code>/chat/completions</code>).</small>
            </div>
            <div class="col-md-6">
                <label for="ai_custom_api_key" class="form-label">API key</label>
                <input type="password" name="ai_custom_api_key" id="ai_custom_api_key" class="form-control" autocomplete="new-password"
                       placeholder="{{ !empty($aiFields['ai_custom_api_key']['value']) ? '••••••••' : 'Paste key to set or update' }}">
            </div>
            <div class="col-md-6">
                <label for="ai_custom_model" class="form-label">Model name</label>
                <input type="text" name="ai_custom_model" id="ai_custom_model" class="form-control"
                       value="{{ $aiFields['ai_custom_model']['form_value'] ?? '' }}"
                       placeholder="my-model">
            </div>
        </div>
    </div>
@else
    <div class="alert alert-warning">
        AI configuration columns are missing from the database. Run <code>php artisan migrate</code> on this server.
    </div>
@endif
