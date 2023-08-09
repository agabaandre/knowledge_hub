	<section class="middle gray" style="background-image:url(<?php echo asset('frontend/img/dots.png') ?>); background-repeat:repeat;">
		<div class="container">

			<div class="row justify-content-center">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
					<div class="sec_title position-relative text-center mb-5">
						<h2 class="ft-bold">Recommended</span></h2>
					</div>
				</div>
			</div>

			<div class="row justify-content-center">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
					<div class="review-slide px-3">

						<!-- single review -->
						@foreach ($featured as $row)
							<a href="{{url('records/resource')}}?id={{$row->id}}">
								<div class="single_review px-2" data-aos="fade-in">
									<div class="reviews_wrap position-relative bg-white rounded py-4 px-4">
										<div class="rw-header d-flex justify-content-start">
											<div class="rv-110-caption pl-0">
												<h4 class="ft-medium fs-md mb-0 lh-1">{{ truncate($row->title, 30) }}</h4>
												<p class="p-0 m-0">Source: <i class="fa fa-bank mr-1"></i>{{truncate(@$row->author->name,30)}}</p>
											</div>
										</div>
										    <span class="d-block theme-cl">
												<i class="lni lni-briefcase mr-1"></i>Theme: {{$row->theme->description}}
											</span>
											<span class="muted medium d-block theme-cl"><i class="lni lni-archive mr-1"></i>Sub Theme: {{$row->sub_theme->description}}</span>
											<span class="text-muted medium d-block "><i class="lni lni-calendar mr-1"></i>Last updated: {{time_ago($row->updated_at)}} </span>
											<span class="text-muted d-block mt-1">
												<span class=" mr-1"><i class="fa fa-eye mr-1"></i>{{$row->visits}} Views </span>
												<span class=" mr-1 ml-1"><i class="fa fa-comments"></i> {{count($row->comments)}} Comments</span>
												
								           </span>
									</div>
								</div>
							</a>
						@endforeach

					</div>
				</div>
			</div>
		</div>
	</section>