
<div class="row justify-content-center">
   
    <?php 
      foreach($subthemes as $theme):
        $row = (Object) $theme;
     ?>

          <div class="card col-lg-3 col-xl-3 col-md-6 col-sm-6 col-xs-12 m-1 justify-content-center active-card">
            <a href="<?php echo base_url(); ?>records/publications/<?php echo $row->id; ?>">
              <div class="card-body category">
                  <div class="d-block text-center">
                    <i class="fas <?php echo $row->icon; ?> fa-3x text-success"></i>
                  </div>
                  <div class="d-block text-center">
                     <h5><?php echo $row->description; ?></h5>
                  </div>
              </div>
            </a>
          </div>
      
  <?php endforeach; ?>
   
</div>