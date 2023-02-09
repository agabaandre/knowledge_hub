		
			<!-- ======================= Newsletter Start ============================ -->
			<section class="space bg-cover" style="background:#03343b url(<?php echo base_url(); ?>resources/img/landing-bg.png) no-repeat;">
				<div class="container py-5">
					
					<div class="row justify-content-center">
						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
							<div class="sec_title position-relative text-center mb-5">
								<h6 class="text-light mb-0">Subscribe Now</h6>
								<h2 class="ft-bold text-light">Get All New Updates</h2>
							</div>
						</div>
					</div>
					
					<div class="row align-items-center justify-content-center">
						<div class="col-xl-7 col-lg-10 col-md-12 col-sm-12 col-12">

							<?php if ($this->session->flashdata('error_message')): ?>
								<div class="form-group mt-2">
									<div class="alert alert-danger">
										<?php echo $this->session->flashdata('error_message'); ?>
									</div>
								</div>
							<?php endif; ?>

							<?php if ($this->session->flashdata('success_message')): ?>
								<div class="form-group mt-2">
									<div class="alert alert-success">
										<?php echo $this->session->flashdata('success_message'); ?>
									</div>
								</div>
							<?php endif; ?>


							
							<?php echo form_open(base_url('subscription/index'), ['class' => 'bg-white rounded p-1', 'id' => 'subscription-form']); ?>
								<div class="row no-gutters">
									<div class="col-xl-9 col-lg-9 col-md-8 col-sm-8 col-8">
										<div class="form-group mb-0 position-relative">
											<input id="subscribe-email" type="text" class="form-control lg left-ico" placeholder="Enter Your Email Address" name="email">
											<i class="bnc-ico lni lni-envelope"></i>
										</div>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 col-4">
										<div class="form-group mb-0 position-relative">
											<button type="submit" id="subscribe-bt" class="btn full-width custom-height-lg theme-bg text-light fs-md">Subscribe</button>
										</div>
									</div>
								</div>
								
							</form>
						</div>
					</div>
					
				</div>
			</section>
			<!-- ======================= Newsletter Start ============================ -->