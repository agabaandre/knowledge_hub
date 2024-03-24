@extends('layouts.app')

@section('content')

	@php
		if($publication->cover):
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

						
								@if(!empty($publication->publication))
								<div class="row col-12 d-flex" style="float:right !importntant;">
								<a href="{{ $publication->publication }}" target="_blank" class="btn btn-sm rounded btn-outline-success fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-eye"></i> Browse Resource</a>
								</div>
								@endif
									
								@auth
								<div class="row col-12 d-flex" style="float:right !importntant;">

								<a href="{{ route('account.newversion')}}?id={{ $publication->id }}"  class="btn btn-sm btn-outline-danger rounded fs-sm ft-medium mb-2" style="width:180px !important;">
								<i class="fa fa-plus"></i> Submit a Version</a>

								<a href="{{ route('account.summarize')}}?id={{ $publication->id }}" class="btn btn-sm btn-outline-danger rounded fs-sm ft-medium" style="width:180px !important;">
								<i class="fa fa-file"></i> Submit a Summary</a>
								</div>
							  @endauth
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

			   @php

			    $col =(count($publication->summaries)>0 || $publication->has_attachments || $publication->parent_id>0)?"7":"12";
			   
				@endphp

				<div class="col-xl-{{$col}} col-lg-{{$col}} col-md-{{$col}} col-sm-12">
					<div class="rounded mb-4">
						<div class="jbd-01 pr-3">
								<div class="jbd-details mb-4">
								@if($publication->is_video)
								<iframe width="650" height="400" src="{{ $publication->publication }}"></iframe>
								@else
								<img src="{{ $image_link }}" class="rounded" width="500px"/>
								@endif
								<br>
								<h5 class="ft-medium fs-md">Description</h5>
								<p>{!! $publication->description !!}</p>
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
										<label class="text-muted">Category</label>
										<span class="text-dark">{{ @$publication->category->category_name }}</span>
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
									<div class="details ft-medium">
										<div class="btn btn-outline-dark mt-2"> 
                                         @php
                                            $row = $publication;
                                         @endphp  
									     @include('common.favourites_btn')
										</div>
									</div>
								</div>
							</div>

						</div>

						<div class="row container">
							<div class="col-lg-12">
							<h5 class="text-bold text-success">Rating</h5>
						    @include('partials.general.rating')
							</div>
						</div>

						<div class="jbd-02 pt-4 pr-3 container">
							<h5 class="text-bold text-success">Share this</h5>
							<div class="row justify-content-center">
								{{ share_buttons( url('records/resource')."?id=".$publication->id) }}
							</div>
						</div>
					</div>

					<!-- Blog Comment -->
					<div class="article_detail_wrapss single_article_wrap format-standard">

						<div class="comment-area">
							<div class="all-comments">
								<h3 class="comments-title">{{count($publication->comments)}} Comments</h3>
								<div class="comment-list">
									<ul>

										@foreach ($publication->comments as $comment)
											<li class="article_comments_wrap">

												<article>
													<div class="comment-details app-comment" >
														<div class="comment-meta">
															<div class="comment-left-meta">
																<h4 class="author-name">{{ ($comment->user) ? $comment->user->name : 'Anonymous'}}</h4>
																<div class="comment-date">{{ time_ago($comment->created_at)}}</div>
															</div>
														</div>
														<div class="comment-text">
															<p>{{ nl2br($comment->comment) }}</p>
														</div>

													</div>

													
												</article>

											</li>
										@endforeach
									</ul>
								</div>
							</div>
						</div>

					</div>


				</div>

				<!-- Sidebar -->

				@if(count($publication->summaries)>0 || $publication->has_attachments || $publication->parent_id>0)
				
				<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">

				                 @if ($publication->has_attachments)
										@php
										$count = 1;
										@endphp
										<h5>Attachments</h5>
										<ul class="list-group mb-3">
										@foreach($publication->attachments as $pub_file)
										<li class="list-group-item"><a href="{{ storage_link('uploads/publications/') }}{{$pub_file->file}}" target="_blank" class="fs-sm ft-medium"><i class="fa fa-download"></i> {{$pub_file->description ?? 'View Attachment '.$count }}</a></li>
										@php
											$count++;
										@endphp
										
										@endforeach

										</ul>
									@endif

				@if(count($publication->summaries)>0 || count($publication->versioning)>0 || $publication->parent_id>0)
					<div class="jb-apply-form bg-white shadow rounded py-3 px-4 box-static">
					
					
					   @if(count($publication->versioning)>0)
							<h4 class="ft-medium fs-md mb-3">Resource Versions</h4>
							<ul class="list-group mb-3">
								@foreach($publication->versioning as $version)
									<li><h5 class="text-muted"><a href="{{ url('records/resource') }}?id={{$version->id}}">Version {{$version->version_no}}</a></h5></li>
								@endforeach
							</ul>
						@elseif($publication->parent_id>0)
							<h5 class=" mb-3"><a class=" col-lg-12 text-center btn btn-sm btn-outline-success rounded" href="{{ url('records/resource') }}?id={{$publication->parent_id}}"><i class="fa fa-link"></i> Original Version</a></h5>
						@endif

						@if(count($publication->summaries)>0)
							<h6 class="ft-medium fs-sm mb-3">Summaries and Abstracts</h6>
							<ul class="list-group mb-3">
								@foreach($publication->summaries as $summary)
									<li>
										<h6 class="text-muted"><a href="{{ url('records/shortened') }}?id={{$summary->id}}">{{ truncate($summary->title,100) }} by {{$summary->author->name}}</a></h6>
								    </li>
								@endforeach
							</ul>
						@endif

						
				        @endif

						@auth
			
						<h4 class="ft-medium fs-md mb-3">Got something to say about this resource?</h4>

						<form action="{{ url('records/comment') }}" method="post" class="_apply_form_form" >
						@csrf
						<input type="hidden" name="publication_id" value="{{ $publication->id }}" />
						<input type="hidden" name="user_id" value="{{ @current_user()->user_id }}" />
						<div class="form-group">
							<label class="text-success mb-1 ft-medium medium">Your comment</label>
							<textarea name="comment" class="form-control" placeholder="Your comment" required>{{ old('comment') }}</textarea>
					

						<div class="form-group mt-4">
							<button type="submit" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Submit Comment</button>
						</div>

                         </form>

						 @else

						 <div class="form-group">
							<label class="text-success mb-1 ft-medium medium">Your comment</label>
							<textarea name="comment" class="form-control" placeholder="Login to comment" disabled required>{{ old('comment') }}</textarea>
						 </div>

						 <div class="form-group mt-4">
							<a href="{{ url('/login') }}" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Login to Comment</a>
						</div>
						
						 @endauth

					</div>
				</div>

				@endif

			</div>
		</div>
	</section>

    @endsection