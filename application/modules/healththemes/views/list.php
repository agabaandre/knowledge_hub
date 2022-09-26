<div class="row">

	<?php foreach ($healththemes as $theme): ?>
	<div class="col-md-4">
		<div class="card">
	     <div class="card-body text-center">
			<h4><i class="fa <?php echo $theme->icon; ?> fa-2x text-muted"></i></h4>
			<p><?php echo $theme->description; ?></p>
		 </div>
		</div>
	</div>
	<?php endforeach; ?>

</div>