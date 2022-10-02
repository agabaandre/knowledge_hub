
<div class="row justify-content-center">
   
    <?php 
      foreach($categories as $cat):
      	$row = (Object) $cat;
     ?>

          <div class="card col-lg-4 col-xl-3 col-md-6 col-sm-6 col-xs-12 m-1 justify-content-center">
          	<a href="<?php echo base_url(); ?>browse/<?php echo $row->link; ?>">
              <div class="card-body category">
                  <div class="block">
                  	<!-- <i class="feather icon feather <?php echo $row->icon; ?> fa-3x text-cdc-gold"></i> -->
                  	<img src="<?php echo base_url();?>assets/images/icons/<?php echo $row->image; ?>" width="70px">
                  </div>
                  <div class="block">
                     <h5><?php echo $row->title; ?></h5>
                  </div>
              </div>
            </a>
          </div>
      
  <?php endforeach; ?>
   
</div>