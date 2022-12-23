<div class="row">
	<div class="card col-lg-12">

		<?php include 'includes/create-modal.php'; ?>
		<?php include 'includes/delete-modal.php'; ?>
		<?php include 'includes/update-modal.php'; ?>

		<!-- Card Header -->
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
			<!-- Export Data onClick-->
			<button type="button" id="exportAuthors" class="btn btn-success btn-sm float-right">Export Data</button>
			<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success btn-sm mx-3 float-right"><i class="fa fa-use-add"></i> Add Author</a>
		</div>

		<!-- Card Body -->
		<div class="card-body">
			<table class="table table-bordered table-striped table-hover" id="authors-table">
				<thead>
					<tr>
						<th>ID</th>
						<th>Author Name</th>
						<th>Telephone</th>
						<th>Email</th>
						<th>Address</th>
						<th>Is Organization</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($authors as $author) : ?>
						<tr>
							<td><?php echo $author->id; ?></td>
							<td><?php echo $author->name; ?></td>
							<td><?php echo $author->telephone; ?></td>
							<td><?php echo $author->email; ?></td>
							<td><?php echo $author->address; ?></td>
							<td><?php echo $author->is_organsiation ? 'Yes' : 'No'; ?></td>
							<td>
								<a href="#edit-modal" data-toggle="modal" class="btn btn-outline-primary btn-sm edit-author" data-target="#edit-author-modal" data-id="<?php echo $author->id; ?>" data-name="<?php echo $author->name; ?>" data-address="<?php echo $author->address; ?>" data-phone="<?php echo $author->telephone; ?>"><i class="fa fa-edit"></i> Edit</a>
								<a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $author->id; ?>)" class="btn btn-outline-danger btn-sm delete-author" data-id="<?php echo $author->id; ?>"><i class="fa fa-trash"></i> Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>


	</div>
</div>
