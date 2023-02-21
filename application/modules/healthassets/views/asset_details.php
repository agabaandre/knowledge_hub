	<!-- ============================ Blog Detail Start ================================== -->
			<section style="padding-top:70px;" class="gray">
			
				<div class="container">
				
					<!-- row Start -->
					<div class="row">
					
						<!-- Blog Detail -->
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="article_detail_wrapss single_article_wrap format-standard">
								<div class="article_body_wrap">
                          
									<h2 class="post-title"><?php echo $asset->asset_name; ?></h2>
									
									<div class="article_top_info">
										<ul class="article_middle_info">
                                            <li><?php echo $asset->type->type_name; ?></li>
										</ul>
									</div>

									<hr>

                                     <p><?php echo $asset->asset_desc; ?></p>

									 <div class="article_top_info py-2">
										<ul class="article_middle_info">
                                            <li class="d-block py-1"><a href="<?php echo $asset->url; ?>" target="_blank"><strong><i class="fa fa-link"></i> URL:</strong> <span class="text-success"><?php echo $asset->url; ?></span></a></li>
											<li class="d-block py-1"><strong><i class="fa fa-map-pin"></i>  Region:</strong> <?php echo $asset->region->region_name; ?></li>
										</ul>
									</div>
								</div>
							</div>
							
							
							
						</div>
						
						
						
					</div>
					<!-- /row -->					
					
				</div>
						
			</section>