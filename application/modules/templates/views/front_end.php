<?php
date_default_timezone_set('Africa/Kampala');

require_once("includes/header.php");
require_once("includes/frontend/preloader.php");

require_once("includes/auto-complete.php");
//require_once("includes/frontend/top_bar.php");
require_once("includes/frontend/nav.php");


?>


<div class="row p-4 pt-2 align-items-center header-bg " style=" background-image: url(<?php echo base_url(); ?>assets/images/map.png);">
    <div class="col-md-1 col-lg-1 col-sm-12 text-center">
        <a class="nav-link" href="<?php echo base_url() ?>records" style="color:#FFFFFF !important;"> <img style="filter: brightness(0) invert(1);" src="<?php echo base_url(); ?>assets/images/icon_Africa_cdc.png" width="250px"></a>
    </div>
    <div class="col-md-10 col-lg-10 col-sm-12 text-center">
        <h3 class="text-white" style="font-size:25px;">Africa CDC RCC Health Security Knowledge Hub</h3>
        <h5 style="color:#C3A366; padding-bottom: 20px;">Global and Regional Policies, Guidance, Databases and Resources</h5>

        <!-- [ Main Content ] start -->
        <?php echo form_open('records/search', 'class="row justify-content-center search-form"'); ?>
        <div class="col-sm-7">
            <div class="form-group">
                <input type="text" class="form-control autocomplete term" name="term" placeholder="Search here" value="<?php echo old('term'); ?>" style="padding:10px; font-size:18px; border-radius:7px;">
            </div>
        </div>
        <?php echo form_close(); ?>

    </div>
    <div class="col-md-1 col-lg-1 col-sm-12 text-center">
    </div>

</div>


<div class="container p-3" style="margin-top: -50px;">

    <?php
    $this->load->view($module . "/" . $view);
    ?>

    <!-- [ Main Content ] end -->

</div>
<!-- [ Main Content ] end -->

<script type="text/javascript">


   $('.autocomplete').autocomplete({
        source: "<?php echo base_url(); ?>records/autocomplete",
        minLength:3,
        select: function( event, ui ) {
            console.log(ui.item);
            $('.term').val(ui.item.label);
            $('.search-form').submit();
        }
    });

</script>

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
            <li><a href="<?php echo base_url(); ?>" data-text="FAQs" class="btn btn-icon btn-rounded btn-success m-0"><i class="feather icon-list"></i></a>
            </li>
            <li><a href="<?php echo base_url(); ?>" data-text="CDC Website" class="btn btn-icon btn-rounded btn-primary m-0">
                    <i class="feather icon feather icon-globe"></i>
                </a>
            </li>
        </ul>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/analytics.js"></script>


</body>

</html>