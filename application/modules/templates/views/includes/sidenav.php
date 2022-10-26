  <!-- [ navigation menu ] start -->
  <nav class="pcoded-navbar menupos-fixed menu-light">
      <div class="navbar-wrapper">
          <div class="navbar-brand header-logo">
              <a href="<?php echo base_url() ?>dashboard" class="b-brand" style="filter: brightness(0) invert(1);">
                  <img src=" <?php echo base_url() ?>assets/images/cdc_square.png" alt="" class="logo images">
                  <img src="<?php echo base_url() ?>assets/images/cdc_square.png" alt="" class="logo-thumb images">
              </a>
              <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
          </div>
          <div class="navbar-content scroll-div">
              <ul class="nav pcoded-inner-navbar">
                  <li class="nav-item pcoded-menu-caption">
                      <label>Navigation</label>
                  </li>
                  <li class="nav-item active">
                      <a href="<?php echo base_url() ?>dashboard" class="nav-link"><span class="pcoded-micon"><i class="fas fa-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                  </li>
                  <li class="nav-item pcoded-menu-caption">
                      <label>Publish</label>
                  </li>
                  <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu">
                      <a href="#" class="nav-link"><span class="pcoded-micon"><i class="fas fa-book"></i></span><span class="pcoded-mtext">Publications</span></a>
                      <ul class="pcoded-submenu">
                          <li class=""><a href="<?php echo base_url(); ?>publications/create" class="">Create Publication</a></li>
                          <li class=""><a href="<?php echo base_url(); ?>publications">Manage Publications</a></li>

                      </ul>
                  </li>

                  <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu">
                      <a href="#" class="nav-link"><span class="pcoded-micon"><i class="fa-solid fa-chart-line"></i></span><span class="pcoded-mtext"></span>Performance Indicators</a>
                      <ul class="pcoded-submenu">
                          <li class=""><a href="<?php echo base_url(); ?>kpi/create" class="">Create and Manage KPIs</a></li>
                          <li class=""><a href="<?php echo base_url(); ?>kpi/data" class="">KPI Data</a></li>
                      </ul>

                  </li>


                  <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu">
                      <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-list"></i></span><span class="pcoded-mtext">Form Lists</span></a>
                      <ul class="pcoded-submenu">
                          <li><a href="<?php echo base_url() ?>filetypes">File Types</a></li>
                          <li><a href=" <?php echo base_url() ?>authors">Authors</a></li>
                          <li><a href="<?php echo base_url() ?>healththemes">Security Themes</a></li>
                          <li><a href="<?php echo base_url() ?>subthemes">Security Sub-Themes</a></li>
                          <li><a href=" <?php echo base_url() ?>geoareas">Geographical Coverage</a></li>
                          <li><a href="<?php echo base_url() ?>slides">Home Slides</a></li>


                      </ul>
                  </li>


                  <li class=" nav-item pcoded-menu-caption">
                      <label>Settings</label>
                  </li>
                  <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu">
                      <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="fas fa-users"></i></span><span class="pcoded-mtext">Settings</span></a>
                      <ul class="pcoded-submenu">
                          <li class=""><a href="<?php echo base_url() ?>auth/users" class="">Manage Users</a></li>
                          <li class=""><a href="<?php echo base_url() ?>permissions" class="">Groups and Permissions</a></li>
                          <li class=""><a href="" class="">User Access Logs</a></li>
                          <li class=""><a href="" class="">Constants and Variables</a></li>

                      </ul>
                  </li>


          </div>
      </div>
  </nav>
  <!-- [ navigation menu ] end -->