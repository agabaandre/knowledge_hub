<div class="row">

		<?php foreach ($authors as $author): ?>
		     <div class="card col-lg-12">
	          <div class="card-body text-left">
				<h4 width="5%">
				<i class="fa fa-user text-muted"></i> <?php echo $author->name; ?></h4>
				<p><?php echo $author->address; ?></p>
				<p><?php echo $author->telephone; ?></p>
				<p><?php echo $author->email; ?></p>
			 	</div>
			</div>
		<?php endforeach; ?>
	
</div>