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
									<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<input type="text"  value="<?php echo old("term"); ?>" class="form-control sm left-ico autocomplete term custom-height-lg" name="term" placeholder="Enter keyword to search" />
											<i class="bnc-ico lni lni-search-alt"></i>
										</div>
									</div>
									<div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<select class="custom-select lg b-0" name="type">
											  <option value="" selected>All</option>
											  <?php foreach ($types as $type): ?>
											  <option value="<?php echo $type->id; ?>" 
											  	<?php echo (old("type")==$type->id)?"selected":"";?>
											  	><?php echo $type->name; ?></option>
											<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="col-xl-2 col-lg-2 col-md-2 col-sm-12 col-12">
										<div class="form-group mb-0 position-relative">
											<button class="btn full-width custom-height-lg theme-bg text-white fs-md" type="submit">Search</button>
										</div>
									</div>
								</div>
							<?php echo form_close(); ?>
						</div>
						
						
					</div>
				</div>
			</div>


