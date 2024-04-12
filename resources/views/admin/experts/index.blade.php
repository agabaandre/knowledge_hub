@extends('admin.layouts.main')

@section('styles')

@endsection

@section('content')

<div class="gray pt-2">
<div class="container">
<div class="row">
			
	<!-- Item Wrap Start -->
	<div class="col-lg-12 col-md-12 col-sm-12 ">
		
		<div class="row">

			<div class="row mt-2">
			@include('layouts.partials.alerts')
			</div>


			<div class="col-md-12 text-right mb-2">
				<a href="#create-modal" data-toggle="modal" class="btn btn-success float-right"><i class="fa fa-plus"></i> Add New</a>
			</div>

			<div class="col-xl-12 col-lg-12 col-md-12 col-12">
				<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
					<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
						<h6 class="mb-0 ft-medium fs-sm" {{ (count($experts)==0)?'py-5':'' }}>
							{{ count($experts) }} {{ (count($experts)>0 &&  isset($_GET['slug']) && !empty($_GET['slug']))?$experts[0]->type->type_name:'Experts' }} {{ (count($experts)>1)?'':'' }} Available
						</h6>
					</div>
					
					@auth
						@if(count($experts)>0)
						<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12 float-end">
							<a href="?export=1" class="btn btn-sm btn-success rounded"><i class="fa fa-file-excel"></i>&nbsp; Export to Excel</a>
						</div>
						@endif
					@endif
				</div>
			</div>
		</div>
	
		<!-- All jobs -->
		<div class="row">
				
				<!-- Single job -->
				@foreach($experts as $expert)
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
			
				<div class="job_grid d-block border rounded px-3 pt-3 pb-2 bg-white mt-2">
					<div class="jb-list01">
						<div class="jb-list-01-title">
							        <h5 class="ft-medium mb-1 text-success">{{ $expert->first_name }} {{ $expert->last_name }}</h5>
									<h6 class="text-dark">{{ $expert->job_title}}</h6>
									<h6 class="text-dark">{{ $expert->occupation}}</h6>
									<h6 class="text-dark">{{ $expert->email}}</h6>
									<h6 class="text-danger">{{ ($expert->type)?$expert->type->type_name:'' }}</h6>
									<h6 class="text-success">{{ ($expert->country)?$expert->country->name:'' }}</h6>
						</div>
						<div class="jb-list-01-info d-block mb-3">
							<span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
							Added: {{ time_ago($expert->created_at) }}
							</span>

							<a href="#create-modal{{$expert->id}}" data-toggle="modal" class="btn btn-dark float-right"><i class="fa fa-edit"></i> Edit Record</a>
						</div>
					</div>
				</div>
			</div>

			@include('admin.experts.partials.create-modal',['record'=>$expert, 'expert_types' => $types])

			@endforeach
		</div>
		
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				{{ $experts->links() }}
			</div>
		</div>
		
	</div>
			
</div>
</div>
</div>

@include('admin.experts.partials.create-modal')

@endsection

@section('scripts')

@include('common.select2')

@endsection