	<!-- ======================= Searchbar Banner ======================== -->
	<div class="py-3 theme-bg" style="margin-top: 20px;">
				<div class="container">
					<div class="row justify-content-between align-items-center">

                    
						<!-- <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
							<div class="d-block position-relative text-right">
								<a href="#"  class="mlb-btn btn ft-medium rounded text-dark bg-light"><i class="ti-bell mr-1"></i>Job Alert</a>
							</div>
						</div> -->

						<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">							
							<?php echo form_open('records/search', 'class="search-form bg-white rounded p-1 border"'); ?>
								<div class="row no-gutters">
									<div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text"  value="<?php echo old("term"); ?>" class="form-control sm left-ico autocomplete term" name="term" placeholder="Job Title, Keyword or Company" />
											<i class="bnc-ico lni lni-search-alt"></i>
										</div>
									</div>
									<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width custom-height sm rounded bg-dark text-white fs-md" type="submit">Search</button>
										</div>
									</div>
								</div>
							<?php echo form_close(); ?>
						</div>
						
						
					</div>
				</div>
			</div>


