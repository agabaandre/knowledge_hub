

    <!-- [ navigation menu ] start -->
    <nav class="pcoded-navbar theme-horizontal navbar-blue">
        <div class="navbar-wrapper container">
            <div class="navbar-brand header-logo">
                <a href="index.html" class="b-brand">
                    <!-- <div class="b-bg">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="b-title">Flash Able</span> -->
                    <img src="<?php echo base_url(); ?>assets/images/logo-dark.svg" alt="" class="logo images">
                    <img src="<?php echo base_url(); ?>assets/images/logo-icon-dark.svg" alt="" class="logo-thumb images">
                </a>
                <a class="mobile-menu" id="mobile-collapse" href="#!"><span></span></a>
            </div>
            <div class="navbar-content sidenav-horizontal" id="layout-sidenav">
                <ul class="nav pcoded-inner-navbar sidenav-inner">
                    <li class="nav-item pcoded-menu-caption">
                        <label>Navigation</label>
                    </li>
                    <li data-username="dashboard" class="nav-item">
                        <a href="index.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                    </li>
                    <li class="nav-item pcoded-menu-caption">
                        <label>Other</label>
                    </li>
                    <li data-username="Menu levels Menu level 2.1 Menu level 2.2" class="nav-item pcoded-hasmenu">
                        <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-menu"></i></span><span class="pcoded-mtext">Menu levels</span></a>
                        <ul class="pcoded-submenu">
                            <li class=""><a href="" class="">Menu Level 2.1</a></li>
                            <li class="pcoded-hasmenu">
                                <a href="#!" class="">Menu level 2.2</a>
                                <ul class="pcoded-submenu">
                                    <li class=""><a href="" class="">Menu level 3.1</a></li>
                                    <li class=""><a href="" class="">Menu level 3.2</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li data-username="Disabled Menu" class="nav-item disabled"><a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-power"></i></span><span class="pcoded-mtext">Disabled menu</span></a></li>
                    <li data-username="Sample Page" class="nav-item"><a href="sample-page.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-sidebar"></i></span><span class="pcoded-mtext">Sample page</span></a></li>
                    <li class="nav-item pcoded-menu-caption">
                        <label>Support</label>
                    </li>
                    <li data-username="Documentation" class="nav-item"><a href="<?php echo base_url(); ?>doc/index.html" class="nav-link" ><span class="pcoded-micon"><i class="feather icon-book"></i></span><span class="pcoded-mtext">Documentation</span></a></li>
                    <li data-username="Need Support" class="nav-item"><a href="#" class="nav-link" ><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">Need
                                support ?</span></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ navigation menu ] end -->