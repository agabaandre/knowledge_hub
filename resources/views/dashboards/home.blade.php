@extends('layouts.chart')

@section('styles')

@endsection

@section('content')

    @include('dashboards.partials.rcc_chart')

@endsection

@section('scripts')

    @include('common.select2')
    @include('dashboards.partials.rcc_chart_js',['chart_url'=>''])

@endsection