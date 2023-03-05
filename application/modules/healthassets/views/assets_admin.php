<div class="card col-md-12">

	<!-- Card Header with tools -->
	<div class="card-header">
		<div class="card-title">
			<h4>Health Asset Admin</h4>
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
								<th>Health Asset Status</th>
								<th>Health Asset Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($healthassets as $healthasset) : ?>
								<tr>
									<td>
										<!-- Clickable title -->
										<a href="<?php echo base_url('healthassets/detail/' . $healthasset->id); ?>">
											<h4><?php echo $healthasset->asset_title; ?></h4>
										</a>
										<?php echo truncate($healthasset->asset_desc, 400); ?>
									</td>

									<td><?php echo $healthasset->status; ?></td>
									<td>
										
										<!-- Edit Button -->
										<button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#edit-healthasset-modal" data-id="<?php echo $healthasset->id; ?>" data-title="<?php echo $healthasset->asset_title; ?>" data-description="<?php echo $healthasset->asset_desc; ?>">
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


<!-- Approve healthasset modal -->
<?php include 'includes/approve-healthasset-modal.php'; ?>

<!-- Delete healthasset modal -->
<?php include 'includes/delete-modal.php'; ?>

<!-- Edit healthasset modal -->
<?php include 'includes/edit-healthasset-modal.php'; ?>

<!-- Script to approve healthasset -->
<script>
	// Check document is ready
	$(document).ready(function() {
		$('#approve-healthasset-modal').on('show.bs.modal', function(event) {
			console.log('approve healthasset modal')
			var button = $(event.relatedTarget) // Button that triggered the modal
			var id = button.data('id') // Extract info from data-* attributes
			var title = button.data('title')
			var description = button.data('description')

			// Update the modal's content.
			var modal = $(this)
			modal.find('#title').text(title)
			modal.find('#description').html(description)

			// Approve healthasset link
			var approveHealth AssetLink = "<?php echo base_url('healthassets/approve_healthasset/'); ?>" + id;
			$('#approveHealth AssetLink').attr('href', approveHealth AssetLink);

			$('#approveHealth AssetLink').on('click', function(e) {
				e.preventDefault();
				Swal.fire({
					title: 'Confirm Approval?',
					text: "Appove healthasset '" + title + "'?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, approve it!'
				}).then((result) => {
					if (result.isConfirmed) {
						// Redirect to approve healthasset link
						// Make Ajax Request
						$.ajax({
							url: approveHealth AssetLink,
							type: 'GET',
							dataType: 'json',
							success: function(data) {
								if (data.status == 'success') {
									Swal.fire({
										title: 'Success',
										text: data.message,
										icon: 'success',
										confirmButtonText: 'Ok'
									}).then((result) => {
										if (result.value) {
											location.reload();
										}
									})
								}
							}
						})
					}
				})
			});


			// Reject healthasset link
			var rejectHealth AssetLink = "<?php echo base_url('healthassets/reject_healthasset/'); ?>" + id;
			$('#rejectHealth AssetLink').attr('href', rejectHealth AssetLink);

			// On Click Reject
			$('#rejectHealth AssetLink').on('click', function(e) {
				e.preventDefault();
				Swal.fire({
					title: 'Are you sure?',
					text: "You won't be able to revert this!",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, reject it!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
							url: rejectHealth AssetLink,
							type: 'GET',
							dataType: 'json',
							success: function(data) {
								if (data.status == 'success') {
									Swal.fire({
										title: 'Success',
										text: data.message,
										icon: 'success',
										confirmButtonText: 'Ok'
									}).then((result) => {
										if (result.value) {
											location.reload();
										}
									})
								}
							}
						})
					}
				})
			})
		});
	});
</script>