@extends('layouts.app')

@php
    $theme = site_theme();
    // SEO Meta Tags for Homepage
    $pageTitle = settings()->title ?? 'Africa CDC Knowledge Hub - Knowledge Repository for Public Health Resources';
    $pageDescription = settings()->site_description ?? 'Explore comprehensive public health resources, publications, research, and knowledge from Africa CDC. Access verified health information, data, and publications across African countries.';
    $pageKeywords = settings()->seo_keywords ?? 'Africa CDC, public health, health research, publications, knowledge hub, Africa, health data, medical research, public health resources';
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
@endphp

@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "{{ $pageTitle }}",
    "description": "{{ strip_tags($pageDescription) }}",
    "url": "{{ url('/') }}",
    "mainEntity": {
        "@type": "CollectionPage",
        "name": "Public Health Resources",
        "description": "Comprehensive collection of public health publications, research, and resources from Africa CDC"
    },
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ url('/') }}"
        }]
    }
}
</script>

@if(isset($recent) && count($recent) > 0)
{{-- ItemList Schema for Featured Publications --}}
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Top Publications",
    "description": "Top searched and featured public health publications from Africa CDC",
    "itemListElement": [
        @foreach($recent->take(10) as $index => $pub)
        {
            "@type": "ListItem",
            "position": {{ $index + 1 }},
            "item": {
                "@type": "ScholarlyArticle",
                "name": "{{ addslashes($pub->title) }}",
                "url": "{{ url('records/resource?id=' . $pub->id) }}",
                "description": "{{ addslashes(Str::limit(strip_tags($pub->description ?? ''), 200)) }}",
                @if($pub->author)
                "author": {
                    "@type": "Organization",
                    "name": "{{ addslashes($pub->author->name) }}"
                },
                @endif
                @if($pub->created_at)
                "datePublished": "{{ $pub->created_at->toIso8601String() }}",
                @endif
                @if($pub->cover || $pub->image_url)
                "image": "{{ filter_var($pub->cover ?? $pub->image_url, FILTER_VALIDATE_URL) ? ($pub->cover ?? $pub->image_url) : asset($pub->cover ?? $pub->image_url) }}",
                @endif
                "keywords": "{{ addslashes($pub->tags->pluck('tag_text')->implode(', ')) }}"
            }
        }@if(!$loop->last),@endif
        @endforeach
    ]
}
</script>
@endif
@endsection


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
        
        .sec_title {
            margin-bottom: 0;
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

    @if((settings()->show_events ?? false) && isset($events) && count($events) > 0)
        {{-- Events section header (same style as Explore Key Sections) --}}
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="sec_title text-center">
                        <h2 style="margin-top: 2px;">Events</h2>
                    </div>
                </div>
            </div>
        </div>

        {{-- Events slider below header --}}
        @include('home.partials.' . $theme . 'events_slider', ['events' => $events])
    @endif

    @include('home.partials.' . $theme . 'top_categories')

    @if(count($initiatives)>0)
        @include('home.partials.' . $theme . 'initiatives',['initiatives'=>$initiatives])
    @endif

    @if((settings()->show_featured ?? false) && count($featured)>0)
        @include('home.partials.' . $theme . 'featured')
    @endif

    @if(settings()->show_top_searches ?? false)
        @include('home.partials.' . $theme . 'top_searches')
    @endif
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
