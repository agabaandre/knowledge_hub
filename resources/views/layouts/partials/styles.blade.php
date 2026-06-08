@php
    $userTheme = 'light';
    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasColumn('users', 'theme_preference')) {
        $pref = auth()->user()->theme_preference ?? 'light';
        $userTheme = in_array($pref, ['light','dark','system']) ? $pref : 'light';
    }
    $menuIconsClass = (settings()->menu_icons_enabled ?? 0) ? 'menu-icons-enabled' : 'menu-icons-disabled';
    $docDir = ui_document_direction();
@endphp
<!DOCTYPE html>
<html lang="{{ $docDir['lang'] }}" dir="{{ $docDir['dir'] }}" xmlns="https://www.w3.org/1999/xhtml" data-bs-theme="{{ $userTheme === 'system' ? 'light' : $userTheme }}" data-theme-preference="{{ $userTheme }}" class="{{ $menuIconsClass }}{{ $docDir['is_rtl'] ? ' khub-rtl-document' : '' }}">
<head>

@include('layouts.partials.header_resources')
@if($userTheme === 'system')
<script>(function(){ var pref = document.documentElement.getAttribute('data-theme-preference'); if (pref === 'system') { var dark = window.matchMedia('(prefers-color-scheme: dark)').matches; document.documentElement.setAttribute('data-bs-theme', dark ? 'dark' : 'light'); } })();</script>
@endif
</head>

<body onload="">
<div class="preloader"></div>
<div id="main-wrapper" class="khub-gt-content">
