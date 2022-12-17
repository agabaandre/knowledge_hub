<div class="gray pt-2">
<div class="container">
<div class="row">
						
					
						<!-- Item Wrap Start -->
						<div class="col-lg-12 col-md-12 col-sm-12 ">
							
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-12">
									<div class="row align-items-center justify-content-between mx-0 bg-white rounded py-4 mb-4">
										<div class="col-xl-3 col-lg-4 col-md-5 col-sm-12">
											<h6 class="mb-0 ft-medium fs-sm"><?php echo $rows_count; ?> Discussions Available</h6>
										</div>

										<?php if(!is_guest()): ?>
											<a href="<?php echo base_url('forums/create'); ?>"  class="btn btn-outline-success btn-sm text-bold mr-4">
												<i class="fa fa-edit mr-2"></i>Start New Discussion
											</a>
										<?php endif; ?>

										
									</div>
								</div>
							</div>
						
							<!-- All jobs -->
							<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
									
									<!-- Single job -->
									<?php foreach($forums as $forum): ?>
									<div class="job_grid d-block border rounded px-3 pt-3 pb-2">
										<div class="jb-list01">
											<div class="jb-list-01-title">
											       <a href="<?php echo base_url("forums/detail/"); ?><?php echo $forum->id; ?>">
														<h5 class="ft-medium mb-1"><?php echo $forum->forum_title; ?></h5>
														<p>
														<?php echo truncate($forum->forum_description,400); ?>
													</a>
													<br>
													<a class="text-success" href="<?php echo base_url("forums/detail/"); ?><?php echo $forum->id; ?>">View Details</a>
													</p>
											</div>
											<div class="jb-list-01-info d-block mb-3">
											   <span class="text-muted mr-2"><i class="lni lni-alarm-clock mr-1"></i>
												<?php echo time_ago($forum->created_at); ?>
											   </span>
											</div>
											<div class="jb-list-01-title">
											<?php foreach($forum->tags as $tag): ?>
												<span class="mr-2 mb-2 d-inline-flex px-2 py-1 rounded theme-cl theme-bg-light"><?php echo $tag->tag; ?></span>
											<?php endforeach; ?>
											</div>
										</div>
									</div>
									<?php endforeach; ?>
									
							
								</div>
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

