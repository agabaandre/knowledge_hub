@php
    $primaryFont = settings()->primary_font ?? '';
    $defaultFontColor = settings()->default_font_color ?? '#212529';
    $frontBodyFontSize = settings()->front_body_font_size ?? '14';
    $frontBodyFontSize = (is_numeric($frontBodyFontSize) && (int)$frontBodyFontSize >= 10 && (int)$frontBodyFontSize <= 24) ? (int)$frontBodyFontSize : 14;
    $navFontSize = settings()->nav_font_size ?? '11';
    $navFontSize = (is_numeric($navFontSize) && (int)$navFontSize >= 9 && (int)$navFontSize <= 16) ? (int)$navFontSize : 11;
    $menuIconsEnabled = (bool) (settings()->menu_icons_enabled ?? 0);

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
        --nav-font-size: {{ $navFontSize }}px;
        --nav-icon-size: {{ $navFontSize + 5 }}px;
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
    /* Main navigation typography (front + admin) */
    .kh-linkedin-nav .kh-nav-label,
    #header .navbar .nav-link,
    .navigation .nav-menu > li > a,
    .navigation .nav-dropdown a,
    #mainnav-container .mainnav__menu .nav-link,
    #mainnav-container .mainnav__menu .nav-label,
    #mainnav-container .mainnav__menu a {
        font-size: var(--nav-font-size, 11px) !important;
        font-weight: var(--nav-font-weight, 500);
    }
    .kh-linkedin-nav .kh-nav-icon {
        font-size: var(--nav-icon-size, 16px);
        font-weight: 400;
        opacity: 0.88;
    }
    html.menu-icons-disabled .kh-linkedin-nav .kh-nav-icon,
    html.menu-icons-disabled #mainnav-container .mainnav__menu .nav-link > i.fa,
    html.menu-icons-disabled #mainnav-container .mainnav__menu .mininav-toggle > i.fa {
        display: none !important;
    }
    html.menu-icons-disabled #mainnav-container .mininav-toggle .nav-label {
        margin-left: 0 !important;
    }
</style>
