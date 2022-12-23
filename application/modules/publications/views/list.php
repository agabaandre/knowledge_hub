<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
		</div>


		<!-- Card Header With Form Filters -->
		<div class="card-header">


			<div class="container">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="title">Title</label>
							<input type="text" name="title" id="filterTitle" class="form-control">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="source">Source / Author</label>
							<input type="text" name="source" id="filterSource" class="form-control" value="">
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="file_type">File Type</label>
							<select name="file_type" id="file_type" class="form-control">
								<option value="">Select File Type</option>
								<?php foreach ($file_types as $file_type) : ?>
									<option value="<?php echo $file_type->id; ?>"><?php echo $file_type->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12 text-right">
						<!-- Export Button -->
						<button type="button" id="exportButton" class="btn btn-success btn-sm">Export Data</button>
						<button type="button" id="filterButton" class="btn btn-primary btn-sm">Filter Data</button>
						<button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
					</div>
				</div>
			</div>


		</div>


		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="publicationTable" class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Description</th>
						<th>Author</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					<?php $i = 1;

					// dd($publications);

					foreach ($publications as $publication) : ?>
						<tr>
							<td width="5%"><?php echo $i++; ?> <i class="fa <?php $publication->file_type->icon; ?> fa-2x text-muted"></i></td>
							<td>
								<?php echo truncate($publication->title, 30); ?>
								<p><a href="<?php echo $publication->publication; ?>" target="_blank">View Publication</a>
								<p>
							</td>
							<td>
								<?php echo truncate($publication->description, 50); ?>
							</td>
							<td>
								<?php echo $publication->author->name ?? ''; ?>
							</td>
							<td>
								<?php echo $publication->is_active; ?>
							</td>
							<td>
								<!-- <a href="<?php echo base_url('publications/edit/' . $publication->id); ?>" class="btn btn-primary btn-sm">Edit</a>
								<a href="<?php echo base_url('publications/delete/' . $publication->id); ?>" class="btn btn-danger btn-sm">Delete</a> -->

								<!-- Edit Modal Action -->
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal" data-id="<?php echo $publication->id; ?>" data-title="<?php echo $publication->title; ?>" data-description="<?php echo $publication->description; ?>" data-publication="<?php echo $publication->publication; ?>" data-file_type="<?php echo $publication->file_type_id; ?>">
									Edit
								</button>

								<!-- Delete Modal Action -->
								<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $publication->id; ?>" data-title="<?php echo $publication->title; ?>" data-description="<?php echo $publication->description; ?>" data-publication="<?php echo $publication->publication; ?>" data-file_type="<?php echo $publication->file_type_id; ?>">
									Delete
								</button>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
		</div>

	</div>


	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editModalLabel">Edit Publication</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url('publications/edit'); ?>" method="post" enctype="multipart/form-data">

						<input type="hidden" name="id" id="id" value="">
						<div class="form-group">
							<label for="title">Title</label>
							<input type="text" name="title" id="title" class="form-control" value="">

						</div>

						<div class="form-group">
							<label for="description">Description</label>
							<textarea name="description" id="description" class="form-control"></textarea>
						</div>

						<div class="form-group">
							<label for="publication">Publication</label>
							<input type="file" name="publication" id="publication" class="form-control">
						</div>

						<div class="form-group">
							<label for="file_type">File Type</label>
							<select name="file_type" id="file_type" class="form-control">
								<?php foreach ($file_types as $file_type) : ?>
									<option value="<?php echo $file_type->id; ?>"><?php echo $file_type->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>


	<script>
		$(document).ready(function() {
			var table = $('#publicationTable').DataTable({

				"dom": 'bootstrap',
				"buttons": [
					'copy', 'csv', 'excel', 'pdf',
				]
			});


			var filterButton = $('#filterButton');

			var exportButton = $('#exportButton');

			filterButton.on('click', function() {
				var filterTitle = $('#filterTitle').val();
				var filterSource = $('#filterSource').val();

				// Apply both filters
				table.columns(1).search(filterTitle).draw();
				table.columns(2).search(filterSource).draw();
			});

			exportButton.on('click', function() {

				// If filter has value, apply filter to table and export
				if ($('#filterTitle').val()) {
					var filter = $('#filterTitle').val();
					table.search(filter).draw();
					table.button(1).trigger();
				} else {
					table.button(1).trigger();
				}
			});
		});

	
		// On Edit Modal Shown Event
		$('#editModal').on('show.bs.modal', function(event) {
			console.log('Edit Modal Event Triggered');
		});

		// Sweet Alert Delete Confirmation
		$('#deleteModal').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget);
			var id = button.data('id');
			var title = button.data('title');
			var modal = $(this);

			modal.find('.modal-title').text('Delete Publication: ' + title);
			modal.find('#delete_id').val(id);

			$('#deleteForm').on('submit', function(e) {
				e.preventDefault();
				var form = $(this);
				var url = form.attr('action');

				$.ajax({
					type: 'POST',
					url: url,
					data: form.serialize(),
					success: function(response) {
						console.log(response);
						Swal.fire({
							title: 'Success!',
							text: 'Publication Deleted Successfully!',
							type: 'success',
							confirmButtonText: 'Ok'
						}).then((result) => {
							if (result.value) {
								window.location.href = '<?php echo base_url('publications'); ?>';
							}
						});
					}
				});
			});
		});
	</script>
