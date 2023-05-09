@extends('layouts.app')

@section('content')

	@php
		if( is_valid_image(storage_link('uploads/publications/'.$publication->cover))):
			$image_link = storage_link('uploads/publications/'.$publication->cover);
		else:
				$image_link = storage_link('uploads/publications/cover.jpg');
		endif;
	@endphp

    <!-- ======================= Publication Info ======================== -->
	<div class="bg-light rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
		<div class="container">

		@include('layouts.partials.alerts')
		
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							<div class="jbd-01-thumb">
								<img src="{{ asset('uploads/publications/'.$publication->cover)}}" class="img-fluid" width="100" alt="" />
							</div>
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">
									<h4 class="mb-0 ft-medium fs-md">
										{!! $publication->title !!}
									</h4>
								</div>
								<div class="jbl_location mb-3">

									<span>{!! $publication->theme->description !!}</span>
								</div>
								<div class="jbl_info01">
									<span class="px-2 py-1 ft-medium medium text-light theme-bg rounded mr-2">{{ (!$publication->is_version)?$publication->sub_theme->description:'Version '.$publication->version_no }}</span>
								</div>
							</div>
						</div>
						
					
						<div class="jbd-01-right text-right">
						
							
							<div class="jbl_button mb-2">

								<a href="{{ url('records/resource') }}?id={{$publication->id}}"  class="btn btn-md rounded bg-white border fs-sm ft-medium"><i class="fa fa-link"></i> View Original Resource</a>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= Publication Info ======================== -->

	<!-- ============================ Publication Details Start ================================== -->
	<section class="py-5">
		<div class="container">
			<div class="row">

			   

				<div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
					<div class="rounded mb-4">
						<div class="jbd-01 pr-3">
							<div class="jbd-details mb-4">
								<br>
								<p>{!! $abstract->description !!}</p>
							</div>

							<div class="jbd-details mb-4">
								<h5 class="ft-medium fs-md text-success">Resource Details</h5>
								<div class="other-details">
									<div class="details ft-medium">
										<label class="text-muted">Source</label>
										<span class="text-dark">{{ $publication->author->name }}</span>
									</div>
									<div class="details ft-medium">
										<label class="text-muted">No. of Visits</label>
										<span class="px-2 py-1 ft-medium medium text-light theme-bg rounded mr-2">{{ $publication->visits }} Visits</span>
									</div>
									<div class="details ft-medium">
										<label class="text-muted">Type</label>
										<span class="text-dark">{{ $publication->file_type->name }}</span>
									</div>
									<div class="details ft-medium">
										<label class="text-muted">Theme</label>
										<span class="text-dark">{!! $publication->theme->description !!}</span>
									</div>
									<div class="details ft-medium">
										<label class="text-muted">Sub-Theme</label>
										<span class="text-dark">{!! nl2br($publication->sub_theme->description) !!}</span>
									</div>
								</div>
							</div>

						</div>

						<div class="jbd-02 pt-4 pr-3">
							<h5 class="text-bold text-success">Share this</h5>
							<div class="row justify-content-center">
								{{ share_buttons( url('records/shortened')."?id=".$publication->id) }}
							</div>
						</div>
					</div>

				</div>

                @if(count($publication->summaries)>0)
				<!-- Sidebar -->
				<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
					<div class="jb-apply-form bg-white shadow rounded py-3 px-4 box-static">
					
							<h4 class="ft-medium fs-md mb-3">Other Summaries and Abstracts</h4>
							<ul class="list-group mb-3">
								@foreach($publication->summaries as $summary)
									<li>
										<h6 class="text-muted">
											
								         @if($summary->id == $abstract->id)
												{{ truncate($summary->title,100) }} <b>by You</b>
										 @else
										   <a href="{{ url('records/summaries') }}?id={{$summary->id}}">
												{{ truncate($summary->title,100) }} by {{$summary->author->name}}
											</a>
										 @endif
										</h6>
								    </li>
								@endforeach
							</ul>
					</div>
				</div>
                
				@endif
			

			</div>
		</div>
	</section>

    @endsection