<div class="row">

		<?php foreach ($authors as $author): ?>
		     <div class="card col-lg-12">
		     <a href="<?php echo base_url();?>site/author_pubs/<?php echo $author->id ?>">
	          <div class="card-body text-left">
	          	<div class="row">
	          	  <div class="col-md-1">
	          	  	  <img src="<?php echo base_url();?>assets/images/icons/author.png" width="40px">
	          	  </div>
	          	 <div class="col-md-11">
					<h4><?php echo $author->name; ?></h4>
					<p><?php echo $author->address; ?></p>
					<p><?php echo $author->telephone; ?></p>
					<p><?php echo $author->email; ?></p>
			    </div>
			 	</div>
			   </div>
			  </a>
			</div>
		<?php endforeach; ?>
	
</div>