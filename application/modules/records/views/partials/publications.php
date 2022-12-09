
	<?php foreach ($publications as $row): ?>
		 <a href="<?php echo base_url(); ?>records/show/<?php echo $row->id; ?>">
	     <div class="card col-lg-12 single-border mb-1">
          <div class="card-body text-left">
          	<div class="row">
          	  <div class="col-md-2">
          	  	<?php if(is_valid_image($row->cover)): ?>
          	  	  <img src="<?php echo base_url();?>uploads/publications/<?php echo $row->cover; ?>" width="80px">
          	  	<?php else: ?>
          	  		<img src="<?php echo base_url();?>uploads/publications/cover.png" width="80px">
          	  	<?php endif; ?>
          	  </div>
          	 <div class="col-md-10">
				<h5><?php echo truncate($row->title,500); ?></h5>
				<p class="text-nothern p-0"><?php echo $row->description; ?></p>
				<p class="text-success  p-0">Sub Theme: <?php echo $row->sub_theme->description; ?></p>
				<a href="<?php echo $row->publication; ?>" target="_blank"><small><?php echo $row->publication; ?></small></a>
				<p class="text-muted  p-0">Last updated: <?php echo time_ago($row->updated_at); ?></p>
			</div>
		 	</div>
		   </div>
		</div>
	</a>
	<?php endforeach; ?>

	<?php echo $links; ?>

	<?php if(count($publications)== 0): ?>

		<div class="row justify-content-center py-5">
			<i class="fa fa-info-circle fa-2x text-muted"></i>
			
			<h4 class="text-muted">No matching records found</h4>
		</div>

	<?php endif; ?>
	