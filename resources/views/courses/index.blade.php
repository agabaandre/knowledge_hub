@extends('layouts.app')

@section('styles')

@endsection

@section('content')
	<!-- ======================= Publication Info ======================== -->
<div class="bg-light  rounded py-5" style="background-image: url({{asset('frontend/img/dots.png')}}); background-repeat:repeat-x; background-size:contain;">
<div class="container">
<div class="row">
	<div class="col-xl-12 col-lg-12 col-md-12 col-12">
		<div class="jbd-01 d-flex align-items-center justify-content-between">
			<div class="jbd-flex d-flex align-items-center justify-content-start">
				<div class="jbd-01-thumb">
					
				</div>
				<div class="jbd-01-caption pl-3">
					<div class="tbd-title">
						<h4 class="mb-0 ft-medium fs-lg">
							Knowledge Hub Courses
						</h4>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<!-- ======================= Publication Info ======================== -->
<div class="container middle">
			
						<div class="row justify-content-center mt-3">
									<!-- single review -->
									@foreach ($courses as $row)
										<a href="https://khub.africacdc.org/elearning/course/view.php?id={{$row->moodle_id}}" 
											target="_blank" 
											class="col-lg-6 mt-2 mb-2" data-aos="fade-in">
											<div class="single_review px-1 card-content" style="border: 1px solid;">
												<div class="reviews_wrap position-relative bg-white rounded py-4 px-4">
													<div class="rw-header d-flex justify-content-start">
														<div class="pl-0">
															<h4 class="fs-md mb-0 ft-bold text-primary" style="color: var(--theme-color-primary)!important;">{{ truncate($row->fullname, 50) }}</h4>
														</div>
													</div>
														<p>{!! truncate($row->summary,270) !!}</p>
												</div>
											</div>
										</a>
									@endforeach
			
							</div>
					</div>

</div>

@endsection