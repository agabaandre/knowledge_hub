			<!-- ================================ Tag Award ================================ -->
			<section class="py-3" style="margin-top: -5%;" >
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="crp_box fl_color ovr_top">
								<div class="row align-items-center">
								
								@foreach($categories as $category)
										
								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
								<a href="{{url($category['link'])}}">
										<div class="dro_140">
											<div class="dro_141 de">
												<i class="{{$category['icon']}}"></i>
												<!-- <img src="{{ asset('assets/img/categories/'.$category['image'])}}" style="max-width:35px;"/> -->
											</div>
											<div class="dro_142">
												<h6>{{$category['title']}}</h6>
												<p>{{$category['stats']}} Records</p>
											</div>
										</div>
								</a>
									</div>
								@endforeach

								</div>

							</div>
						</div>
					</div>
				
				</div>
			</section>
		