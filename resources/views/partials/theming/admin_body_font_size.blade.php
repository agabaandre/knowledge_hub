@php
    $adminBodyFontSize = settings()->admin_body_font_size ?? '14';
    $adminBodyFontSize = (is_numeric($adminBodyFontSize) && (int)$adminBodyFontSize >= 10 && (int)$adminBodyFontSize <= 24) ? (int)$adminBodyFontSize : 14;
@endphp
<style>
    :root { --admin-body-font-size: {{ $adminBodyFontSize }}px; }
    body { font-size: var(--admin-body-font-size) !important; }
</style>
