@php
    $theme = site_theme();
@endphp

@include('layouts.' . $theme . 'partials.styles')
@include('layouts.' . $theme . 'partials.header')

@yield('styles')

@if (Session::has('alert') || Session::has('message') || $errors->any())
    <div class="container">
        @include('layouts.' . $theme . 'partials.alerts')
    </div>
@endif

@yield('content')

@include('layouts.' . $theme . 'partials.footer')

@yield('scripts')
