<div class="row">

	<?php foreach ($results['themes'] as $row): ?>
		<div class="col-lg-2 col-xl-2 col-md-6 col-sm-6 col-xs-12 p-1 m-0">
	     <div class="card justify-content-center ">
          <div class="card-body category">
          	<div class="row">
          	  	 <h5>
          	  	 	<i class="fa <?php echo $row->icon;?>"></i> 
				   <?php echo truncate($row->description,410); ?>
				</h5>
		 	</div>
		   </div>
		</div>
	</div>
	<?php endforeach; ?>
	
</div>