
@include('layouts.partials.styles')

<div id="main-wrapper">

@include('layouts.partials.header')

@yield('styles')

@if(Session::has('alert') || Session::has('message') || $errors->any())
 <div class="container">
    @include('layouts.partials.alerts')
 </div>
@endif;

@yield('content')

@include('layouts.partials.footer')

@yield('scripts')


	