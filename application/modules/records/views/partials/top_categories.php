			<!-- ================================ Tag Award ================================ -->
			<section class="p-0 ">
				<div class="container">
					<div class="row justify-content-center">
						<div class="col-lg-12 col-md-12 col-sm-12">
							<div class="crp_box fl_color ovr_top">
								<div class="row align-items-center">
								
								<?php foreach($categories as $category): ?>
										
								<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
								<a href="<?php echo base_url(); ?>browse/<?php echo $category['link']; ?>">
										<div class="dro_140">
											<div class="dro_141 de"><i class="<?php echo $category['icon']; ?>"></i></div>
											<div class="dro_142">
												<h6><?php echo $category['title']; ?></h6>
												<p><?php echo  $category['stats']; ?></p>
											</div>
										</div>
								</a>
									</div>
								<?php endforeach; ?>

								</div>

							</div>
						</div>
					</div>
				
				</div>
			</section>
		