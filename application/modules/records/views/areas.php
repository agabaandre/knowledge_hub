
<div class="row justify-content-center">
   
    <?php 
      foreach($areas as $area):
        $row = (Object) $area;
     ?>

          <div class="card col-lg-2 col-xl-2 col-md-6 col-sm-6 col-xs-6 m-1 justify-content-center active-card">
            
            <a href="<?php echo base_url(); ?>records/areas_pubs/<?php echo $row->id; ?>">
              <div class="card-body category">
                  <div class="block">
                    <i class="fas fa-map-pin fa-2x text-cdc-gold"></i>
                  </div>
                  <div class="block">
                     <h6 class="text-center"><?php echo $row->name; ?></h6>
                  </div>
              </div>
            </a>

          </div>
      
  <?php endforeach; ?>
   
</div>