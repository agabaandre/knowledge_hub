<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
		</div>


		<div class="card-body text-left">
			<!-- Datatable -->
			<table id="datatable" class="table table-striped table-bordered" style="width:100%">
				<thead>
					<tr>
						<th>#</th>
						<th>ID</th>
						<th>Title</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>

					<?php $i = 1;
					foreach ($publications as $publication) : ?>
						<tr>
							<td width="5%"><?php echo $i++; ?> <i class="fa <?php $publication->file_type->icon; ?> fa-2x text-muted"></i></td>
							<td>
								<?php echo truncate($publication->title, 50); ?>
								<p><a href="<?php echo $publication->publication; ?>" target="_blank">View Publication</a>
								<p>
							</td>
							<td>
								<?php echo truncate($publication->description, 20); ?>
							</td>
							<td>
								<!-- <a href="<?php echo base_url('publications/edit/' . $publication->id); ?>" class="btn btn-primary btn-sm">Edit</a>
								<a href="<?php echo base_url('publications/delete/' . $publication->id); ?>" class="btn btn-danger btn-sm">Delete</a> -->

								<!-- Edit Modal Action -->
								<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal" data-id="<?php echo $publication->id; ?>" data-title="<?php echo $publication->title; ?>" data-description="<?php echo $publication->description; ?>" data-publication="<?php echo $publication->publication; ?>" data-file_type="<?php echo $publication->file_type_id; ?>">
									Edit
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
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Delete Modal -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="deleteModalLabel">Delete Publication</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="<?php echo base_url('publications/delete'); ?>" method="post" enctype="multipart/form-data">
					</form>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function() {
			$('#datatable').DataTable();
		});
	</script>
