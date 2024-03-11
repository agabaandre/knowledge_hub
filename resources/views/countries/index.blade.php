
@extends('layouts.plain')

@section('styles')

<link rel="stylesheet" href="{{ asset('assets/css/map.css')}}">

@endsection
@section('content')      	
<!-- ======================= Countries ======================== -->
<section class="space gray">
    <div class="container">
        
        <!-- row -->
        <div class="row align-items-center justify-content-center states-map">
            
            @include('countries.map')
             
        </div>
        <!-- /row -->
        
        <div class="row align-items-center justify-content-center states-tiles">
         
            @foreach($countries as $country)

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6" data-aos="fade-in">
                    <div class="cats-wrap text-center" style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                        <a href="{{ url('countries/details')}}?state={{$country->id}}" class="cats-box d-block rounded bg-white px-2 py-4">
                            <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 circle" style="background-image:url({{ asset('assets/img/flags/'.@$country->flag) }}); background-position:center; background-size:cover;">
                            </div>
                            <div class="cats-box-caption">
                                <h5 class="fs-md mb-0 ft-medium m-catrio">{{truncate($country->name,30)}}</h5>
                                <small>{{$country->resources}} Resources</small>
                            </div>
                        </a>
                    </div>
                </div>

            @endforeach
            
            
        </div>
    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
 <script  src="{{ asset('assets/js/map.js')}}"></script>
@endsection
