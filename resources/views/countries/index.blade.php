@extends('layouts.plain')

@section('styles')

<link rel="stylesheet" href="{{ asset('assets/css/map.css')}}">

@endsection
@section('content')
<!-- ======================= Countries ======================== -->
<section class="space gray">
    <div class="container">

        <!-- row -->
        <div class="row">

            <div class="col-lg-8  states-map">
                @include('countries.map')
            </div>
            <div class="col-lg-4">
                <div class="row align-items-center justify-content-center states-tiles">

                    <div id="accordion">
                        @foreach($regions as $region)
                        @php

                        $region_countries = array_filter(
                        $countries->toArray(),
                        function ($cnt) use ($region) {
                        return $cnt['region_id'] == $region->id;
                        }
                        );

                        $resources = array_column($region_countries, 'resources');
                        $regionalResources = array_sum($resources);

                        @endphp
                        <div class="row justify-content-center">
                            <div class="card col-lg-12">
                                <div class="card-header bg-white link" id="heading{{$region->id}}">
                                    <h5 class="mb-0">
                                        <a href="#" data-toggle="collapse" data-target="#collapse{{$region->id}}"
                                            aria-expanded="false" aria-controls="collapse{{$region->id}}">
                                            <i class="fas fa-chevron-down indicator"></i> {{ $region->region_name}},
                                            {{$regionalResources}} Resources
                                        </a>
                                    </h5>
                                </div>

                                <div id="collapse{{$region->id}}" class="collapse"
                                    aria-labelledby="heading{{$region->id}}" data-parent="#accordion">
                                    <div class="card-body">

                                        <div class="row">

                                            @foreach($region_countries as $country)

                                            @php
                                            $country = (Object) $country;
                                            @endphp

                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 "
                                                data-aos="fade-in">
                                                <div class="cats-wrap py-1 mb-1"
                                                    style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                                                    <a class="row"
                                                        href="{{ url('countries/details')}}?state={{$country->id}}"
                                                        class="cats-box d-block rounded bg-white px-2 py-4 row">
                                                        <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center py-3 circle col-lg-1"
                                                            style="background-image:url({{ asset('assets/img/flags/' . @$country->flag) }}); background-position:center; background-size:cover;">
                                                        </div>
                                                        <div class="cats-box-caption col-lg-11">
                                                            <h5 class="fs-md mb-0 ft-medium m-catrio">
                                                                {{truncate($country->name, 30)}}</h5>
                                                            <small>{{$country->resources}} Resources</small>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>

                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @endforeach
                        </div>

                    </div>




                </div>
            </div>

        </div>
        <!-- /row -->

    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection

@section('scripts')

<script>
$(document).ready(function() {
    $('#accordion .collapse').on('shown.bs.collapse', function() {
        $(this).prev('.card-header').find('.indicator').removeClass('fa-chevron-down').addClass(
            'fa-chevron-up');
    }).on('hidden.bs.collapse', function() {
        $(this).prev('.card-header').find('.indicator').removeClass('fa-chevron-up').addClass(
            'fa-chevron-down');
    });
});
</script>

@endsection