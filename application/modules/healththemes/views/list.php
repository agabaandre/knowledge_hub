<div class="row">

	<div class="card col-lg-12">
	<div class="card-header text-left">
		<h3 class="card-title"><?php echo $title; ?></h3>
	 </div>
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
				<td><a href="edit#<?php echo $theme->id; ?>"><i class="fa fa-edit"></i> Edit</td>
				<td><a href="del#<?php echo $theme->id; ?>" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
			 </tr>
		<?php endforeach; ?>
		</table>

		</div>
	</div>

</div>