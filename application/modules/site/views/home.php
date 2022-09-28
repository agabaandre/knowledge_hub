<style type="text/css">
	.category{
		display:flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}
	.card:hover{
		box-shadow:1px 2px 6px 2px #C3A366;
		transition: 0.3s;
	}

	.block{
		display: block;
		padding-bottom: 8px
	}
	.text-cdc-green{
		color: #119A48;
	}
	.text-cdc-gold{
		color:#C3A366;
	}
</style>

<div class="row justify-content-center">
   
    <?php 
      foreach($categories as $cat):
      	$row = (Object) $cat;
     ?>

          <div class="card col-lg-3 col-xl-3 col-md-6 col-sm-6 col-xs-12 m-1 justify-content-center">
          	<a href="<?php echo base_url(); ?>browse/<?php echo $row->link; ?>">
              <div class="card-body category">
                  <div class="block">
                  	<i class="feather icon feather <?php echo $row->icon; ?> fa-3x text-cdc-gold"></i>
                  </div>
                  <div class="block">
                     <h5><?php echo $row->title; ?></h5>
                  </div>
              </div>
            </a>
          </div>
      
  <?php endforeach; ?>
   
</div>