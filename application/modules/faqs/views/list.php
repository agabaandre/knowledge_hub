	<!-- ======================= Publication Info ======================== -->
	<div class="bg-light  rounded py-5" style="background-image: url(<?php echo base_url() ?>resources/img/dots.png); background-repeat:repeat-x; background-size:contain;">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							<div class="jbd-01-thumb">
								
							</div>
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">
									<h4 class="mb-0 ft-medium fs-lg">
										FREQUENTLY ASKED QUESTIONS
									</h4>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= Publication Info ======================== -->
	<div class="container">

	<!-- All faqs -->
	<div class="row">
								<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 py-5">
									
									<!-- Single job -->
									<?php foreach($faqs as $row): ?>
									<div class="job_grid d-block border rounded px-3 pt-3 pb-2" >
										<div class="jb-list01 pl-2" style="border-left: 2px solid green;">
											<div class="jb-list-01-title">
												<h5 class="ft-medium mb-1">1. <?php echo $row->question; ?></h5>
												<p class="pl-2">
												 <i><?php echo $row->answer; ?></i>
												<br>
												</p>
											</div>
										
										</div>
									</div>
									<?php endforeach; ?>
									
							
								</div>
							</div>

	</div>

	