			<!-- ======================= Top Searches List ======================== -->
			<section class="middle">
				<div class="container">

					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="sec_title position-relative text-center mb-5">
								<h2 class="ft-bold">Top Searches</span></h2>
							</div>
						</div>
					</div>

					<!-- row -->
					<div class="row align-items-center">

						<?php 

							$i=0;
						   foreach ($recent as $row) :
							$i++;
						 ?>
							<!-- Single -->
							<a href="<?php echo base_url("records/show/") . $row->id; ?>">
								<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12">
									<div class="jbr-wrap text-left border rounded">
										<div class="cats-box mlb-res rounded bg-white d-flex align-items-center px-3 py-3">
											<div class="cats-box rounded bg-white d-flex align-items-center" style="min-width:75%;">
												<div class="text-center"><img src="<?php echo base_url(); ?>assets/images/icons/author.png" class="img-fluid" width="55" alt=""></div>
												<div class="cats-box-caption">
													<h4 class="fs-md mb-0 ft-medium"><?php echo truncate($row->title, 40); ?></h4>
													<div class="d-block mb-2 position-relative" >
													  <p class="text-nothern p-0"><?php echo nl2br(truncate($row->description,60)); ?></p>
														<span class="text-muted medium">
															Source: <i class="fa fa-bank mr-1"></i><?php echo $row->author->name; ?></span>
														
														<span class="muted medium ml-2 theme-cl"><br>
														<i class="lni lni-briefcase mr-1"></i>Theme: <?php echo $row->theme->description; ?></span>
															<span class="muted medium ml-2 theme-cl"><br>
														<i class="lni lni-archive mr-1"></i>Sub Theme: <?php echo $row->sub_theme->description; ?></span>
														
														<span class="muted medium ml-2 text-muted mt-1 "><br>
														<i class="lni lni-empty-file mr-1"></i>Type: <?php echo $row->file_type->name; ?></span>
														
														<span class="text-muted medium d-block mt-1">
														    <span class=" mr-2"><i class="lni lni-calendar mr-1"></i>Last updated: <?php echo time_ago($row->updated_at); ?> </span>
															<span class=" mr-2"><i class="fa fa-eye mr-1"></i><?php echo $row->visits; ?> Views </span>
														    <span><a  class="mr-1 ml-2 text-muted d-inline" data-toggle="collapse" href="#comments<?php echo $i; ?>"  aria-expanded="false" aria-controls="comments<?php echo $i; ?>"><i class="fa fa-comments"></i> <?php echo count($row->comments); ?> Comments</a></span>
															<?php if(!is_guest()): ?>
															<div class="btn btn-outline-dark btn-sm mt-2 favbtn">
														      <?php include 'favourites_btn.php'; ?>
															</div>
															<?php endif; ?>
														</span>   

													</div>
												</div>
											</div>
											<div class="text-center mlb-last"><a href="<?php echo base_url("records/show/") . $row->id; ?>" class="btn gray ft-medium apply-btn fs-sm rounded">Browse</a></div>
										</div>

										<?php if(count($row->comments)>0): ?>
										<div class="collapse px-3 py-3" id="comments<?php echo $i; ?>">
											<h5>Comments</h5>
											<ul class="list-group">
												<?php 
												$com_count=0;
												foreach($row->comments as $comm): 
												$com_count++;
												if($com_count<6):
												?>
												<li class="app-comment">
												 <h6><small><?php echo $comm->user->name; ?></small></h6>
												 <p><?php echo $comm->comment; ?></p>
												 <small class="text-success"><?php echo time_ago($comm->created_at); ?></small>
												</li>
												<?php endif; endforeach; ?>
												<a class="py-3" href="<?php echo base_url("records/show/") . $row->id; ?>">View All</a>
											</ul>
										</div>
										<?php endif; ?>


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