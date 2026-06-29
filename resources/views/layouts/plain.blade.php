@php
    $theme = site_theme();
@endphp

@include('layouts.' . $theme . 'partials.styles')
@include('layouts.' . $theme . 'partials.header')

@if (!@$is_home && !@$hide_search)
    @include('home.partials.' . $theme . 'page_search')
@endif

@include('partials.secondary_navigation')

@yield('styles')

@if (Session::has('alert') || Session::has('message') || $errors->any())
    <div class="container">
        @include('layouts.' . $theme . 'partials.alerts')
    </div>
@endif

@if ($theme === 'theme1.')
<div id="content" class="content front-bg khub-gt-content">
<div class="content__boxed"><div class="content__wrap">
@else
<div id="khub-page-content" class="khub-page-content">
@endif
@yield('content')
@if ($theme === 'theme1.')
</div></div></div>
@else
</div>
@endif

@include('layouts.partials.content_preloader')

@include('layouts.' . $theme . 'partials.footer')

@yield('scripts')
