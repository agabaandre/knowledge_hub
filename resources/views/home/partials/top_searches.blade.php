			<!-- ======================= Top Searches List ======================== -->
			<section class="middle gray">
				<div class="container">

					<div class="row justify-content-center"  
					data-aos="fade-in">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="sec_title position-relative text-center mb-5">
								<h2 class="ft-bold">Top Searches</span></h2>
							</div>
						</div>
					</div>

					<!-- row -->
					<div class="row align-items-center" 
					>

							@php 
								$i=0; 
							@endphp

						   @foreach ($recent as $row)

							@php 
								$i++;
							@endphp
							
							<!-- Single -->
							
								<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12"
								  data-aos="zoom-in">
									<div class="jbr-wrap text-left border rounded">
										<div class="cats-box mlb-res rounded bg-white d-flex align-items-center px-3 py-3">
											   <div class="cats-box rounded bg-white d-flex align-items-center" style="min-width:100%;">
													@php
														if( is_valid_image(storage_link('uploads/publications/'.$row->cover))):
															$image_link = storage_link('uploads/publications/'.$row->cover);
														else:
																$image_link = storage_link('uploads/publications/cover.jpg');
														endif;
													@endphp

												<div class="cats-box-caption">
													<h4 class="fs-md mb-0 ft-medium"><a href="{{ url('records/show') }}?id={{$row->id}}">{{truncate($row->title, 50)}}</a></h4>
													<div class="d-block mb-2 position-relative" >
													  <p class="text-nothern p-0"><a href="{{ url('records/show') }}id={{$row->id}}">{{htmlspecialchars_decode(stripslashes(truncate($row->description,60)))}}</a></p>
														<span class="text-muted medium">
															Source: <i class="fa fa-bank mr-1"></i>{{$row->author->name}}</span>
														
														<span class="muted medium ml-2 theme-cl"><br>
														<i class="lni lni-briefcase mr-1"></i>Theme: {{$row->theme->description}}</span>
															<span class="muted medium ml-2 theme-cl"><br>
														<i class="lni lni-archive mr-1"></i>Sub Theme: {{$row->sub_theme->description}}</span>
														
														<span class="muted medium ml-2 text-muted mt-1 "><br>
														<i class="lni lni-empty-file mr-1"></i>Type: {{$row->file_type->name}}</span>
														
														<span class="text-muted medium d-block mt-1">
														    <span class=" mr-2"><i class="lni lni-calendar mr-1"></i>Last updated: {{time_ago($row->updated_at)}} </span>
															<span class=" mr-2"><i class="fa fa-eye mr-1"></i>{{$row->visits}} Views </span>
														    <span><span class="mr-1 ml-2 text-muted d-inline "
															         data-bs-toggle="popover"
																	 data-bs-placement="bottom" aria-expanded="false" aria-controls="comments{{$i}}"><i class="fa fa-comments"></i> {{count($row->comments)}} Comments</span></span>
															@auth()
															<div class="btn btn-outline-dark btn-sm mt-2 favbtn">
															@include ('common.favourites_btn')
															</div>
															@endauth
														</span>   

													</div>
												</div>
												<div class="text-center px-2 d-none d-lg-block"  style="min-width:20%; min-height:20vh;">
												  @if($row->is_video)

												  <iframe width="150" height="80" src="{{ $row->publication }}"></iframe>

												  @else
												 <a href="{{ url('records/show') }}?id={{$row->id}}">
													<img src="{{ $image_link }}" class="rounded" width="100px"/>
												  </a>
												  @endif
											    </div>
												
											</div>
										</div>

										@include('home.partials.comments')

									</div>
								</div>

						     @endforeach

					</div>
					<!-- row -->

					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="position-relative text-center">
								<a href="{{ url('records/search')}}" class="btn btn-md theme-bg rounded text-light hover-theme">Explore More Resources<i class="lni lni-arrow-right-circle ml-2"></i></a>
							</div>
						</div>
					</div>

				</div>
			</section>
			<!-- ======================= Top Searches ======================== -->