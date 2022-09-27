<div class="row">

	<div class="card col-lg-12">
	<div class="card-header text-left">
		<h3 class="card-title float-left"><?php echo $title; ?></h3>
		<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Health Security Theme</a>
	 </div>

	 <?php include 'includes/create-modal.php'; ?>

	 <div class="card-body text-left">
	 	<table class="table table-striped">
	 		<thead>
	 			<tr>
	 				<th colspan="2">Description</th>
	 				<th colspan="2"></th>
	 			</tr>
	 		</thead>
		<?php foreach ($healththemes as $theme): ?>
		     <tr>
				<td width="5%"><i class="fa <?php echo $theme->icon; ?> fa-2x text-muted"></i></td>
				<td><?php echo $theme->description; ?></td>
				<td><a href="#edit<?php echo $theme->id; ?>"><i class="fa fa-edit"></i> Edit</td>
				<td><a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $theme->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
			 </tr>
		<?php endforeach; ?>
		</table>

		<?php include 'includes/delete-modal.php'; ?>

		</div>
	</div>

</div>