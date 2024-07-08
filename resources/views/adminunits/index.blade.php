
@extends('layouts.plain')

@section('styles')


@endsection
@section('content')      	
<!-- ======================= Countries ======================== -->
<section class="space gray">
    <div class="container">
    
        <div class="row justify-content-center" data-aos="slide-down">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="sec_title position-relative text-center mb-5">
                    <h2 class="ft-bold">Administrative Units</h2>
                </div>
            </div>
        </div>
        
        @include('adminunits.admin_units')
        <!-- /row -->
        
    </div>
</section>
<!-- ======================= Countries ======================== -->
@endsection
@section('scripts')
@endsection
