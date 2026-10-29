@php
    /** @var string $name Field name, e.g. ai_openai_model */
    /** @var string $current Current model id */
    /** @var string $provider Catalog key: openai|claude|gemini|deepseek */
    $catalog = \App\Support\AiConfig::modelCatalog($provider);
    $current = trim((string) $current);
    $selected = array_key_exists($current, $catalog) ? $current : '';
@endphp
<div class="ai-model-picker" data-ai-model-picker>
    <label class="form-label" for="{{ $name }}_select">Model</label>
    <select name="{{ $name }}_select" id="{{ $name }}_select" class="form-select form-select-sm ai-model-select">
        <option value="">{{ ui_t('admin_nav.ai_model_custom_option', 'Custom / type below') }}</option>
        @foreach($catalog as $modelId => $modelLabel)
            <option value="{{ $modelId }}" @selected($selected === $modelId)>{{ $modelLabel }} ({{ $modelId }})</option>
        @endforeach
    </select>
    <label class="form-label mt-2 mb-1" for="{{ $name }}">
        <small class="text-muted">{{ ui_t('admin_nav.ai_model_custom_label', 'Custom model ID (used when dropdown is empty)') }}</small>
    </label>
    <input type="text" name="{{ $name }}" id="{{ $name }}" class="form-control form-control-sm ai-model-custom"
           value="{{ $current }}"
           placeholder="{{ ui_t('admin_nav.ai_model_custom_placeholder', 'e.g. provider-specific model id') }}">
</div>
