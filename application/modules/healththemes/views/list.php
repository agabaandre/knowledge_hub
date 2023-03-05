<div class="row">

	<div class="card col-lg-12">
	<div class="card-header text-left">
		<h3 class="card-title float-left"><?php echo $title; ?></h3>
		<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add Health Security Theme</a>
	 </div>

	 <?php include 'includes/create-modal.php'; ?>
	 <?php include 'includes/update-modal.php'; ?>

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
				<td><a href="#edit<?php echo $theme->id; ?>" data-id="<?php echo $theme->id; ?>"
							data-icon="<?php echo $theme->icon; ?>"
							data-description="<?php echo $theme->description; ?>" data-toggle="modal" class="text-success"
							data-target="#edit-healththeme-modal"
						><i class="fa fa-edit"></i> Edit</td>
				<td><a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $theme->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</a></td>
			 </tr>
		<?php endforeach; ?>
		</table>

		<?php include 'includes/delete-modal.php'; ?>

		</div>
	</div>

</div>

<script>
	$(document).ready(function () {
		$('#edit-healththeme-modal').on('show.bs.modal', function (e) {
			var id = $(e.relatedTarget).data('id');
			var description = $(e.relatedTarget).data('description');
			var icon = $(e.relatedTarget).data('icon');

			$(e.currentTarget).find('input[name="id"]').val(id);
			$(e.currentTarget).find('input[name="description"]').val(description);
			$(e.currentTarget).find('input[name="icon"]').val(icon);
			
		})
	});
</script>