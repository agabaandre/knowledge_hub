<div class="row">

	<?php foreach ($results['publications'] as $row): ?>
	     <div class="card col-lg-12">
          <div class="card-body text-left">
          	<div class="row">
          	  <div class="col-md-1">
          	  	  <img src="<?php echo base_url();?>assets/images/icons/author.png" width="80px">
          	  </div>
          	 <div class="col-md-11">
				<p><?php echo truncate($row->description,410); ?></p>
				<a href="<?php echo $row->publication; ?>" target="_blank"><small><?php echo $row->publication; ?></small></a>
		    </div>
		 	</div>
		   </div>
		</div>
	<?php endforeach; ?>
	
</div>