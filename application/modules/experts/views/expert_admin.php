<div class="card col-md-12">

	<!-- Card Header with tools -->
	<div class="card-header">
		<div class="card-title">
			<h4>Experts List</h4>
			<a href="#create" data-toggle="modal" class="btn btn-success float-end"> <i class="fa fa-plus-circle"></i> Add New Expert</a>
		</div>
	</div>

	<!-- Card Body -->
	<div class="card-body">

		<style>
			td {
				word-wrap: break-word;
				overflow: hidden;
				max-width: calc(100vw - 500px);
			}
		</style>
		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped table-bordered table-hover" id="expert-approval-table">
						<thead>
							<tr>
								<th>Expert Name</th>
								<th>Expert Category</th>
								<th>Phone Number</th>
								<th>Email</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($experts as $expert) : ?>
								<tr>
									<td>
										<h4><?php echo $expert->first_name." ".$expert->last_name; ?></h4>
										<small class="text-muted">Position:</small><br>
										<?php echo truncate($expert->job_title, 350); ?>
									</td>
									<td><?php echo $expert->expert_type; ?></td>
									<td><?php echo $expert->phone_number; ?></td>
									<td><?php echo $expert->email; ?></td>
									<td>
										
										<!-- Edit Button -->
										<button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal">
											Edit
										</button>

										<!-- Delete Button -->
										<a class="btn btn-sm btn-danger btn-block" href="javascript:void(0);" onclick="openDeleteModal(<?php echo $expert->id; ?>)" class="text-danger">Delete</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


</div>

<!-- Delete expert modal -->
<?php include 'includes/delete-modal.php'; ?>

<?php include 'includes/create-modal.php'; ?>

