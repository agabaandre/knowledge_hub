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
										<button type="button" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#approveForumModal" data-forumid="<?php echo $forum->id; ?>" data-forumtitle="<?php echo $forum->forum_title; ?>" data-forumdescription="<?php echo $forum->forum_description; ?>">
											Approve
										</button>


										<!-- <a href="<?php echo base_url('forums/admin/approve_forum/' . $forum->id); ?>" class="btn btn-success btn-sm btn-block">Approve</a> -->
									<?php endif; ?>
									<a href="<?php echo base_url('forums/admin/edit_forum/' . $forum->id); ?>" class="btn btn-primary btn-sm btn-block">Edit</a>
									<a href="<?php echo base_url('forums/admin/delete_forum/' . $forum->id); ?>" class="btn btn-danger btn-sm btn-block">Delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Approve forum modal -->
	<div class="modal fade" id="approveForumModal" tabindex="-1" role="dialog" aria-labelledby="approveForumModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="approveForumModalLabel">Approve Forum</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<!-- <h6>Are you sure you want to approve this forum?</h6> -->
					<p><span id="forumTitle"></span></p>
					<!-- Forum description -->
					<p><span id="forumDescription"></span></p>
				</div>
				<div class="modal-footer">
					<!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button> -->
					<!-- Reject Button -->
					<a href="" id="rejectForumLink" class="btn btn-danger">Reject</a>

					<a href="" id="approveForumLink" class="btn btn-success">Approve</a>

				</div>
			</div>
		</div>
	</div>
</div>