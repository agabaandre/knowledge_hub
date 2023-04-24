
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')

@yield('styles')

@include('layouts.partials.alerts')

@yield('content')

@include('layouts.partials.footer')

@yield('scripts')


	