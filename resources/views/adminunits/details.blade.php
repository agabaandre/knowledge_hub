
@extends('layouts.plain')

@section('styles')


@endsection
@section('content')      	
<!-- ======================= Countries ======================== -->
<section class="space gray">
    
    <div class="container">

    @if(count($publications) == 0 && count($child_units) == 0)
    <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-6" data-aos="flip-left">
                <div class="cats-wrap text-center" style="box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;">
                    <a href="#" class="cats-box d-block rounded bg-white px-2 py-4">
                        <div class="text-center mb-2 mx-auto position-relative d-inline-flex align-items-center justify-content-center p-3 circle" style="background-image:url({{ storage_link('uploads/adminunits/'.@$unit->logo) }}); background-position:center; background-size:cover;">
                        </div>
                        <div class="cats-box-caption">
                            <h5 class="fs-md mb-0 ft-medium m-catrio">{{$unit->name}}</h5>
                            <p>{{$unit->description}}</p>

                            <small class="text-muted mt-5 py-3"> <br>No Publications from this Unit.</small>
                        </div>
                    </a>
                </div>
            </div>
    </div>

    @else
       
            @if(count($child_units)>0)

                <div class="row justify-content-center" data-aos="slide-down">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5 mt-3">
                            <h2 class="ft-bold">Units Under {{$unit->name}}</h2>
                        </div>
                    </div>
                </div>

                @include('adminunits.admin_units',['adminunits'=>$child_units])

            @endif

            
            @if(count($publications)>0)
            
                <div class="row justify-content-center" data-aos="slide-down">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="sec_title position-relative text-center mb-5 mt-3">
                            <h2 class="ft-bold">Published Resources</h2>
                        </div>
                    </div>
                </div>
            
                <div class="container">
                    @include('publications.partials.publications')
                </div>

            @else
                <div class="row justify-content-center mb-2">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-2">
                        <div class="sec_title position-relative text-center mb-5 mt-5">
                            <h4 class="ft-bold ft-md text-warning">No resources have been published by  {{$unit->name}} yet</h4>
                        </div>
                    </div>
                </div>
            @endif
    @endif


        
    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
@endsection
