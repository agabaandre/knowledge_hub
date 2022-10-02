<div class="row p-5">

	<?php foreach ($results['themes'] as $row): ?>
	     <div class="card col-lg-3">
          <div class="card-body text-left">
          	<div class="row">
          	  	 <h5>
          	  	 	<i class="fa <?php echo $row->icon;?>"></i> 
				   <?php echo truncate($row->description,410); ?>
				</h5>
		 	</div>
		   </div>
		</div>
	<?php endforeach; ?>
	
</div>