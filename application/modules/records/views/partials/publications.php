
	<?php 
	   $i=0;
	   foreach ($publications as $row):
		$i++;
	 ?>
	     <div class="card col-lg-12 single-border mb-1">
          <div class="card-body text-left">
          	<div class="row">
          	  <div class="col-md-2">
				
				<a href="<?php echo base_url(); ?>records/show/<?php echo $row->id; ?>">
						<?php if(is_valid_image($row->cover)): ?>
						<img src="<?php echo base_url();?>uploads/publications/<?php echo $row->cover; ?>" width="80px">
						<?php else: ?>
							<img src="<?php echo base_url();?>uploads/publications/cover.png" width="80px">
						<?php endif; ?>
				</a>
          	  </div>
          	 <div class="col-md-10">
				<h5>
					<a href="<?php echo base_url(); ?>records/show/<?php echo $row->id; ?>">
					<?php echo truncate($row->title,500); ?></a>
			    </h5>
				<p class="text-nothern p-0">
				<a href="<?php echo base_url(); ?>records/show/<?php echo $row->id; ?>">
					<?php echo nl2br(truncate($row->description,100)); ?>
				</a>
				</p>
				<a href="<?php echo $row->publication; ?>" target="_blank"><small><?php echo $row->publication; ?></small></a>
		

				<span class="muted medium ml-2 theme-cl"><br>
				<i class="lni lni-briefcase mr-1"></i>Theme: <?php echo $row->theme->description; ?></span>
					<span class="muted medium ml-2 theme-cl"><br>
				<i class="lni lni-archive mr-1"></i>Sub Theme: <?php echo $row->sub_theme->description; ?></span>
				
				<span class="muted medium ml-2 text-muted mt-1 "><br>
				<i class="lni lni-empty-file mr-1"></i>Type: <?php echo $row->file_type->name; ?></span>
				
				<span class="text-muted medium d-block mt-1">
					<span class=" mr-2"><i class="lni lni-calendar mr-1"></i>Last updated: <?php echo time_ago($row->updated_at); ?> </span>
					<a href="<?php echo base_url(); ?>records/show/<?php echo $row->id; ?>">
					<span class=" mr-2"><i class="fa fa-eye mr-1"></i><?php echo $row->visits; ?> Views </span>
					<span class=" mr-1 ml-2 comments<?php echo $i; ?>" 
					data-bs-toggle="popover"
					data-bs-placement="bottom"><i class="fa fa-comments"></i> <?php echo count($row->comments); ?> Comments</span>
					</a>
					<?php include 'comments.php'; ?>
					<?php include 'favourites_btn.php'; ?>
				</span>

			</div>
		 	</div>
		   </div>
		</div>
	<?php endforeach; ?>

	<?php echo $links; ?>

	<?php if(count($publications)== 0): ?>

		<div class="row justify-content-center py-5">
			<i class="fa fa-info-circle fa-2x text-muted"></i>
			
			<h4 class="text-muted">No matching records found</h4>
		</div>

	<?php endif; ?>
	