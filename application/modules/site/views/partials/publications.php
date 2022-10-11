<div class="row p-2">

	<?php foreach ($publications as $row): ?>
	     <div class="card col-lg-12 single-border">
          <div class="card-body text-left">
          	<div class="row">
          	  <div class="col-md-1">
          	  	  <img src="<?php echo base_url();?>assets/images/icons/author.png" width="80px">
          	  </div>
          	 <div class="col-md-11">
				<h5><?php echo truncate($row->title,500); ?></h5>
				<p class="text-primary p-0"><?php echo $row->description; ?></p>
				<p class="text-primary  p-0">Sub Theme: <?php echo $row->sub_theme->description; ?></p>
				<a href="<?php echo $row->publication; ?>" target="_blank"><small>
					<?php echo $row->publication; ?></small></a>
		    </div>
		 	</div>
		   </div>
		</div>
	<?php endforeach; ?>

	<?php echo $links; ?>
	
</div>