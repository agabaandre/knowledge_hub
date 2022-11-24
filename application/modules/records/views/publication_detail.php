	<!-- ======================= Publication Info ======================== -->
    <div class="bg-light rounded py-5" style="background-image: url(<?php echo base_url()?>resources/img/dots.png); background-repeat:repeat-x; background-size:contain;">
				<div class="container">
					<div class="row">
						<div class="col-xl-12 col-lg-12 col-md-12 col-12">
							<div class="jbd-01 d-flex align-items-center justify-content-between">
								<div class="jbd-flex d-flex align-items-center justify-content-start">
									<div class="jbd-01-thumb">
										<img src="<?php echo base_url(); ?>uploads/publications/<?php echo $publication->cover;?>" class="img-fluid" width="100" alt="" />
									</div>
									<div class="jbd-01-caption pl-3">
										<div class="tbd-title"><h4 class="mb-0 ft-medium fs-md">
                                            <?php echo $publication->title; ?>
                                        </h4></div>
										<div class="jbl_location mb-3">
											<span><?php echo $publication->theme->description; ?></span>
										</div>
										<div class="jbl_info01">
											<span class="px-2 py-1 ft-medium medium text-light theme-bg rounded mr-2"><?php echo $publication->sub_theme->description; ?></span>
										</div>
									</div>
								</div>
								<div class="jbd-01-right text-right">
									<div class="jbl_button mb-2"><a href="<?php echo $publication->publication; ?>" target="_blank"  class="btn btn-md rounded theme-bg-light theme-cl fs-sm ft-medium">Browse Resource</a></div>
                                    
                                    <?php if($publication->has_attachments): ?>
									<div class="jbl_button"><a href="<?php echo $publication->publication; ?>" target="_blank" class="btn btn-md rounded bg-white border fs-sm ft-medium"><i class="fa fa-download"></i> Attachment</a></div>
                                    <?php endif; ?>
                                </div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- ======================= Publication Info ======================== -->
    
    <!-- ============================ Publication Details Start ================================== -->
    <section class="py-5">
				<div class="container">
					<div class="row">
						
						<div class="col-xl-7 col-lg-7 col-md-7 col-sm-12">
							<div class="rounded mb-4">
								<div class="jbd-01 pr-3">
									<div class="jbd-details mb-4">
										<h5 class="ft-medium fs-md">Description</h5>
										<p><?php echo $publication->description; ?></p>
									</div>
									
									<div class="jbd-details mb-4">
										<h5 class="ft-medium fs-md text-success">Resource Details</h5>
										<div class="other-details">
											<div class="details ft-medium">
                                                <label class="text-muted">Author</label>
                                                <span class="text-dark"><?php echo $publication->author->name; ?></span>
                                            </div>
                                            <div class="details ft-medium">
                                                <label class="text-muted">No. of Visits</label>
                                                <span class="px-2 py-1 ft-medium medium text-light theme-bg rounded mr-2"><?php echo $publication->visits ?> Visits</span>
                                            </div>
                                            <div class="details ft-medium">
                                                <label class="text-muted">Type</label>
                                                <span class="text-dark"><?php echo $publication->file_type->name; ?></span>
                                            </div>
                                            <div class="details ft-medium">
                                                <label class="text-muted">Theme</label>
                                                <span class="text-dark"><?php echo $publication->theme->description; ?></span>
                                            </div>
                                            <div class="details ft-medium">
                                                <label class="text-muted">Sub-Theme</label>
                                                <span class="text-dark"><?php echo $publication->sub_theme->description; ?></span>
                                            </div>
                                        </div>
                                    </div>
									
								</div>
								
								<div class="jbd-02 pt-4 pr-3">
                                    <h5 class="text-bold text-success">Share this</h5>
									<div class="row justify-content-center">
										<?php  share_buttons(base_url("records/show/".$publication->id)); ?>
									</div>
								</div>
							</div>
							
						</div>
						
						<!-- Sidebar -->
						<div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
							<div class="jb-apply-form bg-white shadow rounded py-3 px-4 box-static">
								<h4 class="ft-medium fs-md mb-3">Got something to say about this resource?</h4>
								
                                <?php echo form_open('publications/comment', 'class="_apply_form_form"'); ?>
							
                                    <input type="hidden" name="publication_id" value="<?php echo $publication->id; ?>" />
									<div class="form-group">
										<label class="text-success mb-1 ft-medium medium">Your comment</label>
										<textarea name="comment" class="form-control" placeholder="Your comment"></textarea>
									</div>
									
									<div class="form-group">
										<button type="submit" class="btn btn-md rounded theme-bg text-light ft-medium fs-sm full-width">Submit Comment</button>
									</div>
									
                                <?php echo form_close(); ?>
							</div>
						</div>
						
					</div>
				</div>
			</section>
			