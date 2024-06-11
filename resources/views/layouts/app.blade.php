@include('layouts.partials.styles')



<div id="main-wrapper">

@include('layouts.partials.header')

@if(!@$is_home && !@$hide_search)
    @include('home.partials.page_search')
@endif

@yield('styles')

@include('layouts.partials.alerts')

@yield('content')
@yield('scripts')

<x-notify::notify />
@notifyJs

@include('layouts.partials.footer')
