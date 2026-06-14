@php
    $name = $name ?? '';
    $value = $value ?? '#000000';
    $label = $label ?? '';
    $hint = $hint ?? null;
    $id = $id ?? $name;
    $pickerId = $pickerId ?? null;
    $placeholder = $placeholder ?? '#119A48';
    $normalized = trim((string) $value);
    if ($normalized !== '' && ! str_starts_with($normalized, '#')) {
        $normalized = '#'.$normalized;
    }
    $nativeValue = preg_match('/^#[0-9A-Fa-f]{6}$/i', $normalized) ? $normalized : '#119A48';
@endphp
<div class="form-group settings-field--compact mb-0">
    <label for="{{ $id }}">{{ $label }}</label>
    @if($hint)
        <small class="form-text text-muted d-block mb-1">{!! $hint !!}</small>
    @endif
    <div class="input-group colorPicker settings-color-picker" @if($pickerId) id="{{ $pickerId }}" @endif>
        <input
            type="text"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $value }}"
            class="form-control settings-color-text"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            spellcheck="false"
        />
        <div class="input-group-append settings-color-picker__controls">
            <input
                type="color"
                class="settings-color-native"
                value="{{ $nativeValue }}"
                title="Pick a color"
                aria-label="Color picker for {{ strip_tags($label) }}"
            />
            <span class="input-group-text color-preview settings-color-preview" style="background-color: {{ $normalized !== '' ? $normalized : $nativeValue }}"></span>
        </div>
    </div>
</div>
