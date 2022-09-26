
<?php
date_default_timezone_set('Africa/Kampala');
	
require_once("includes/header.php");
require_once("includes/navtop.php");
require_once("includes/sidenav.php"); 

//db connection
?>
  <!-- ============================================================== -->
            <!-- Start right Content here -->
 <!-- ============================================================== -->
<div class="main-content" >
    
   <span class="base_url" style="display:none;"><?php echo base_url()?></span>


  <div class="page-content">
      <div class="container-fluid">

           <?php 

                require_once('includes/breadcrumb.php');
                require_once('includes/messages.php');
     
               $this->load->view($module."/".$view); 

          ?>

      </div>
      <!-- container-fluid -->
  </div>
  <!-- End Page-content -->
  
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © Minia.
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Design & Develop by <a href="#!" class="text-decoration-underline">Themesbrand</a>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->


<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>


<?php
      require_once("includes/footer.php");
?>

<!-- Session timeout init js -->
<script src="<?php echo base_url()?>assets/js/pages/session-timeout.init.js"></script>
<script src="<?php echo base_url()?>assets/js/back-button-hook.js"></script>


<?php
    require_once("includes/common_js.php");
    
    if($module == 'currencypairprices' && (!isset($cross_currencies) || $cross_currencies == false)){
        require_once("includes/slick_js.php");
    }elseif ($module == 'currencypairprices' && (isset($cross_currencies) && $cross_currencies == true)){
        require_once("includes/slick_cross_js.php");
    }elseif ($module == 'settings'){
        require_once("includes/settings_js.php");
    }elseif ($module == 'reports'){
        require_once("includes/reports_js.php");
    }else;
?>