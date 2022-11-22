<div class="row">

	<div class="card col-lg-12">
	<div class="card-header text-left">
		<h3 class="card-title float-left"><?php echo $title; ?></h3>
		<a href="#create-modal" data-toggle="modal" class="btn btn-outline-success float-right"><i class="fa fa-use-add"></i> Add Slider</a>
	 </div>

	 <?php include 'includes/create-modal.php'; ?>

	 <div class="card-body text-left">
	 	<table class="table table-striped">
	 		<thead>
	 			<tr>
	 				<th></th>
	 				<th>Caption</th>
	 				<th colspan="2"></th>
	 			</tr>
	 		</thead>
		<?php foreach ($slides as $row): ?>
		     <tr>
				<td width="5%">
					<img src="<?php echo base_url(); ?>assets/images/slider/<?php echo $row->slide_image; ?>" width="80px">
				</td>
				<td width="60%"><?php echo $row->caption; ?></td>
				<td><a href="#edit<?php echo $row->id; ?>"><i class="fa fa-edit"></i> Edit</td>
				<td><a href="javascript:void(0);" onclick="openDeleteModal(<?php echo $row->id; ?>)" class="text-danger"><i class="fa fa-trash"></i> Delete</td>
			 </tr>
		<?php endforeach; ?>
		</table>

		<?php include 'includes/delete-modal.php'; ?>

		</div>
	</div>

</div>