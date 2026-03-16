@php
    $primaryFont = settings()->primary_font ?? '';
    $defaultFontColor = settings()->default_font_color ?? '#212529';
    $frontBodyFontSize = settings()->front_body_font_size ?? '14';
    $frontBodyFontSize = (is_numeric($frontBodyFontSize) && (int)$frontBodyFontSize >= 10 && (int)$frontBodyFontSize <= 24) ? (int)$frontBodyFontSize : 14;

    $fontStack = 'inherit';
    $customFontFamily = null;
    if ($primaryFont === 'univers_45_light') {
        $fontStack = '"Univers 45 Light", "Univers", sans-serif';
    } elseif ($primaryFont === 'arial') {
        $fontStack = 'Arial, Helvetica, sans-serif';
    } elseif ($primaryFont === 'times_new_roman') {
        $fontStack = '"Times New Roman", Times, serif';
    } elseif ($primaryFont === 'montserrat') {
        $fontStack = '"Montserrat", sans-serif';
    } elseif ($primaryFont === 'brandon_text') {
        $fontStack = '"Brandon Text", sans-serif';
    } elseif (str_starts_with($primaryFont, 'custom_') && \Illuminate\Support\Facades\Schema::hasTable('custom_fonts')) {
        $id = (int) str_replace('custom_', '', $primaryFont);
        $customFont = $id ? \App\Models\CustomFont::find($id) : null;
        if ($customFont) {
            $customFontFamily = $customFont->font_family;
            $fontStack = '"' . e($customFont->font_family) . '", sans-serif';
        }
    }
@endphp
@if($primaryFont === 'montserrat')
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endif
@if($customFontFamily && isset($customFont) && $customFont && ($customFont->font_files ?? null))
@php
    $srcs = [];
    foreach (['woff2', 'woff', 'ttf', 'otf'] as $ext) {
        if (!empty($customFont->font_files[$ext])) {
            $fmt = $ext === 'ttf' ? 'truetype' : ($ext === 'otf' ? 'opentype' : $ext);
            $srcs[] = 'url("' . asset('storage/uploads/fonts/' . $customFont->font_files[$ext]) . '") format("' . $fmt . '")';
        }
    }
@endphp
@if(count($srcs) > 0)
<style>
@font-face {
    font-family: "{{ $customFont->font_family }}";
    font-style: normal;
    font-weight: 300 700;
    font-display: swap;
    src: {!! implode(",\n    ", $srcs) !!};
}
</style>
@endif
@endif
<style>
    :root {
        --font-family-primary: {!! $fontStack !!};
        --default-font-color: {{ $defaultFontColor }};
        --front-body-font-size: {{ $frontBodyFontSize }}px;
    }
    /* Apply admin font and body font size to front end for all themes (default + theme1) */
    body {
        font-family: var(--font-family-primary) !important;
        color: var(--default-font-color) !important;
        font-size: var(--front-body-font-size) !important;
    }
    #main-wrapper,
    .content,
    .content__boxed,
    .content__wrap,
    .front-bg {
        font-family: var(--font-family-primary) !important;
    }
</style>
