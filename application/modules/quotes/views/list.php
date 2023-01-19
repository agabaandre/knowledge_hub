<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
			 <hr>
			<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-plus"></i> Add New Quote</a>
		</div>

		<?php include 'includes/create-modal.php'; ?>

		<div class="card-body text-left">
			<table class="table table-striped">
				<thead>
					<tr>
						<th colspan="2">Quote</th>
						<th colspan="2"></th>
					</tr>
				</thead>
				<?php foreach ($quotes as $row) : ?>
					<tr>
						<td width="5%"><i class="fa fa-map-pin text-muted"></i></td>
						<td><?php echo $row->quote; ?></td>
						<!-- Edit Modal Action with data -->
						<td>
							<a href="#" data-id="<?php echo $row->id; ?>" data-quote="<?php echo $row->quote; ?>" data-toggle="modal" data-target="#edit-quote-modal" class="text-success"><i class="fa fa-edit"></i> Edit</a>
						</td>
						<td><a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $row->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</a></td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php include 'includes/delete-modal.php'; ?>
			<?php include 'includes/update-modal.php'; ?>

		</div>
	</div>

</div>
