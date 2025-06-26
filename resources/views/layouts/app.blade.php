@php
    $theme = site_theme();
@endphp

@include('layouts.' . $theme . 'partials.styles')

@include('layouts.' . $theme . 'partials.header')

@if (!@$is_home && !@$hide_search)
    @include('home.partials.' . $theme . 'page_search')
@endif

@yield('styles')

@include('layouts.' . $theme . 'partials.alerts')

@yield('content')
@yield('scripts')

<x-notify::notify />
@notifyJs

@include('layouts.' . $theme . 'partials.footer')
