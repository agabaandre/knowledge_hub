			

            	
			<!-- ======================= Top Searches List ======================== -->
			<section class="middle">
				<div class="container">
				
					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="sec_title position-relative text-center mb-5">
								<h2 class="ft-bold">Top<span class="theme-cl">Searches</span></h2>
							</div>
						</div>
					</div>
					
					<!-- row -->
					<div class="row align-items-center">
					
					<?php foreach($recent as $row): ?>
						<!-- Single -->
						<a href="<?php echo base_url("records/show/").$row->id; ?>">
							<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
								<div class="jbr-wrap text-left border rounded">
									<div class="cats-box mlb-res rounded bg-white d-flex align-items-center justify-content-between px-3 py-3">
										<div class="cats-box rounded bg-white d-flex align-items-center">
											<div class="text-center"><img src="<?php echo base_url();?>assets/images/icons/author.png" class="img-fluid" width="55" alt=""></div>
											<div class="cats-box-caption px-2">
												<h4 class="fs-md mb-0 ft-medium"><?php echo truncate($row->title,40); ?></h4>
												<div class="d-block mb-2 position-relative">
													<span class="text-muted medium">
														Author: <i class="fa fa-bank mr-1"></i><?php echo $row->author->name; ?></span>
													<span class="muted medium ml-2 theme-cl"><br><i class="lni lni-briefcase mr-1"></i>Sub Theme: <?php echo $row->sub_theme->description; ?></span>
													<span class="text-muted medium d-block">
														Last updated: <i class="fa fa-bank mr-1"></i><?php echo time_ago($row->updated_at); ?>
												</div>
											</div>
										</div>
										<div class="text-center mlb-last"><a href="<?php echo base_url("records/show/").$row->id; ?>"  class="btn gray ft-medium apply-btn fs-sm rounded">Browse</a></div>
									</div>
								</div>
							</div>
						</a>

						<?php endforeach; ?>
						
					</div>
					<!-- row -->
					
					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="position-relative text-center">
								<a href="<?php echo base_url("records/search"); ?>" class="btn btn-md theme-bg rounded text-light hover-theme">Explore More Resources<i class="lni lni-arrow-right-circle ml-2"></i></a>
							</div>
						</div>
					</div>
					
				</div>
			</section>
			<!-- ======================= Top Searches ======================== -->
		