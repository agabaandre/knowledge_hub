@php
    $userTheme = 'light';
    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasColumn('users', 'theme_preference')) {
        $pref = auth()->user()->theme_preference ?? 'light';
        $userTheme = in_array($pref, ['light','dark','system']) ? $pref : 'light';
    }
    $menuIconsClass = (settings()->menu_icons_enabled ?? 0) ? 'menu-icons-enabled' : 'menu-icons-disabled';
    $uiLocale = active_ui_locale();
    $uiLang = str_replace('_', '-', $uiLocale);
    $uiDir = locale_direction($uiLocale);
@endphp
<!DOCTYPE html>
<html lang="{{ $uiLang }}" dir="{{ $uiDir }}" data-bs-theme="{{ $userTheme === 'system' ? 'light' : $userTheme }}" data-theme-preference="{{ $userTheme }}" data-scheme="navy" class="{{ $menuIconsClass }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- description, canonical, og:* come from partials.seo.meta in header_resources (avoid duplicate global site_description) --}}
    @include('layouts.partials.header_resources')

    {{-- Webfonts: loaded from Admin > Appearance (typography partial: Montserrat, custom uploads, etc.); avoid fixed Poppins/Ubuntu so settings font applies everywhere --}}

    <link rel="stylesheet" href="{{ asset('theme1/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme1/assets/css/nifty.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme1/assets/css/demo-purpose/demo-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme1/assets/css/demo-purpose/demo-settings.min.css') }}">
    @include('layouts.theme1.partials.theme1_colors')
    @include('layouts.partials.rtl_stylesheet')
    <style>
        .root.front-container { min-height: 100vh; display: flex; flex-direction: column; background-color: #f8f9fa; }
        .root.front-container #content.content { flex: 1; pointer-events: auto; }
        .root.front-container .content__boxed,
        .root.front-container .content__wrap { pointer-events: auto; }
        .root.front-container .header { box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .root.front-container .navbar-nav .nav-link { font-weight: var(--nav-font-weight, 500); }
        .kh-linkedin-nav { align-items: stretch; gap: 0.05rem; }
        .kh-linkedin-nav .kh-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 4rem;
            padding: 0.3rem 0.45rem 0.15rem !important;
            color: inherit;
            text-align: center;
            border-bottom: 2px solid transparent;
            position: relative;
            gap: 0.1rem;
        }
        .kh-linkedin-nav .kh-nav-item:hover,
        .kh-linkedin-nav .kh-nav-item:focus { color: inherit; opacity: 0.92; }
        .kh-linkedin-nav .kh-nav-item.active {
            border-bottom-color: currentColor;
            font-weight: 600;
        }
        .kh-linkedin-nav .kh-nav-icon {
            font-size: var(--nav-icon-size, 16px);
            line-height: 1;
            margin-bottom: 0.05rem;
            font-weight: 400;
            opacity: 0.88;
        }
        .kh-linkedin-nav .kh-nav-label {
            font-size: var(--nav-font-size, 11px);
            line-height: 1.15;
            white-space: nowrap;
            letter-spacing: 0.01em;
        }
        .kh-linkedin-nav .kh-nav-badge {
            position: absolute;
            top: 0.15rem;
            right: 0.35rem;
            min-width: 1rem;
            height: 1rem;
            padding: 0 0.25rem;
            border-radius: 999px;
            background: #dc3545;
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            line-height: 1rem;
        }
        #header.nav-colored .kh-linkedin-nav .kh-nav-item { color: rgba(255,255,255,0.92); }
        #header.nav-colored .kh-linkedin-nav .kh-nav-item.active { color: #fff; border-bottom-color: #fff; }
        #header.nav-light .kh-linkedin-nav .kh-nav-item { color: #334155; }
        #header.nav-light .kh-linkedin-nav .kh-nav-item.active { color: var(--theme-color-primary, #119A48); border-bottom-color: var(--theme-color-primary, #119A48); }
        @media (max-width: 991.98px) {
            .kh-linkedin-nav .kh-nav-item {
                flex-direction: row;
                justify-content: flex-start;
                min-width: 0;
                padding: 0.55rem 0.75rem !important;
                border-bottom: none;
            }
            .kh-linkedin-nav .kh-nav-icon { margin: 0 0.55rem 0 0; font-size: var(--nav-icon-size, 16px); }
            .kh-linkedin-nav .kh-nav-label { font-size: calc(var(--nav-font-size, 11px) + 2px); }
            .kh-linkedin-nav .kh-nav-badge { position: static; margin-left: 0.35rem; }
        }
        .publication-list-card,
        .publication-list-card a,
        .publication-list-card .publication-image-link,
        .publication-list-card .publication-title-desktop a,
        .publication-list-card .publication-title-mobile a { pointer-events: auto !important; cursor: pointer; }
        .user-avatar-btn .user-avatar-placeholder { align-items: center; justify-content: center; min-width: 100%; min-height: 100%; }
        .user-avatar-btn .user-avatar-placeholder:not(.d-none) { display: inline-flex !important; }
        .user-avatar-btn .user-avatar-placeholder i.fa-user { font-size: 0.9rem; }
    </style>
    @if($userTheme === 'system')
    <script>
        (function(){
            var pref = document.documentElement.getAttribute('data-theme-preference');
            if (pref === 'system') {
                var dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light');
            }
        })();
    </script>
    @endif
</head>
<body class="bg-light">
<div id="root" class="root front-container">
    @if (!@$is_home)
        <div class="clearfix" style="height: 72px;"></div>
    @endif
