{{-- Theme1: inherit colors and typography from Admin > Configure > Appearance --}}
@include('partials.theming.typography')
@php
    $primary = settings()->primary_color ?? '#006239';
    $secondary = settings()->secondary_color ?? '#413C3C';
    $textPrimary = settings()->primary_text_color ?? '#212529';
    $linksActive = settings()->links_active_color ?? $primary;
    $iconColor = settings()->icon_font_color ?? $textPrimary;
    $navStyle = settings()->nav_style ?? 'colored';
    $adminNavStyle = settings()->admin_nav_style ?? 'colored';
    $navLinkColor = settings()->nav_link_color ?? (($navStyle === 'light') ? '#334155' : 'rgba(255,255,255,0.92)');
    $navLinkHover = settings()->nav_link_hover_color ?? (($navStyle === 'light') ? ($primary) : '#fff');
    $navLinkActive = settings()->nav_link_active_color ?? $primary;
    $navFontWeight = settings()->nav_font_weight ?? '500';
    $h = ltrim($primary, '#');
    $primaryRgb = strlen($h) >= 6 ? hexdec(substr($h,0,2)).','.hexdec(substr($h,2,2)).','.hexdec(substr($h,4,2)) : '0,98,57';
    $h2 = ltrim($secondary, '#');
    $secondaryRgb = strlen($h2) >= 6 ? hexdec(substr($h2,0,2)).','.hexdec(substr($h2,2,2)).','.hexdec(substr($h2,4,2)) : '65,60,60';
@endphp
<style>
    :root, [data-bs-theme="light"] {
        --theme-color-primary: {{ $primary }};
        --theme-color-secondary: {{ $secondary }};
        --text-color-primary: {{ $textPrimary }};
        --text-color-secondary: {{ $linksActive }};
        --icon-color: {{ $iconColor }};
        --nav-link-color: {{ $navLinkColor }};
        --nav-link-hover-color: {{ $navLinkHover }};
        --nav-link-active-color: {{ $navLinkActive }};
        --bs-primary: {{ $primary }};
        --bs-primary-rgb: {{ $primaryRgb }};
        --bs-secondary: {{ $secondary }};
        --bs-secondary-rgb: {{ $secondaryRgb }};
    }
    .btn-primary { background-color: {{ $primary }} !important; border-color: {{ $primary }} !important; }
    .btn-primary:hover { filter: brightness(1.08); }
    .text-primary, a.text-primary { color: {{ $primary }} !important; }
    .bg-primary { background-color: {{ $primary }} !important; }
    /* Theme1 front nav: use configured nav colors and font-weight for all states */
    #header.nav-light .navbar .nav-link { color: {{ $navLinkColor }} !important; font-weight: {{ $navFontWeight }}; }
    #header.nav-light .navbar .nav-link:hover { color: {{ $navLinkHover }} !important; border-bottom-color: {{ $navLinkHover }} !important; }
    #header.nav-light .navbar .nav-link.active { color: {{ $navLinkActive }} !important; font-weight: 600; border-bottom-color: {{ $navLinkActive }} !important; }
    #header:not(.nav-light) .navbar .nav-link { color: {{ $navLinkColor }} !important; font-weight: {{ $navFontWeight }}; }
    #header:not(.nav-light) .navbar .nav-link:hover { color: {{ $navLinkHover }} !important; border-bottom-color: {{ $navLinkHover }} !important; }
    #header:not(.nav-light) .navbar .nav-link.active { color: {{ $navLinkActive }} !important; font-weight: 600; border-bottom-color: {{ $navLinkActive }} !important; }
    /* Colored nav: primary background; light nav: light background */
    #header.nav-colored { background: {{ $primary }} !important; border-bottom-color: rgba(0,0,0,0.1) !important; }
    #header.nav-colored .navbar-brand span { color: rgba(255,255,255,0.95) !important; }
    #header.nav-colored .btn-outline-primary { border-color: rgba(255,255,255,0.8); color: #fff !important; }
    #header.nav-colored .btn-outline-primary:hover { background: rgba(255,255,255,0.2); color: #fff !important; }
    #header.nav-colored .btn-primary { background: rgba(255,255,255,0.95) !important; color: {{ $primary }} !important; border-color: #fff !important; }
    #header.nav-light { background: #fff !important; }
    .nav-link.active { color: {{ $navLinkActive }} !important; font-weight: 600; }
    .navbar .nav-link.active { border-bottom-color: {{ $navLinkActive }}; }
    a:not(.btn):not(.nav-link).link-primary { color: {{ $primary }} !important; }
    /* Admin header: use admin_nav_style (admin panel only) */
    .header { background-color: {{ $adminNavStyle === 'light' ? '#f8fafc' : $primary }} !important; border-bottom: 1px solid {{ $adminNavStyle === 'light' ? '#e2e8f0' : 'transparent' }} !important; }
    .header .header__btn, .header .brand-title { color: {{ $adminNavStyle === 'light' ? '#334155' : 'rgba(255,255,255,0.9)' }} !important; }
    .header .searchbox__input { color: {{ $adminNavStyle === 'light' ? '#334155' : 'rgba(255,255,255,0.9)' }} !important; }
    .header .searchbox__input::placeholder { color: {{ $adminNavStyle === 'light' ? '#94a3b8' : 'rgba(255,255,255,0.6)' }} !important; }
    .header .psi-bell, .header .psi-magnifi-glass, .header .pli-magnifi-glass, .header .fa-ellipsis-v { color: {{ $adminNavStyle === 'light' ? '#334155' : 'rgba(255,255,255,0.9)' }} !important; }
    .header .cm-selected-icon { color: {{ $adminNavStyle === 'light' ? '#334155' : 'rgba(255,255,255,0.9)' }} !important; }
    /* Admin sidebar: dark background + white text so menu labels and links are readable */
    body #root.root #mainnav-container.mainnav,
    body #root.root #mainnav-container .mainnav__inner { background-color: {{ $secondary }} !important; }
    /* Side nav: use root-relative font size so it responds to browser/OS font size increase (accessibility) */
    #mainnav-container,
    #mainnav-container .mainnav__inner { font-size: 1rem !important; }
    #mainnav-container .nav-link,
    #mainnav-container .nav-label,
    #mainnav-container .mainnav__caption,
    #mainnav-container .mainnav__categoriy h6,
    #mainnav-container .mainnav__menu a { font-size: inherit !important; }
    #mainnav-container .mainnav__menu .fs-5,
    #mainnav-container .mainnav__menu i { font-size: 1em !important; }
    body #root.root {
        --nf-mainnav-bg: {{ $secondary }};
        --nf-mainnav-link-color: rgba(255,255,255,0.92);
        --nf-mainnav-link-hover: #fff;
        --nf-mainnav-link-active: #fff;
        --nf-mainnav-icon-color: rgba(255,255,255,0.92);
        --nf-mainnav-heading-color: rgba(255,255,255,0.98);
        --nf-mainnav-color: rgba(255,255,255,0.92);
        --nf-mainnav-submenu-active-color: #fff;
        --nf-mainnav-min-icon-color: rgba(255,255,255,0.92);
        --nf-mainnav-min-icon-active-color: #fff;
    }
    #mainnav-container .mainnav__caption,
    #mainnav-container .mainnav__categoriy h6 { color: rgba(255,255,255,0.98) !important; }
    #mainnav-container .mainnav__menu .nav-link,
    #mainnav-container .mainnav__menu a.nav-link,
    #mainnav-container .nav-label,
    #mainnav-container .mainnav__menu .nav-link .nav-label,
    #mainnav-container .mainnav__menu .mininav-toggle .nav-label,
    #mainnav-container .mainnav__menu a { color: rgba(255,255,255,0.92) !important; font-weight: {{ $navFontWeight }} !important; visibility: visible !important; opacity: 1 !important; }
    #mainnav-container .mainnav__menu .nav-link:hover,
    #mainnav-container .mainnav__menu a.nav-link:hover,
    #mainnav-container .mainnav__menu .nav-link.active,
    #mainnav-container .mainnav__menu a.nav-link.active,
    #mainnav-container .mainnav__menu .nav-link .nav-label { color: #fff !important; }
    #mainnav-container .mininav-content .nav-link { color: rgba(255,255,255,0.88) !important; }
    #mainnav-container .mininav-content .nav-link:hover { color: #fff !important; }
    #mainnav-container .badge { color: #fff !important; background-color: #dc3545 !important; }
    #mainnav-container .mainnav__menu i[class*="pli-"],
    #mainnav-container .mainnav__menu .fs-5 { color: rgba(255,255,255,0.92) !important; }
    #mainnav-container .mainnav__top-content { color: rgba(255,255,255,0.92); }
    #mainnav-container .mainnav__menu .nav-label { display: inline-block !important; }
    /* Inverse logo: for use on dark backgrounds (header/footer when option enabled) */
    .logo-inverse { filter: brightness(0) invert(1); }
    /* Dark theme: ensure front main nav text is visible (light text on dark) */
    [data-bs-theme="dark"] #header.nav-light .navbar .nav-link,
    [data-bs-theme="dark"] #header.nav-light .navbar .navbar-brand span,
    [data-bs-theme="dark"] #header.nav-light .btn-outline-primary { color: rgba(255,255,255,0.95) !important; }
    [data-bs-theme="dark"] #header.nav-light { background: #242628 !important; border-color: #3e4348 !important; }
    [data-bs-theme="dark"] #header.nav-colored .navbar .nav-link,
    [data-bs-theme="dark"] #header.nav-colored .navbar-brand span,
    [data-bs-theme="dark"] #header.nav-colored .btn-outline-primary { color: rgba(255,255,255,0.95) !important; }
    [data-bs-theme="dark"] #header.nav-colored { background: {{ $primary }} !important; }
    /* Theme1 footer tags: use theme/AU colors instead of blue */
    .footer .badge.bg-primary,
    .footer a.badge.bg-primary { background-color: {{ $primary }} !important; color: #fff !important; border: none; }
    .footer .badge.bg-primary:hover,
    .footer a.badge.bg-primary:hover { background-color: {{ settings()->au_corporate_green ?? '#1A5632' }} !important; color: #fff !important; }

    /* Select2 dark mode – admin */
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single,
    [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple { background-color: #242628 !important; border-color: #3e4348 !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__rendered,
    [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__rendered { color: #e4e6eb !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__placeholder,
    [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__placeholder { color: #9ca3af !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: #9ca3af transparent transparent transparent !important; }
    [data-bs-theme="dark"] .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b { border-color: transparent transparent #9ca3af transparent !important; }
    [data-bs-theme="dark"] .select2-dropdown { background-color: #242628 !important; border-color: #3e4348 !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-results__option { color: #e4e6eb !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-results__option[aria-selected=true] { background-color: #2d3136 !important; color: #e4e6eb !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] { background-color: var(--theme-color-primary, #119A48) !important; color: #fff !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-results__option[aria-disabled=true] { color: #6b7280 !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-results__group { color: #9ca3af !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-search--dropdown .select2-search__field { background-color: #2d3136 !important; border-color: #3e4348 !important; color: #e4e6eb !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-search--dropdown .select2-search__field::placeholder { color: #9ca3af !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: #3e4348 !important; border-color: #4b5262 !important; color: #e4e6eb !important; }
    [data-bs-theme="dark"] .select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: #d1d5db !important; }
</style>
