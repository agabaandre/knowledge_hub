<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
			<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add File Type</a>
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
				<?php foreach ($filetypes as $row) : ?>
					<tr>
						<td width="5%"><i class="fa fa-map-pin text-muted"></i></td>
						<td><?php echo $row->name; ?></td>
						<td><a href="#edit<?php echo $row->id; ?>" data-id="<?php echo $row->id; ?>"
							data-name="<?php echo $row->name; ?>" data-toggle="modal" class="text-success"
							data-target="#edit-filetype-modal"
						><i class="fa fa-edit"></i> Edit</td>
						<td><a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $row->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php include 'includes/delete-modal.php'; ?>

		</div>
	</div>

</div>

<script>
	$(document).ready(function() {
		$('#edit-filetype-modal').on('show.bs.modal', function(e) {
			var id = $(e.relatedTarget).data('id');
			var name = $(e.relatedTarget).data('name');
			$(e.currentTarget).find('input[name="id"]').val(id);
			$(e.currentTarget).find('input[name="name"]').val(name);
		});
	});
</script>