@extends('admin.layouts.main')

@section('content')


    <!-- ======================= Publication Info ======================== -->
	<div class="rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
		<div class="container">

		@include('layouts.partials.alerts')
		
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">
									<h4 class="mb-0 ft-medium fs-md">Resource Summary/Abstract</h4>
								</div>
								<div class="jbl_location mb-3">

									<span>Summary For: {!! $summary->publication->title !!}</span>
									<a href="{{ url('admin/publications/details') }}?id={{$summary->resource_id}}"  class="btn btn-primary btn-sm" 
									>View Original</a>
								</div>
							</div>
						</div>
						
					
						<div class="jbd-01-right text-right">

							<div class="jbl_button mb-2">

						
								@if($summary->is_approved == 0)
								<div class="row col-12 d-flex">
								    <a href="#approval-modal" data-toggle="modal"  class="btn btn-sm rounded btn-success fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-check-circle"></i> {{ ($summary->is_rejected== 0)?'Approve':'Reconsider'}}</a>
								</div>
								@endif
                                @if( ($summary->is_rejected== 0 && $summary->is_approved==1) || ($summary->is_rejected== 0 && $summary->is_approved==0))
								<div class="row col-12 d-flex">
								    <a href="#reject-modal" data-toggle="modal" class="btn btn-sm rounded btn-danger fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-times-circle"></i> {{ ($summary->is_approved==1)?'Recall':'Reject'}}</a>
								</div>
								@endif

                                 @include('admin.publications.partials.approval-modal',['action'=>url('admin/publications/summary_approval'),'record'=>$summary])
                                 @include('admin.publications.partials.reject-modal',['action'=>url('admin/publications/summary_approval'),'record'=>$summary])
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= Publication Info ======================== -->

	<!-- ============================ Publication Details Start ================================== -->
	<section class="py-5  bg-white">
		<div class="container">
			<div class="row">


				<div class="col-sm-12">
					<div class="rounded mb-4">
						<div class="jbd-01 pr-3">
						
							<div class="jbd-details mb-4">
								<h5 class="ft-medium fs-md text-success">Summary/Abstract Details</h5>
								<div class="other-details">
                                    
                                    <br>
									<h5 class="ft-medium fs-md">Title</h5>
                                    <p>{!! $summary->title !!}</p>
									<br>
                                    <h5 class="ft-medium fs-md">Description</h5>
                                    <p>{!! $summary->description !!}</p>
									<div class="details ft-medium">
										<label class="text-muted">Source/Author</label>
										<span class="text-dark">{{ $summary->author->name }}</span>
									</div>
								</div>
							</div>

						</div>

				</div>
                </div>

				
				</div>


			</div>
		</div>
	</section>

    @endsection