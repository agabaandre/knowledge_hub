<div class="card col-md-12">

	<!-- Card Header with tools -->
	<div class="card-header">
		<div class="card-title">
			<h4>Forum Admin</h4>
		</div>
		<!-- <div class="card-tools text-right">
			<a href="<?php echo base_url('forums/admin/add_forum'); ?>" class="btn btn-primary btn-sm">Add Forum</a>
		</div> -->
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
					<table class="table table-striped table-bordered table-hover" id="forum-approval-table">
						<thead>
							<tr>
								<th>Forum</th>
								<th>Forum Status</th>
								<th>Forum Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($forums as $forum) : ?>
								<tr>
									<td>
										<!-- Clickable title -->
										<a href="<?php echo base_url('forums/detail/' . $forum->id); ?>">
											<h4><?php echo $forum->forum_title; ?></h4>
										</a>
										<?php echo truncate($forum->forum_description, 400); ?>
									</td>

									<td><?php echo $forum->status; ?></td>
									<td>
										<!-- Approve Button -->
										<?php if ($forum->status == 'pending') : ?>

											<!-- Approve modal action -->
											<button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#approve-forum-modal" data-id="<?php echo $forum->id; ?>" data-title="<?php echo $forum->forum_title; ?>" data-description="<?php echo $forum->forum_description; ?>">
												Approve
											</button>


											<!-- <a href="<?php echo base_url('forums/admin/approve_forum/' . $forum->id); ?>" class="btn btn-success btn-sm btn-block">Approve</a> -->
										<?php endif; ?>
										<!-- <a href="<?php echo base_url('forums/admin/edit_forum/' . $forum->id); ?>" class="btn btn-primary btn-sm btn-block">Edit</a>
									<a href="<?php echo base_url('forums/admin/delete_forum/' . $forum->id); ?>" class="btn btn-danger btn-sm btn-block">Delete</a> -->

										<!-- Edit Button -->
										<button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#edit-forum-modal" data-id="<?php echo $forum->id; ?>" data-title="<?php echo $forum->forum_title; ?>" data-description="<?php echo $forum->forum_description; ?>">
											Edit
										</button>

										<!-- Delete Button -->
										<a class="btn btn-sm btn-danger btn-block" href="javascript:void(0);" onclick="openDeleteModal(<?php echo $forum->id; ?>)" class="text-danger">Delete</a>
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


<!-- Approve forum modal -->
<?php include 'includes/approve-forum-modal.php'; ?>

<!-- Delete forum modal -->
<?php include 'includes/delete-modal.php'; ?>

<!-- Edit forum modal -->
<?php include 'includes/edit-forum-modal.php'; ?>

<!-- Script to approve forum -->
<script>
	// Check document is ready
	$(document).ready(function() {
		$('#approve-forum-modal').on('show.bs.modal', function(event) {
			console.log('approve forum modal')
			var button = $(event.relatedTarget) // Button that triggered the modal
			var id = button.data('id') // Extract info from data-* attributes
			var title = button.data('title')
			var description = button.data('description')

			// Update the modal's content.
			var modal = $(this)
			modal.find('#title').text(title)
			modal.find('#description').html(description)

			// Approve forum link
			var approveForumLink = "<?php echo base_url('forums/approve_forum/'); ?>" + id;
			$('#approveForumLink').attr('href', approveForumLink);

			$('#approveForumLink').on('click', function(e) {
				e.preventDefault();
				Swal.fire({
					title: 'Confirm Approval?',
					text: "Appove forum '" + title + "'?",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, approve it!'
				}).then((result) => {
					if (result.isConfirmed) {
						// Redirect to approve forum link
						// Make Ajax Request
						$.ajax({
							url: approveForumLink,
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


			// Reject forum link
			var rejectForumLink = "<?php echo base_url('forums/reject_forum/'); ?>" + id;
			$('#rejectForumLink').attr('href', rejectForumLink);

			// On Click Reject
			$('#rejectForumLink').on('click', function(e) {
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
							url: rejectForumLink,
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