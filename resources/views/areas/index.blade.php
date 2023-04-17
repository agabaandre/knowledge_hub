@extends('layouts.app')

@section('styles')

@endsection

@section('content')

<div class="py-3 gray">
<div class="container">
<div class="row align-items-center">
					
@foreach ($areas as $area)
		<!-- Single -->
			<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
				<div class="jbr-wrap text-left border rounded">
					<div class="cats-box mlb-res rounded bg-white d-flex align-items-center justify-content-between px-3 py-3">
						<div class="cats-box rounded bg-white d-flex align-items-center">
							<div class="cats-box-caption px-2">
								<h4 class="fs-md mb-0 ft-medium">{!! truncate($area->name,100) !!}</h4>
							</div>
						</div>
						<div class="text-center mlb-last"><a href="{{ url('records/search')}}?area={{$area->id }}" class="btn gray ft-medium apply-btn fs-sm rounded">View Publications<i class="lni lni-arrow-right-circle ml-1"></i></a></div>
					</div>
				</div>
			</div>

@endforeach

<br/>

<div class="row justify-content-center">
	{{ $areas->links() }}
</div>

</div>
</div>
</div>

@endsection