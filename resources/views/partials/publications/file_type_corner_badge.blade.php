{{-- Top-left file type icon; short label only for generic/“other” types (unknown name). Color from icon_font_color. Parent: .pub-card-file-type-corner-wrap --}}
@php
    $pub = $row ?? $publication ?? null;
    $showFileTypeBadge = settings()->show_publication_card_file_type_badge ?? true;
@endphp
@if($showFileTypeBadge && $pub && $pub->file_type)
@php
    $iconColor = settings()->icon_font_color ?? '#64748b';
    $t = strtolower((string) ($pub->file_type->name ?? ''));
    $icon = 'fa-file-o';
    $short = strtoupper(\Illuminate\Support\Str::limit(trim((string) $pub->file_type->name), 7, ''));
    $showTextLabel = false;
    if (str_contains($t, 'pdf')) {
        $icon = 'fa-file-pdf';
    } elseif (str_contains($t, 'word') || str_contains($t, 'doc')) {
        $icon = 'fa-file-word';
    } elseif (str_contains($t, 'excel') || str_contains($t, 'xls')) {
        $icon = 'fa-file-excel';
    } elseif (str_contains($t, 'presentation') || str_contains($t, 'powerpoint') || str_contains($t, 'ppt')) {
        $icon = 'fa-file-powerpoint';
    } elseif (str_contains($t, 'url') && str_contains($t, 'link')) {
        $icon = 'fa-link';
    } elseif (str_contains($t, 'link')) {
        $icon = 'fa-link';
    } elseif (str_contains($t, 'video')) {
        $icon = 'fa-file-video';
    } elseif (str_contains($t, 'audio')) {
        $icon = 'fa-file-audio';
    } elseif (str_contains($t, 'image') || str_contains($t, 'photo')) {
        $icon = 'fa-file-image';
    } else {
        $showTextLabel = strlen($short) >= 2 && strlen($short) <= 10;
    }
    $typeTitle = trim((string) ($pub->file_type->name ?? ''));
@endphp
@once
<style>
.pub-card-file-type-corner-wrap { position: relative !important; }
.pub-card-file-type-corner {
    position: absolute;
    top: 0.35rem;
    left: 0.45rem;
    right: auto;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 0.125rem;
    text-align: left;
    pointer-events: none;
    max-width: 4.25rem;
}
.pub-card-file-type-corner .pub-card-file-type-corner__icon {
    font-size: 1.5rem;
    line-height: 1;
    text-shadow: 0 0 1px rgba(255, 255, 255, 0.95);
}
.pub-card-file-type-corner .pub-card-file-type-corner__label {
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    line-height: 1.05;
    word-break: break-word;
}
</style>
@endonce
<div class="pub-card-file-type-corner" role="img" aria-label="File type: {{ e($typeTitle) }}">
    <i class="fa {{ $icon }} pub-card-file-type-corner__icon" style="color: {{ e($iconColor) }};" aria-hidden="true"></i>
    @if($showTextLabel)
        <span class="pub-card-file-type-corner__label" style="color: {{ e($iconColor) }};">{{ $short }}</span>
    @endif
</div>
@endif
