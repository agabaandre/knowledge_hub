@extends('layouts.app')

@section('styles')

@endsection

@section('content')

<div class="gray pt-2">
<div class="container">
<div class="row">
			
	<!-- Item Wrap Start -->
	<div class="col-lg-12 col-md-12 col-sm-12 ">
		
		<div class="row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-12">
				<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
					<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
						<h6 class="mb-0 ft-medium fs-sm" {{ (count($assets)==0)?'py-5':'' }}>
							{{ count($assets) }} {{ (count($assets)>0 &&  isset($_GET['slug']) && !empty($_GET['slug']))?$assets[0]->type->type_name:'Resources' }} Available
						</h6>
					</div>

					@auth
						@if(count($assets)>0)
						<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12 float-end">
							<a href="?export=1" class="btn btn-sm btn-success rounded"><i class="fa fa-file-excel"></i>&nbsp; Export to Excel</a>
						</div>
						@endif
					@endauth
				</div>
			</div>
		</div>
	
		<!-- All jobs -->
		<div class="row">
				
				<!-- Single job -->
				@foreach($assets as $asset)
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
			
				<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
					<div class="jb-list01">
						<div class="jb-list-01-title">
								<a href="{{url('healthassets/detail')}}?id={{$asset->id}}">
									<h5 class="ft-medium mb-1">{{ $asset->asset_name }}</h5>
									<p>{{ $asset->type->type_name }}</p>
									<hr>
									<a href="{{$asset->url}}" target="_blank" class="text-success">{{ $asset->url }}</a>
									<p>{{ truncate($asset->asset_desc,182)}}</p>
								</a>
								<a class="text-success" href="{{ asset('healthassets/detail') }}?id={{$asset->id}}">View Details</a>
								</p>
						</div>
						<div class="jb-list-01-info d-block mb-3">
							<span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
							Added: {{ time_ago($asset->created_at) }}
							</span>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				{{ $assets->links() }}
			</div>
		</div>
		
	</div>
			
</div>
</div>
</div>

@endsection