	<section class="middle gray">
		<div class="container">

			<div class="row justify-content-center">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
					<div class="sec_title position-relative text-center mb-5">
						<h2 class="ft-bold">Recommended</span></h2>
					</div>
				</div>
			</div>

			<div class="row justify-content-center">
				<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
					<div class="review-slide px-3">



						<!-- single review -->
						<?php foreach ($featured as $row) : ?>
							<a href="<?php echo base_url("records/show/") . $row->id; ?>">
								<div class="single_review px-2">
									<div class="reviews_wrap position-relative bg-white rounded py-4 px-4">
										<div class="rw-header d-flex align-items-center justify-content-start">
											<div class="rv-110-caption pl-3">
												<h4 class="ft-medium fs-md mb-0 lh-1"><?php echo truncate($row->title, 30); ?></h4>
												<p class="p-0 m-0">Source: <i class="fa fa-bank mr-1"></i><?php echo $row->author->name; ?></p>
											</div>
										</div>
										<div class="">
											<span class="muted medium ml-2 theme-cl"><br><i class="lni lni-briefcase mr-1"></i>Sub Theme: <?php echo $row->sub_theme->description; ?></span>
											<span class="text-muted medium d-block">
												Last updated: <i class="fa fa-bank mr-1"></i><?php echo time_ago($row->updated_at); ?></span>
										</div>
									</div>
								</div>
							</a>
						<?php endforeach; ?>

					</div>
				</div>
			</div>
		</div>
	</section>