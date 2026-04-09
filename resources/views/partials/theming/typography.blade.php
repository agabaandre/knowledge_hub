@php
    $primaryFont = settings()->primary_font ?? '';
    $defaultFontColor = settings()->default_font_color ?? '#212529';
    $frontBodyFontSize = settings()->front_body_font_size ?? '14';
    $frontBodyFontSize = (is_numeric($frontBodyFontSize) && (int)$frontBodyFontSize >= 10 && (int)$frontBodyFontSize <= 24) ? (int)$frontBodyFontSize : 14;

    $fontStack = 'system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
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
        /* Bootstrap 5 / Nifty: UI text inherits Admin > Appearance primary font */
        --bs-font-sans-serif: var(--font-family-primary);
        --bs-body-font-family: var(--font-family-primary);
        --bs-btn-font-family: var(--font-family-primary);
        --nf-brand-font-family: var(--font-family-primary);
    }
    /* Apply settings font site-wide (icons keep their own font-family from icon CSS) */
    html {
        font-family: var(--font-family-primary) !important;
    }
    body {
        font-family: var(--font-family-primary) !important;
        color: var(--default-font-color) !important;
        font-size: var(--front-body-font-size) !important;
    }
    #main-wrapper,
    #root.root,
    .root,
    .content,
    .content__boxed,
    .content__wrap,
    .front-bg,
    .front-container {
        font-family: var(--font-family-primary) !important;
    }
    .h1, .h2, .h3, .h4, .h5, .h6,
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-family-primary) !important;
    }
</style>
