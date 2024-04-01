@extends('admin.layouts.main')

@section('content')

	@php
		if($publication->cover):
			$image_link = storage_link('uploads/publications/'.$publication->cover);
		else:
			$image_link = storage_link('uploads/publications/cover.jpg');
		endif;
	@endphp

    <!-- ======================= Publication Info ======================== -->
	<div class="rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
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
                                
								@if(!empty($publication->publication))
								<div class="jbl_location mb-3">
								<a href="{{ $publication->publication }}" target="_blank" class="btn btn-sm rounded btn-outline-success fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-eye"></i> Visit Resource Link</a>
								</div>
								@endif
								<div class="jbl_info01">
									<span class="px-2 py-1 ft-medium medium badge badge-success px-3 rounded mr-2">{{ (!$publication->is_version)?$publication->sub_theme->description:'Version: '.$publication->version_no }}</span>
								</div>
							</div>
						</div>
						
					
						<div class="jbd-01-right text-right">

							<div class="jbl_button mb-2">

						
								@if($publication->is_approved == 0)
								<div class="row col-12 d-flex">
								    <a href="#approval-modal" data-toggle="modal"  class="btn btn-sm rounded btn-success fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-check-circle"></i> {{ ($publication->is_rejected== 0)?'Approve':'Reconsider'}}</a>
								</div>
								@endif
                                @if( ($publication->is_rejected== 0 && $publication->is_approved==1) || ($publication->is_rejected== 0 && $publication->is_approved==0))
								<div class="row col-12 d-flex">
								    <a href="#reject-modal" data-toggle="modal" class="btn btn-sm rounded btn-danger fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-times-circle"></i> {{ ($publication->is_approved==1)?'Recall':'Reject'}}</a>
								</div>
								@endif

                                 @include('admin.publications.partials.approval-modal',['action'=>url('admin/publications/approval'),'record'=>$publication])
                                 @include('admin.publications.partials.reject-modal',['action'=>url('admin/publications/approval'),'record'=>$publication])
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

			   @php

			    $col =(count($publication->summaries)>0 || $publication->has_attachments || $publication->parent_id>0)?"7":"12";
			   
				@endphp

				<div class="col-xl-{{$col}} col-lg-{{$col}} col-md-{{$col}} col-sm-12">
					<div class="rounded mb-4">
						<div class="jbd-01 pr-3">
						
							<div class="jbd-details mb-4">
								<h5 class="ft-medium fs-md text-success">Resource Details</h5>
								<div class="other-details">
                                    
                                    <br>
                                    <h5 class="ft-medium fs-md">Description</h5>
                                    <p>{!! $publication->description !!}</p>
									<div class="details ft-medium">
										<label class="text-muted">Source/Author</label>
										<span class="text-dark">{{ $publication->author->name }}</span>
									</div>
									<div class="details ft-medium">
										<label class="text-muted">No. of Visits</label>
										<span class="px-2 py-1 ft-medium medium theme-bg rounded mr-2">{{ $publication->visits }} Visits</span>
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
								</div>
							</div>

						</div>

                <div class="article_detail_wrapss single_article_wrap format-standard">

    
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
							<ul class="list-group mb-3 pl-2">
								@foreach($publication->versioning as $version)
									<li><h5 class="text-muted"><a href="{{ url('admin/publications/details') }}?id={{$version->id}}">Version {{$version->version_no}}</a></h5></li>
								@endforeach
							</ul>
						@elseif($publication->parent_id>0)
							<h5 class=" mb-3"><a class=" col-lg-12 text-center btn btn-sm btn-outline-success rounded" href="{{ url('admin/publications/details') }}?id={{$publication->parent_id}}"><i class="fa fa-link"></i> Original Version</a></h5>
						@endif

						@if(count($publication->summaries)>0)
							<h6 class="ft-medium fs-sm mb-3">Summaries and Abstracts</h6>
							<ul class="list-group mb-3">
								@foreach($publication->summaries as $summary)
									<li>
										<h6 class="text-muted"><a href="{{ url('admin/publications/summary') }}?id={{$summary->id}}">{{ truncate($summary->title,100) }} by {{$summary->author->name}}</a></h6>
								    </li>
								@endforeach
							</ul>
						@endif

						
				        @endif
                        </div>

					</div>

					<!-- Blog Comment -->
					<div class="article_detail_wrapss single_article_wrap format-standard">

						<div class="comment-area">
							<div class="all-comments">
								<h3 class="comments-title mt-3">{{count($publication->comments)}} Comment(s)</h3>
								<div class="comment-list">
									<ul>

										@foreach ($publication->comments as $comment)
											<li class="article_comments_wrap">

												<article>
													<div class="comment-details app-comment" >
														<div class="comment-meta">
															<div class="comment-left-meta">
																<h5 class="author-name">{{ ($comment->user) ? $comment->user->name : 'Anonymous'}}</h5>
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
                </div>

				<!-- Sidebar -->

				<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">

                <div class="row">

                    <div class="jbd-details mb-4 col-lg-12">
                    @if($publication->is_video)
                    <iframe width="650" height="400" src="{{ $publication->publication }}"></iframe>
                    @else
                    <img src="{{ $image_link }}" class="rounded" width="400px"/>
                    @endif
                    </div>

                </div>

                </div>
				</div>


			</div>
		</div>
	</section>

    @endsection