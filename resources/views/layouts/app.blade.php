
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')

@if(!@$is_home)
    @include('home.partials.page_search')
@endif

@yield('styles')
@yield('content')
@yield('scripts')

@include('layouts.partials.footer')


	