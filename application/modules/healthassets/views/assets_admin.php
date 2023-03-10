<div class="card col-md-12">

	<!-- Card Header with tools -->
	<div class="card-header">
		<div class="card-title">
			<h4>Health Assets</h4>
			<a href="#create" data-toggle="modal" class="btn btn-success float-end"> <i class="fa fa-plus-circle"></i> Add New Asset</a>
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
					<table class="table table-striped table-bordered table-hover" id="healthasset-approval-table">
						<thead>
							<tr>
								<th>Health Asset</th>
								<th>Health URL</th>
								<th>Type</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($assets as $healthasset) : ?>
								<tr>
									<td>
										<!-- Clickable title -->
										<a href="<?php echo base_url('healthassets/detail/' . $healthasset->id); ?>">
											<h4><?php echo $healthasset->asset_name; ?></h4>
										</a>
										<small class="text-muted">Description:</small><br>
										<?php echo truncate($healthasset->asset_desc, 350); ?>
									</td>

									<td><?php echo $healthasset->url; ?></td>
									<td><?php echo $healthasset->type->type_name; ?></td>
									<td>
										
										<!-- Edit Button -->
										<button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#edit-healthasset-modal" data-id="<?php echo $healthasset->id; ?>" data-title="<?php echo $healthasset->asset_name; ?>" data-description="<?php echo $healthasset->asset_desc; ?>">
											Edit
										</button>

										<!-- Delete Button -->
										<a class="btn btn-sm btn-danger btn-block" href="javascript:void(0);" onclick="openDeleteModal(<?php echo $healthasset->id; ?>)" class="text-danger">Delete</a>
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

<!-- Delete healthasset modal -->
<?php include 'includes/delete-modal.php'; ?>

<?php include 'includes/create-modal.php'; ?>

