<div class="row justify-content-center">

  <div class="col-lg-7">

    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
      <div class="carousel-inner">

        <?php
        $i = 0;
        foreach ($slides as $slide) : ?>
          <div class="carousel-item <?php echo ($i == 0) ? 'active' : ''; ?>">
            <img class="d-block w-100" src="<?php echo base_url(); ?>assets/images/slider/<?php echo $slide->slide_image; ?>">

            <?php if ($slide->caption) : ?>
              <div class="carousel-caption d-none d-md-block">
                <h5 class="text-white"><?php echo $slide->caption; ?></h5>
              </div>
            <?php endif; ?>

          </div>

        <?php
          $i++;
        endforeach; ?>

      </div>

      <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>

    </div>


  </div>

  <div class="col-lg-5">

    <div class="card col-lg-12">
      <a href="<?php echo base_url(); ?>rcc/dashboards">
        <div class="card-body">
          <img src="<?php echo base_url(); ?>assets/images/icons/dashboards.png" width="50px">
          <h4 class="text-muted" style="font-size:17px;">
            Regional Collaborating Centres
            Dashboards
          </h4>
        </div>
      </a>
    </div>

    <div class="card col-lg-12">
      <a href="<?php echo base_url(); ?>records/index">
        <div class="card-body">
          <img src="<?php echo base_url(); ?>assets/images/icons/seo-report.png" width="50px">
          <h4 class="text-muted" style="font-size:17px;">
            Resources
          </h4>
        </div>
      </a>
    </div>


  </div>

</div>