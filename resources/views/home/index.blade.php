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

        /* Modern Homepage Enhancements */
        .middle.gray {
            padding: 4rem 0;
        }

        .jbr-wrap {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0 !important;
            overflow: hidden;
        }

        .jbr-wrap:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .sec_title h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .sec_title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--theme-color-primary, #119A48), #16c653);
            margin: 1rem auto 0;
            border-radius: 2px;
        }

        .single_review {
            margin-bottom: 1.5rem;
        }

        .reviews_wrap {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .reviews_wrap:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
            border-color: rgba(17, 154, 72, 0.2);
        }

        /* Remove border radius from search buttons only */
        .search-form .btn.theme-bg,
        .search-container .btn.theme-bg,
        #simple_search .btn.theme-bg,
        .filters .btn.theme-bg {
            border-radius: 0 !important;
        }

        .btn.theme-bg {
            transition: all 0.3s ease;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }

        .btn.theme-bg:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
        }

        @media (max-width: 768px) {
            .sec_title h2 {
                font-size: 1.5rem;
            }

            .middle.gray {
                padding: 2rem 0;
            }
        }
    </style>

    @if ((!get_cookie('CDC_Tour_Finished') && !get_cookie('CDC_Tour_Declined')) || !env('SITE_LIVE'))
        @include('partials.tour.css')
    @endif
@endsection

@section('content')
    @include('home.partials.' . $theme . 'spotlight')

    @if(empty($theme))
        @include('home.partials.' . $theme . 'top_categories')

        @if(count($initiatives)>0)
            @include('home.partials.' . $theme . 'initiatives',['initiatives'=>$initiatives])
        @endif

        @if(count($featured)>0)
            @include('home.partials.' . $theme . 'featured')
        @endif
    @endif

    @include('home.partials.' . $theme . 'top_searches')
@endsection

@section('scripts')
    @include('common.select2')

    @if ((!get_cookie('CDC_Tour_Finished') && !get_cookie('CDC_Tour_Declined')) || !env('SITE_LIVE'))
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
