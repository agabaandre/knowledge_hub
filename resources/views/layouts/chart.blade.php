
@include('layouts.partials.styles')

<div id="main-wrapper" style="background-color:#f2f2f2;">

@include('layouts.partials.header')

@if (!@$is_home && !@$hide_search)
    @include('home.partials.' . site_theme() . 'page_search')
@endif

@include('partials.secondary_navigation')

@yield('styles')

<link rel="stylesheet" href="{{ asset('assets/plugins/highcharts/css/highcharts.css')}}"/>

@include('layouts.partials.alerts')

@include('layouts.partials.content_preloader')

<div id="khub-page-content" class="khub-page-content position-relative">
@yield('content')
</div>



<script src="{{ asset('assets/plugins/highcharts/highcharts.js')}}"></script>

@yield('scripts')

@include('layouts.partials.footer')


	