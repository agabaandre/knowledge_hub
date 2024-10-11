@extends('layouts.app')

@php
    $theme = site_theme();
@endphp


@section('styles')
    @if (empty($theme))
        <link href="{{ asset('frontend/css/home.css') }}" rel="stylesheet" />
    @endif

    <style>
        .spotlight:after {
            max-height: 20% !important;
        }
    </style>

    @if (!get_cookie('CDC_Tour_Finished') || !env('SITE_LIVE'))
        @include('partials.tour.css')
    @endif
@endsection

@section('content')
    @include('home.partials.' . $theme . 'spotlight')
    @include('home.partials.' . $theme . 'top_categories')
    @include('home.partials.' . $theme . 'featured')
    @include('home.partials.' . $theme . 'top_searches')
    @include('home.partials.top_authors')
@endsection

@section('scripts')
    @include('common.select2')

    @if (!get_cookie('CDC_Tour_Finished') || !env('SITE_LIVE'))
        @include('partials.tour.js')
    @endif

    <script>
        var showing = false;

        function showComments(elem) {

            // Pick the div data as required
            var head = "" + $('.heading' + elem).html();
            var data = "" + $('.pbody' + elem).html();


            $('.pop' + elem).popover({
                html: true,
                title: head,
                content: data,
                placement: 'bottom',
                manual: 'hover'
            });

        }
    </script>
@endsection
