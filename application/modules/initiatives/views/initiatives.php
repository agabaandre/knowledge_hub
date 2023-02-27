<div class="gray pt-2">
<div class="container">
<div class="row">
						
					
						<!-- Item Wrap Start -->
						<div class="col-lg-12 col-md-12 col-sm-12 ">
							
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-12">
									<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
										<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
											<h6 class="mb-0 ft-medium fs-sm"><?php echo $rows_count; ?> Public Health Initiatives Available</h6>
										</div>

										
									</div>
								</div>
							</div>
						
							<!-- All jobs -->
							<div class="row">
									
									<!-- Single job -->
									<?php foreach($initiatives as $initiative): ?>
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								
									<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
										<div class="jb-list01">
											<div class="jb-list-01-title">
														<h5 class="ft-medium mb-1"><?php echo $initiative->initiative_title; ?></h5>
														<p><?php echo truncate($initiative->initiative_description,200); ?></p>
													</p>
											</div>
											<div class="jb-list-01-info d-block mb-3">
											   <span class="text-success mr-2"><i class="lni lni-alarm-clock mr-1"></i>
												Added: <?php echo time_ago($initiative->created_at); ?>
											   </span>
											</div>
										</div>
									</div>
								</div>
									<?php endforeach; ?>
									
							
							</div>
							
							<div class="row">
								<div class="col-lg-12 col-md-12 col-sm-12">
									<?php echo $links; ?>
								</div>
							</div>
							
						</div>
						
					</div>
</div>
</div>

