@php
    $assetKey = $assetKey ?? 'asset';
    $title = $title ?? 'Image';
    $spec = $spec ?? '';
    $description = $description ?? '';
    $fieldName = $fieldName ?? $assetKey;
    $existingFieldName = $existingFieldName ?? $fieldName.'_existing';
    $inputId = $inputId ?? $fieldName;
    $existingId = $existingId ?? $existingFieldName;
    $currentUrl = $currentUrl ?? null;
    $currentFile = $currentFile ?? '';
    $accept = $accept ?? 'image/*';
    $previewClass = $previewClass ?? '';
    $cardClass = $cardClass ?? '';
    $colClass = $colClass ?? 'col-lg-6';
    $galleryImages = $galleryImages ?? ($configGalleryImages ?? []);
    $infoText = $infoText ?? null;
@endphp
<div class="{{ $colClass }} mb-3 mb-lg-0">
    <div class="branding-asset-card {{ $cardClass }}" data-branding-asset="{{ $assetKey }}">
        <div class="branding-asset-card__header">
            <div>
                <h4 class="branding-asset-card__title">{{ $title }}</h4>
                @if($spec !== '')
                    <span class="branding-asset-spec"><i class="fa fa-arrows-alt"></i> {{ $spec }}</span>
                @endif
                @if($description !== '')
                    <small class="info-text d-block mt-2 mb-0">{{ $description }}</small>
                @endif
            </div>
            <div class="branding-asset-preview js-branding-preview {{ $previewClass }}">
                @if($currentUrl)
                    <img src="{{ $currentUrl }}" alt="{{ $title }} preview">
                @else
                    <span class="branding-asset-preview__placeholder">No image set</span>
                @endif
            </div>
        </div>
        <div class="branding-asset-card__body">
            <label class="branding-field-label" for="{{ $existingId }}">Config gallery</label>
            <select name="{{ $existingFieldName }}" id="{{ $existingId }}" class="form-control js-branding-gallery-select">
                <option value="">— Keep current / upload new —</option>
                @foreach($galleryImages as $f)
                    <option value="{{ $f }}" @if($f === $currentFile) selected @endif>{{ $f }}</option>
                @endforeach
            </select>

            <div class="branding-upload-row">
                <input type="file" name="{{ $fieldName }}" id="{{ $inputId }}" accept="{{ $accept }}">
                <button type="button" class="branding-upload-btn js-branding-upload-trigger" data-target="{{ $inputId }}">
                    <i class="fa fa-upload mr-1"></i>Choose file
                </button>
                <span class="branding-upload-filename js-branding-filename" id="{{ $inputId }}-filename">No file chosen</span>
            </div>
            @if($infoText)
                <small class="info-text d-block mt-2">{{ $infoText }}</small>
            @endif
        </div>
    </div>
</div>
