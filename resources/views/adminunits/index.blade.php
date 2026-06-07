
@extends('layouts.plain')

@section('styles')


@endsection
@section('content')      	
<!-- ======================= Administrative Units ======================== -->
<section class="space gray">
    <div class="container">
    
        <div class="row justify-content-center" data-aos="slide-down">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h2 class="ft-bold">Administrative Units</h2>
                </div>
            </div>
        </div>

        @if(!empty($show_admin_units_map))
        <div class="row justify-content-center mb-5" data-aos="fade-up">
            <div class="col-xl-11 col-lg-11 col-md-12">
                <div class="bg-white rounded p-3 shadow-sm">
                    <div id="adminUnitsMapChart" class="text-center text-muted p-4">
                        <i class="fa fa-spinner fa-spin me-2"></i> Loading map…
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @include('adminunits.admin_units')
        <!-- /row -->
        
    </div>
</section>
<!-- ======================= Administrative Units ======================== -->
@endsection
@section('scripts')
@include('adminunits.partials.map_script')
@endsection
