{{-- Top-right file type icon + short label; color from Icon Font Color (icon_font_color). Toggle: show_publication_card_file_type_badge (default on). Parent: .pub-card-file-type-corner-wrap --}}
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
    if (str_contains($t, 'pdf')) {
        $icon = 'fa-file-pdf';
        $short = 'PDF';
    } elseif (str_contains($t, 'word') || str_contains($t, 'doc')) {
        $icon = 'fa-file-word';
        $short = 'WORD';
    } elseif (str_contains($t, 'excel') || str_contains($t, 'xls')) {
        $icon = 'fa-file-excel';
        $short = 'EXCEL';
    } elseif (str_contains($t, 'presentation') || str_contains($t, 'powerpoint') || str_contains($t, 'ppt')) {
        $icon = 'fa-file-powerpoint';
        $short = 'PPT';
    } elseif (str_contains($t, 'url') && str_contains($t, 'link')) {
        $icon = 'fa-link';
        $short = 'LINK';
    } elseif (str_contains($t, 'link')) {
        $icon = 'fa-link';
        $short = 'LINK';
    } elseif (str_contains($t, 'video')) {
        $icon = 'fa-file-video';
        $short = 'VIDEO';
    } elseif (str_contains($t, 'audio')) {
        $icon = 'fa-file-audio';
        $short = 'AUDIO';
    } elseif (str_contains($t, 'image') || str_contains($t, 'photo')) {
        $icon = 'fa-file-image';
    }
    $typeTitle = trim((string) ($pub->file_type->name ?? ''));
@endphp
@once
<style>
.pub-card-file-type-corner-wrap { position: relative !important; }
.pub-card-file-type-corner {
    position: absolute;
    top: 0.35rem;
    right: 0.45rem;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: 0.125rem;
    text-align: center;
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
    @if(strlen($short) >= 2 && strlen($short) <= 10)
        <span class="pub-card-file-type-corner__label" style="color: {{ e($iconColor) }};">{{ $short }}</span>
    @endif
</div>
@endif
