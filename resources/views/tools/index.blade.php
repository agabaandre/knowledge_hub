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
						<h6 class="mb-0 ft-medium fs-sm" {{ (count($tools)==0)?'py-5':'' }}>
							{{ count($tools) }} Tool{{(count($tools)>1)?'s':''}} Available
						</h6>
					</div>
					
					@auth
						@if(count($tools)>0)
						<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12 float-end">
							<a href="{{ current_url() }}export=1" class="btn btn-sm btn-success rounded"><i class="fa fa-file-excel"></i>&nbsp; Export to Excel</a>
						</div>
						@endif
					@endif
				</div>
			</div>
		</div>
	
		<!-- All jobs -->
		<div class="row">
				
				<!-- Single job -->
				@foreach($tools as $tool)
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
			
				<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
					<div class="jb-list01">
						<div class="jb-list-01-title">
									<h4 class="ft-medium mb-1 text-success">{{ $tool->tool_name }}</h4>
									<h6 class="text-dark text-muted text-sm">{{ $tool->category->category_name}}</h6>
									<p class="text-dark">{!! $tool->tool_desc !!}</p>
									<span class="text-muted"><i class="lni lni-alarm-clock mr-1"></i>
							        Added {{ time_ago($tool->created_at) }}</span>
									
						</div>
						<div class="jb-list-01-info d-block mb-3 mt-3">
						  <a class="btn btn-sm theme-bg text-white" href="{{ $tool->tool_url}}"><i class="fa fa-link mr-1"></i>Details</a>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				{{ $tools->links() }}
			</div>
		</div>
		
	</div>
			
</div>
</div>
</div>

@endsection