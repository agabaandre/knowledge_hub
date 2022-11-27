      
			<!-- ======================= Home Banner ======================== -->
			<div class="home-banner margin-bottom-0 intro-bg">
				<div class="container">
					
					<div class="row align-items-center justify-content-between">
						<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
							<div class="banner_caption text-left mb-4">

								<div class="d-block mb-2"><span class="px-3 py-1 medium theme-bg-light theme-cl rounded">Africa CDC</span></div>
								<h1 class="banner_title ft-bold mb-1">Explore Africa's Health Data</h1>
								
							</div>
							<?php echo form_open('records/search', 'class="bg-white rounded p-1 search-form"'); ?>
							<div class="row no-gutters">
									<div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text" class="form-control lg left-ico autocomplete term" name="term" value="<?php echo old("term"); ?>" placeholder="Enter keyword to search" />
											<i class="bnc-ico lni lni-search-alt"></i>
										</div>
									</div>
									<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width custom-height-lg theme-bg text-white fs-md" type="submit">Search</button>
										</div>
									</div>
								</div>
								<?php echo form_close(); ?>

                            

                            <div class="d-block mb-2 mt-3">
                                <span class="text-muted">Tags:</span>
                                    <span class="px-3 py-1 medium bg-dark text-white rounded">HIV AIDs</span>
                                <span class="px-3 py-1 medium bg-info text-white rounded">Population</span>
                                <span class="px-3 py-1 medium bg-blue text-white rounded">Others</span>
                            </div>

                            <div class="quotes-slider px-3 col-lg-8">
								
								<!-- single review -->
								<?php foreach($quotes as $quote): ?>

								<div class="single_review px-2">
									<div class="reviews_wrap position-relative rounded py-1 px-2">
										
										<div class="rw-header d-flex mt-3 text-bold">
											<p class="text-bold"><?php echo nl2br($quote->quote); ?></p>
										</div>
									</div>
								</div>

								<?php endforeach; ?>

							</div>

							
						</div>
						<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-12">
							<div class="bnr_thumb position-relative">
								<img src="<?php echo base_url(); ?>resources/img/bn-3.png" class="img-fluid bnr_img" alt="" />
								<div class="list_crs_img">
									<img src="<?php echo base_url(); ?>resources/img/favs/health-check.png" class="img-fluid elsio cirl animate-fl-y" alt="" style="min-width: 170px; cursor: pointer;"
									onclick="$('.quiz').show();">

									<img src="<?php echo base_url(); ?>resources/img/favs/africa.png" class="img-fluid elsio moon animate-fl-x" alt="" style="min-width: 170px;">

									<img src="<?php echo base_url(); ?>resources/img/favs/demographics.png" class="img-fluid elsio arrow animate-fl-y" alt="" style="min-width: 170px;">
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

			

		