
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')

@yield('styles')
@yield('content')
@yield('scripts')

@include('layouts.partials.footer')


	