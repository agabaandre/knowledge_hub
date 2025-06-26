@extends('layouts.app')

@section('styles')

<link rel="stylesheet" type="text/css" href="https://cdn.flexmonster.com/theme/accessible/flexmonster.min.css" />

@endsection

@php
 $hide_search = true
@endphp

@section('content')
<!-- ======================= Publication Info ======================== -->
<div class="bg-light  rounded py-5" style="background-image: url({{asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
    <div class="container">
        <div class="row justify-content-center" style="min-height:500px;">
            <div id="pivotContainer">The component will appear here</div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
    <script src="https://cdn.flexmonster.com/flexmonster.js"></script>
    <script>
        console.log('Flex')
        let pivot = new Flexmonster({
            container: "pivotContainer",
            componentFolder: "https://cdn.flexmonster.com/",
            toolbar: true,
            report: {
                options: {
                    grid: {
                        type:"flat",
                        showFilter: true,
                        showTotals: true,
                        showGrandTotals: false,
                        showHierarchyCaptions: true,
                        showHeaders:true
                    },
                showAggregations: false,
                defaultHierarchySortName: 'desc'
               }
            },
           licenseKey: "Z783-XDC455-365V5K-04055M-54025X-242D24-3L5B1D"
        });

        
    </script>
@endsection

