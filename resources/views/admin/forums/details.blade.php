@extends('admin.layouts.main')

@section('content')

	@php
		if($forum->forum_image):
			$image_link = storage_link('uploads/forums/'.$forum->forum_image);
		else:
			$image_link = null;
		endif;
	@endphp

    <!-- ======================= forum Info ======================== -->
	<div class="rounded py-5" style="background-image: url({{ asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
		<div class="container">

		@include('layouts.partials.alerts')
		
			<div class="row">
                <div class="col-lg-12 mb-3">
                   <h3 class="mb-0 ft-medium fs-md text-primary">Forum Details</h3>
                </div>
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">

									<h4 class="mb-0 ft-medium fs-md">
										{!! $forum->forum_title !!}
									</h4>
								</div>
								<div class="jbl_location mb-3">

									<span>By {{ $forum->user->name }}, {{time_ago($forum->created_at)}}</span>
								</div>
                                
							</div>
						</div>
						
					
						<div class="jbd-01-right text-right">

							<div class="jbl_button mb-2">

						
								@if($forum->is_approved == 0)
								<div class="row col-12 d-flex">
                                    @if($forum->is_rejected==1)<label class="badge badge-danger">Rejected</label>@endif
								    <a href="#approval-modal" data-toggle="modal"  class="btn btn-sm rounded btn-success fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-check-circle"></i> {{ ($forum->is_rejected== 0)?'Approve':'Reconsider'}}</a>
								</div>
								@endif
                                @if( ($forum->is_rejected== 0 && $forum->is_approved==1) || ($forum->is_rejected== 0 && $forum->is_approved==0))
								<div class="row col-12 d-flex">
                                    @if($forum->is_approved==1)<label class="badge badge-success">Approved</label>@endif
								    <a href="#reject-modal" data-toggle="modal" class="btn btn-sm rounded btn-danger fs-sm ft-medium mb-2" style="width:180px !important;"><i class="fa fa-times-circle"></i> {{ ($forum->is_approved==1)?'Recall':'Reject'}}</a>
								</div>
								@endif

                                 @include('admin.publications.partials.approval-modal',['action'=>url('admin/forums/approve'),'record'=>$forum])
                                 @include('admin.publications.partials.reject-modal',['action'=>url('admin/forums/reject'),'record'=>$forum])
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= forum Info ======================== -->

	<!-- ============================ forum Details Start ================================== -->
	<section class="py-5  bg-white">
		<div class="container">
			<div class="row">

				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
					<div class="rounded mb-4">
						<div class="jbd-01 pr-3">
						
							<div class="jbd-details mb-4">
								<div class="other-details">
                                    
                                    <br>
                                    <h5 class="ft-medium fs-md">Description</h5>
                                    <p>{!! $forum->forum_description !!}</p>
									<div class="details ft-medium">
										<label class="text-muted">Publisher/Author</label>
										<span class="text-dark">{{ $forum->user->name }}</span>
									</div>
								</div>
							</div>

						</div>

					<!-- Blog Comment -->
					<div class="article_detail_wrapss single_article_wrap format-standard">

						<div class="comment-area">
							<div class="all-comments">
								<h3 class="comments-title mt-3">{{count($forum->comments)}} Comment(s)</h3>
								<div class="comment-list">
									<ul>

										@foreach ($forum->comments as $comment)
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
															<p>{!!($comment->comment) !!}</p>
                                                            <br>
                                                            <label class="badge theme-bg text-white">{{ ucwords($comment->status) }}</label>
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

				</div>


			</div>
		</div>
	</section>

    @endsection