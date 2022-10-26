<div class="row">

	<div class="card col-lg-12">
		<div class="card-header text-left">
			<h3 class="card-title float-left"><?php echo $title; ?></h3>
		</div>


		<div class="card-body text-left">
			<table class="table table-striped table table-bordered">
				<thead>
					<tr>

						<th>#</th>
						<th>Title</th>
						<th>Description</th>




						<th>Edit/Delete</th>
					</tr>
				</thead>
				<?php

				$i = 1;
				foreach ($publications as $row) :
					//print_r($row);
				?>
					<tr>
						<td width="5%"><?php echo $i++; ?> <i class="fa <?php $row->file_type->icon; ?> fa-2x text-muted"></i></td>
						<td>
							<?php echo truncate($row->title, 50); ?>
							<p><a href="<?php echo $row->publication; ?>" target="_blank">View Publication</a>
							<p>
						</td>

						<td>
							<?php echo truncate($row->description, 20); ?>
						</td>


						<td><a href="#edit<?php echo $row->id; ?>"><i class="fa fa-edit"></i> Edit
								<a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $row->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<?php include 'includes/delete-modal.php'; ?>

		</div>
	</div>

</div>