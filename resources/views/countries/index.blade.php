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

            <div class="col-lg-8 col-md-8 col-xs-12 states-map">
                @include('countries.map')
            </div>
 <div class="col-lg-4 col-md-4 col-xs-12">
    <div class="row states-tiles">
        <div id="accordion">
            @foreach($regions as $region)
                            @php
                $region_countries = array_filter($countries->toArray(), function ($cnt) use ($region) {
                    return $cnt['region_id'] == $region->id;
                });
                $resources = array_column($region_countries, 'resources');
                $regionalResources = array_sum($resources);
                            @endphp
                            <div class="card mb-1">
                                <div class="card-header bg-white" id="heading{{$region->id}}">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse{{$region->id}}" aria-expanded="false" aria-controls="collapse{{$region->id}}">
                                            <i class="fas fa-chevron-down"></i> {{ $region->region_name }}, {{$regionalResources}} Resources
                                        </button>
                                    </h5>
                                </div>
                                <div id="collapse{{$region->id}}" class="collapse" aria-labelledby="heading{{$region->id}}" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($region_countries as $country)
                                                                            @php
                                                $country = (Object) $country;
                                                                            @endphp
                                                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" data-aos="fade-in">
                                                                                <a href="{{ url('countries/details')}}?state={{$country->id}}">
                                                                                <div class="card">
                                                                                    <div class="card-body text-center">
                                                                                        <h5 class="card-title">{{ truncate($country->name, 30) }}</h5>
                                                                                        <p class="card-text"><small class="text-muted">{{$country->resources}} Resources</small></p>
                                                                                        <div class="country-img" style="background-image:url({{ asset('assets/img/flags/' . @$country->flag) }}); background-repeat: no-repeat; background-position: center; background-size: cover; width: 100px; height: 60px; margin: 10px auto;"></div>
                                                                                    </div>
                                                                                </div>
                                                                                </a>
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
        <!-- /row -->

    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection

@section('scripts')

<script>
    $(document).ready(function () {
        $('#accordion .collapse').on('shown.bs.collapse', function () {
            $(this).prev('.card-header').find('.indicator').removeClass('fa-chevron-down').addClass(
                'fa-chevron-up');
        }).on('hidden.bs.collapse', function () {
            $(this).prev('.card-header').find('.indicator').removeClass('fa-chevron-up').addClass(
                'fa-chevron-down');
        });
    });
</script>

@endsection