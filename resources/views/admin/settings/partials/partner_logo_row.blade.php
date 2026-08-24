@php
    $index = $index ?? 0;
    $partner = $partner ?? ['file' => '', 'name' => '', 'url' => '', 'image' => ''];
@endphp
<div class="partner-logo-row branding-options-panel mb-2" data-partner-row>
    <div class="row align-items-center settings-grid-row">
        <div class="col-md-2">
            <div class="branding-asset-preview branding-asset-preview--square js-partner-preview mb-2 mb-md-0" style="width:72px;height:72px;">
                @if(!empty($partner['image']))
                    <img src="{{ $partner['image'] }}" alt="">
                @else
                    <span class="branding-asset-preview__placeholder">Logo</span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <label class="branding-field-label">Logo</label>
            <input type="hidden" name="partner_logos[{{ $index }}][file]" value="{{ $partner['file'] ?? '' }}">
            <input type="file" name="partner_logos[{{ $index }}][image]" accept="image/*" class="form-control js-partner-file">
        </div>
        <div class="col-md-3">
            <label class="branding-field-label">Name</label>
            <input type="text" name="partner_logos[{{ $index }}][name]" class="form-control" value="{{ $partner['name'] ?? '' }}" placeholder="Organisation" maxlength="120">
        </div>
        <div class="col-md-3">
            <label class="branding-field-label">Website (optional)</label>
            <input type="url" name="partner_logos[{{ $index }}][url]" class="form-control" value="{{ $partner['url'] ?? '' }}" placeholder="https://">
        </div>
        <div class="col-md-1 text-md-right pt-3">
            <button type="button" class="btn btn-link text-danger js-remove-partner-logo" title="Remove">&times;</button>
        </div>
    </div>
</div>
