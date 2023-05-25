
@include('layouts.partials.styles')

<div id="main-wrapper" style="background-color:aliceblue;">

@include('layouts.partials.header')

@yield('styles')

<link rel="stylesheet" href="{{ asset('assets/plugins/highcharts/css/highcharts.css')}}"/>

@include('layouts.partials.alerts')

@yield('content')



<script src="{{ asset('assets/plugins/highcharts/highcharts.js')}}"></script>

@yield('scripts')

@include('layouts.partials.footer')


	