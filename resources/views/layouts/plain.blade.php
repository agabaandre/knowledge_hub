
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')


@yield('styles')

@yield('content')

@include('layouts.partials.footer')

@yield('scripts')


	