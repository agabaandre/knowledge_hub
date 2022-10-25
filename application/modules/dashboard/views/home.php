<div class="row">
  <div class="col-xl-12">
    <div class="row">
      <div class="col-md-4">
        <div class="card statustic-card">
          <div class="card-header borderless pb-0">
            <h5>Publications</h5>
          </div>
          <div class="card-body text-center">
            <span class="d-block text-c-blue f-36"><?php echo $tabs['publications']; ?></span>
            <p class="m-b-0">Total</p>
            <div class="progress">
              <div class="progress-bar bg-c-blue" style="width:100%"></div>
            </div>
          </div>
          <div class="card-footer border-0">
            <h6 class="text-muted m-b-0">Inactive: <?php echo $tabs['publications'] - $tabs['active_pubs']; ?></h6>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card statustic-card">
          <div class="card-header borderless pb-0">
            <h5>Authors</h5>
          </div>
          <div class="card-body text-center">
            <span class="d-block text-c-green f-36"><?php echo $tabs['authors']; ?></span>
            <p class="m-b-0">Total</p>
            <div class="progress">
              <div class="progress-bar bg-c-green" style="width:100%"></div>
            </div>
          </div>
          <div class="card-footer border-0">

            <h6 class="text-muted m-b-0">Organisation Authors: <?php echo $tabs['org_authors']; ?></h6>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card statustic-card">
          <div class="card-header borderless pb-0">
            <h5>Peformance Indicators</h5>
          </div>
          <div class="card-body text-center">
            <span class="d-block text-c-purple f-36"><?php echo $tabs['kpis']; ?></span>
            <p class="m-b-0">Total</p>
            <div class="progress bg-c-red">
              <div style="width:40%; "></div>
            </div>
          </div>
          <div class="card-footer  border-0">
            <h6 class="text-muted m-b-0">Subject Areas: <?php echo $tabs['subject_areas']; ?></h6>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php //print_r($tabs); 
  ?>

  <div class="col-xl-12">
    <div class="card product-progress-card">
      <div class="card-body">
        <div class="row pp-main">
          <div class="col-xl-3 col-md-6">
            <div class="pp-cont">
              <div class="row align-items-center m-b-20">
                <div class="col-auto">

                  <i class="fa fa-tag f-24 text-mute"></i>
                </div>
                <div class="col text-right">
                  <h2 class="m-b-0 text-c-blue"><?php echo  $tabs['thematic_areas']; ?></h2>
                </div>
              </div>
              <div class="row align-items-center m-b-15">
                <div class="col-auto">
                  <p class="m-b-0">Data Themes</p>
                </div>
                <div class="col text-right">

                </div>
              </div>
              <div class="progress">
                <div class="progress-bar progress-c-blue" style="width:100%"></div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="pp-cont">
              <div class="row align-items-center m-b-20">
                <div class="col-auto">
                  <i class="fas fa-tag f-24 text-mute"></i>
                </div>
                <div class="col text-right">
                  <h2 class="m-b-0 text-c-red"><?php echo $tabs['subthemes']; ?></h2>
                </div>
              </div>
              <div class="row align-items-center m-b-15">
                <div class="col-auto">
                  <p class="m-b-0">Data Sub Themes</p>
                </div>
                <div class="col text-right">

                </div>
              </div>
              <div class="progress">
                <div class="progress-bar progress-c-red" style="width:100%"></div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="pp-cont">
              <div class="row align-items-center m-b-20">
                <div class="col-auto">
                  <i class="fa-solid fa-earth-africa f-24 text-mute"></i>
                </div>
                <div class="col text-right">
                  <h2 class="m-b-0 text-c-yellow"><?php echo $tabs['publication_areas']; ?></h2>
                </div>
              </div>
              <div class="row align-items-center m-b-15">
                <div class="col-auto">
                  <p class="m-b-0">Data Geographical Coverage</p>
                </div>
                <div class="col text-right">

                </div>
              </div>
              <div class="progress">
                <div class="progress-bar progress-c-yellow" style="width:100%"></div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="pp-cont">
              <div class="row align-items-center m-b-20">
                <div class="col-auto">
                  <i class="fa -solid fa-flag fa-24 text-muted "></i>
                </div>
                <div class="col text-right">
                  <h2 class="m-b-0 text-c-green"><?php echo $tabs['countries']; ?></h2>
                </div>
              </div>
              <div class="row align-items-center m-b-15">
                <div class="col-auto">
                  <p class="m-b-0">Countries</p>
                </div>
                <div class="col text-right">
                  <p class="m-b-0 text-c-green"><?php echo $tabs['rccs']; ?> RCCS</p>
                </div>
              </div>
              <div class="progress">
                <div class="progress-bar progress-c-green" style="width:100%"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>