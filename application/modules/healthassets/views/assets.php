<div class="gray pt-2">
<div class="container">
<div class="row">
						
					
						<!-- Item Wrap Start -->
						<div class="col-lg-12 col-md-12 col-sm-12 ">
							
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-12">
									<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
										<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
											<h6 class="mb-0 ft-medium fs-sm">
												<?php echo $rows_count; ?> <?php echo (isset($_GET['slug']) &&!empty($_GET['slug']))?$assets[0]->type->type_name:'Resource ';?><?php ($rows_count>1)?'s':''; ?> Available</h6>
										</div>
										
										
									</div>
								</div>
							</div>
						
							<!-- All jobs -->
							<div class="row">
									
									<!-- Single job -->
									<?php foreach($assets as $asset): ?>
										<div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
								
									<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
										<div class="jb-list01">
											<div class="jb-list-01-title">
											       <a href="<?php echo base_url("healthassets/detail/"); ?><?php echo $asset->id; ?>">
														<h5 class="ft-medium mb-1"><?php echo $asset->asset_name; ?></h5>
														<p><?php echo $asset->type->type_name; ?></p>
														<hr>
														<a href="<?php echo $asset->url; ?>" target="_blank" class="text-success"><?php echo $asset->url; ?></a>
														<p><?php echo truncate($asset->asset_desc,182); ?></p>
													</a>
													<a class="text-success" href="<?php echo base_url("healthassets/detail/"); ?><?php echo $asset->id; ?>">View Details</a>
													</p>
											</div>
											<div class="jb-list-01-info d-block mb-3">
											   <span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
												Added: <?php echo time_ago($asset->created_at); ?>
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

