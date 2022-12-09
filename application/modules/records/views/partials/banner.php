      
			<!-- ======================= Home Banner ======================== -->
			<div class="home-banner margin-bottom-0 intro-bg">
				<div class="container">
					
					<div class="row align-items-center justify-content-between">
						<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
							<div class="banner_caption text-left mb-4">

								<h1 class="banner_title ft-bold mb-1">Explore Africa's Health Data</h1>
								
							</div>
							<?php echo form_open('records/search', 'class="bg-white rounded p-1 search-form"'); ?>
							<div class="row no-gutters">
									<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text" class="form-control lg left-ico autocomplete term" name="term" value="<?php echo old("term"); ?>" placeholder="Enter keyword to search" />
											<i class="bnc-ico lni lni-search-alt"></i>
										</div>
									</div>
									<div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<select class="custom-select lg b-0" name="type">
											  <option value="" disabled selected>Choose Type</option>
											  <?php foreach ($types as $type): ?>
											  <option value="<?php echo $type->id; ?>" 
											  	<?php echo (old("type")==$type->id)?"seleced":"";?>
											  	><?php echo $type->name; ?></option>
											<?php endforeach; ?>
											</select>
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

                                <?php 

                                $colors = ['dark','blue','green'];

                                foreach ($tags as $tag): ?>
                                    <span class="px-3 py-1 medium bg-<?php echo $colors[mt_rand(0,2)]; ?> text-white rounded"><?php echo $tag->tag_text; ?></span>
                                 <?php endforeach; ?>

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

			

		