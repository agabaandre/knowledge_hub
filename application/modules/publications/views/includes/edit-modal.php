<!-- Edit Modal -->
<div class="modal fade" id="edit-publication-modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editModalLabel">Edit Publication</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- <form action="<?php echo base_url('publications/edit'); ?>" method="post" enctype="multipart/form-data"> -->
                    <?php echo form_open_multipart('publications/edit', array('id' => 'edit-publication-form')); ?>

						<input type="hidden" name="id" id="id" value="">
						<div class="form-group">
							<label for="title">Title</label>
							<input type="text" name="title" id="title" class="form-control" value="">

						</div>

						<div class="form-group">
							<label for="description">Description</label>
							<textarea id="publication_description" name="description" style="min-height: 300px;"></textarea>
						</div>

						<div class="mb-3">
							<label for="publication" class="form-label">Author</label>
							<select class="form-control" name="author" required id="authors">
								<option disabled>Select</option>
							</select>
						</div>


                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>

					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
