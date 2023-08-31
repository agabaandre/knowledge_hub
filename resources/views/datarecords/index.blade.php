@extends('layouts.plain')

@section('styles')

@endsection

@section('content')

<div class="gray pt-2">
<div class="container">
<form class="row container">
	<div class="form-group col-lg-6">
			<label>RCC</label>
			<select class="form-control">
				<option>Select</option>
			</select>
		</div>
		<div class="form-group col-lg-6">
			<label>Country</label>
			<select class="form-control">
				<option>Select</option>
			</select>
		</div>
</form>
			
	<!-- Item Wrap Start -->
	<div class="col-lg-12 col-md-12 col-sm-12 ">
		
		<div class="row">
			<div class="col-xl-12 col-lg-12 col-md-12 col-12">
				<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
					<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
						<h6 class="mb-0 ft-medium fs-sm" {{ (count($records)==0)?'py-5':'' }}>
							{{ count($records) }} Data Records Available
						</h6>
					</div>

					@auth
						@if(count($records)>0)
						<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12 float-end">
							<a href="?export=1" class="btn btn-sm btn-success rounded"><i class="fa fa-file-excel"></i>&nbsp; Export to Excel</a>
						</div>
						@endif
					@endauth
				</div>
			</div>
		</div>
	
		<!-- All jobs -->
		<div class="row " style="min-height: 300px;">
				
				<!-- Single job -->
				@foreach($records as $record)
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
			
				<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
					<div class="jb-list01">
						<div class="jb-list-01-title">
								<a href="{{url('categories/data/detail')}}?id={{$record->id}}">
									<h5 class="ft-medium mb-1">{{ $record->title }}</h5>
									<p>{{ $record->sub_category->category->category_name }}</p>
									<hr>
							
									<p>{!! truncate($record->description,182) !!}</p>
								</a>
								<a class="text-success" href="{{ asset('categories/data/detail') }}?id={{$record->id}}">View Details</a>
								</p>
						</div>
						<div class="jb-list-01-info d-block mb-3">
							<span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
							Published: {{ time_ago($record->created_at) }}
							</span>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				{{ $records->links() }}
			</div>
		</div>
		
	</div>
			
</div>
</div>
</div>

@endsection