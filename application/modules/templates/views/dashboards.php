<?php
date_default_timezone_set('Africa/Kampala');
require_once("includes/header.php");
?>


<style media="screen">
    .sidenav-horizontal:before,
    .sidenav-horizontal:after {
        content: "";
        background: #242e3e;
        width: 100%;
        position: absolute;
        top: 0;
        height: 100%;
        z-index: 5;
    }

    .sidenav-horizontal:before {
        left: 100%;
    }

    .sidenav-horizontal:after {
        right: 100%;
    }

    @media only screen and (max-width: 991px) {

        .sidenav-horizontal:before,
        .sidenav-horizontal:after {
            display: none;
        }
    }
</style>


<!-- [ Pre-loader ] start -->
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>
<!-- [ Pre-loader ] End -->

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <span class="navbar-brand" style="background-color: transparent;">
      <img width="130px" src="<?php echo base_url(); ?>assets/images/icon_Africa_cdc.png">
  </span>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <a class="nav-item nav-link active" href="#">HOME</a>
      <a class="nav-item nav-link" href="#">Population</a>
      <a class="nav-item nav-link" href="#">Population</a>
    </div>
  </div>
</nav>
<!-- [ Main Content ] end -->
<section style="padding: 20px 50px;">
<div class="card">
  <div class="card-body">

    <?php
      $this->load->view($module . "/" . $view);
    ?>

  </div>
</div>
</section>

<!-- Required Js -->
<script src="<?php echo base_url(); ?>assets/js/vendor-all.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pcoded.min.js"></script>
<!-- prism Js -->
<script src="<?php echo base_url(); ?>assets/plugins/prism/js/prism.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/horizontal-menu.js"></script>
<script type="text/javascript">
    (function() {
        if ($('#layout-sidenav').hasClass('sidenav-horizontal') || window.layoutHelpers.isSmallScreen()) {
            return;
        }
        try {
            window.layoutHelpers.setCollapsed(
                localStorage.getItem('layoutCollapsed') === 'true',
                false
            );
        } catch (e) {}
    })();
    $(function() {

        $('#layout-sidenav').each(function() {
            new SideNav(this, {
                orientation: $(this).hasClass('sidenav-horizontal') ? 'horizontal' : 'vertical'
            });
        });


        $('body').on('click', '.layout-sidenav-toggle', function(e) {
            e.preventDefault();
            window.layoutHelpers.toggleCollapsed();
            if (!window.layoutHelpers.isSmallScreen()) {
                try {
                    localStorage.setItem('layoutCollapsed', String(window.layoutHelpers.isCollapsed()));
                } catch (e) {}
            }
        });
    });
    $(document).ready(function() {
        $("#pcoded").pcodedmenu({
            themelayout: 'horizontal',
            MenuTrigger: 'hover',
            SubMenuTrigger: 'hover',
        });
    });
</script>

<div class="footer-fab">
    <div class="b-bg">
        <i class="fas fa-question"></i>
    </div>
    <div class="fab-hover">
        <ul class="list-unstyled">
            <li><a href="<?php echo base_url(); ?>doc/index-bc-package.html" data-text="UI Kit" class="btn btn-icon btn-rounded btn-info m-0"><i class="feather icon-layers"></i></a></li>
            <li><a href="<?php echo base_url(); ?>doc/index.html" data-text="Document" class="btn btn-icon btn-rounded btn-primary m-0"><i class="feather icon feather icon-book"></i></a></li>
        </ul>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/analytics.js"></script>

</body>

</html>