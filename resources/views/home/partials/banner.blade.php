      
			<!-- ======================= Home Banner ======================== -->
			<div class="home-banner margin-bottom-0 intro-bg">
				<div class="container">
					
					<div class="row align-items-center justify-content-between">
						<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
							<div class="banner_caption text-left mb-4">

								<h3 class="banner_title ft-bold mb-1" style="text-align:center;">"It’s a Good Day to Have a Good Day!"</h3>
								
							</div>
							<form action="{{ url('records/search') }}" class="bg-white rounded p-1 search-form">
							<div class="row no-gutters">
									<div class="col-xl-8 col-lg-8 col-md-8 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text" class="form-control lg left-ico autocomplete term" name="term" value="{{ @old('term') }}" placeholder="Type Keywords to unfold it all" />
											<i class="bnc-ico lni lni-search-alt"></i>
										</div>
									</div>
								
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width custom-height-lg theme-bg text-white fs-md" type="submit">Click to Unfold</button>
										</div>
									</div>
								</div>
							</form>

                            

                            <?php /*
							<div class="d-block mb-2 mt-3">
                                <span class="text-muted">Tags:</span>

	  							@php
                                 $colors = ['dark','blue','green'];
								@endphp

                                @foreach ($tags as $tag)
                                    <a href="{{ url('records/search') }}?tag={{$tag->tag_text}}" class="px-3 py-1 medium bg-{{$colors[mt_rand(0,2)]}} text-white rounded">{{$tag->tag_text}}</a>
                                @endforeach

                            </div> */ ?>

                            <div class="quotes-slider px-3 col-lg-8">
								
								<!-- single review -->
								@foreach($quotes as $quote)

								<div class="single_review px-2">
									<div class="reviews_wrap position-relative rounded py-1 px-2">
										
										<div class="rw-header d-flex mt-3 text-bold">
											<p class="text-bold">{{nl2br($quote->quote)}}</p>
										</div>
									</div>
								</div>
								@endforeach

							</div>

							
						</div>
						<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
							<div class="bnr_thumb position-relative">
								<img src="{{ asset('frontend/img/bn-3.png') }}" class="img-fluid bnr_img" alt="" />
								<div class="list_crs_img">
									<img src="{{ asset('frontend/img/favs/health-check.png') }}" class="img-fluid elsio cirl animate-fl-y" alt="" style="min-width: 170px; cursor: pointer;"
									onclick="$('.quiz').show();">

									<img src="{{ asset('frontend/img/favs/africa.png') }}" class="img-fluid elsio moon animate-fl-x" alt="" style="min-width: 170px;">

									<img src="{{ asset('frontend/img/favs/demographics.png') }}" class="img-fluid elsio arrow animate-fl-y" alt="" style="min-width: 170px;">
								</div>

								
							</div>

						<div id='container' class="quiz" style="z-index:100; display: none; position:absolute; top:70px; opacity:0.9;">
							<div id='title'>
								<h1 class="quizH1">Attempt this Quiz</h1>
							</div>
							<br/>
							<div id='quiz'></div>
							<div class='button' id='next'><a href='#'>Next</a></div>
							<div class='button' id='prev'><a href='#'>Prev</a></div>
							<div class='button' id='start'> <a href='#'>Start Over</a></div>
						</div>
						</div>

					</div>
				</div>
				
			</div>
			<!-- ======================= Home Banner ======================== -->

			

		