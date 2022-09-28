<?php
date_default_timezone_set('Africa/Kampala');

require_once("includes/header.php");
require_once("includes/frontend/preloader.php");
//require_once("includes/frontend/top_nav.php");
//require_once("includes/frontend/top_bar.php");

?>
<style type="text/css">
    
    .header-bg{
        background-color: rgba(145, 28, 57,0.8); 
        min-height: 220px; 
        background-image: url(<?php echo base_url(); ?>assets/images/map.png); 
        background-position: right; 
        background-repeat: no-repeat
    }
</style>

<div class="row p-4 pt-2 align-items-center header-bg">
    <div class="col-md-1 col-lg-1 col-sm-12 text-center">
        <img src="<?php echo base_url(); ?>assets/images/icon_Africa_cdc.png" width="100px">
    </div>
    <div class="col-md-11 col-lg-11 col-sm-12 text-center">
        <h3 class="text-white">Africa CDC RCC Health Security Knowledge Hub</h3>
        <h5  style="color:#C3A366; padding-bottom: 20px;">GLOBAL AND REGIONAL POLICIES, GUIDANCE, DATABASES AND RESOURCES</h5>
        
    </div>
</div>

<div class="container p-3" style="margin-top: -50px;">
     
    <!-- [ Main Content ] start -->

   <?php $this->load->view($module . "/" . $view); ?>

    <!-- [ Main Content ] end -->
                       
</div>
    <!-- [ Main Content ] end -->

    <!-- Required Js -->
    <script src="<?php echo base_url(); ?>assets/js/vendor-all.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/pcoded.min.js"></script>
    <!-- prism Js -->
    <script src="<?php echo base_url(); ?>assets/plugins/prism/js/prism.min.js"></script>
  

    <div class="footer-fab">
        <div class="b-bg">
            <i class="fas fa-info"></i>
        </div>
        <div class="fab-hover">
            <ul class="list-unstyled">
                <li><a href="<?php echo base_url(); ?>"  data-text="FAQs" class="btn btn-icon btn-rounded btn-success m-0"><i class="feather icon-list"></i></a>
                </li>
                <li><a href="<?php echo base_url(); ?>"  
                    data-text="CDC Website" class="btn btn-icon btn-rounded btn-primary m-0">
                    <i class="feather icon feather icon-globe"></i>
                </a>
                </li>
            </ul>
        </div>
    </div>

    <script src="<?php echo base_url(); ?>assets/js/analytics.js"></script>
</body>

</html>
