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
                <li data-username="dashboard Default Ecommerce CRM Analytics Crypto Project" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-home"></i></span><span class="pcoded-mtext">Dashboard</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="index.html" class="">Analytics</a></li>
                        <li class=""><a href="dashboard-sale.html" class="">Sales</a></li>
                        <li class=""><a href="dashboard-crypto.html" class="">Crypto</a></li>
                        <li class=""><a href="dashboard-project.html" class="">Project</a></li>
                        <li class=""><a href="dashboard-help.html" class="">Helpdesk<span class="pcoded-badge label label-danger">NEW</span></a></li>
                    </ul>
                </li>
                <li data-username="Vertical Horizontal Box Layout RTL fixed static collapse menu color icon dark" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layout"></i></span><span class="pcoded-mtext">Page layouts</span></a>
                    <ul class="pcoded-submenu">
                        <li class="pcoded-hasmenu"><a href="#!" class="">Vertical</a>
                            <ul class="pcoded-submenu">
                                <li class=""><a href="layout-static.html" class="">Static</a></li>
                                <li class=""><a href="layout-fixed.html" class="">Fixed</a></li>
                                <li class=""><a href="layout-menu-fixed.html" class="">Navbar fixed</a></li>
                                <li class=""><a href="layout-mini-menu.html" class="">Collapse menu</a></li>
                                <li class=""><a href="layout-navbg.html" class="">Navbar imagebg</a></li>
                            </ul>
                        </li>
                        <li class=""><a href="layout-horizontal.html" class="">Horizontal</a></li>
                        <li class=""><a href="layout-horizontal-1.html" class="">Horizontal v1</a></li>
                        <li class=""><a href="layout-horizontal-2.html" class="">Horizontal v2</a></li>
                        <li class=""><a href="layout-box.html" class="">Box layout</a></li>
                        <li class=""><a href="layout-rtl.html" class="">RTL</a></li>
                        <li class=""><a href="layout-light.html" class="">Light layout</a></li>
                        <li class=""><a href="layout-dark.html" class="">Dark layout <span class="pcoded-badge label label-danger">Hot</span></a></li>
                        <li class=""><a href="layout-menu-icon.html" class="">Color icon</a></li>
                    </ul>
                </li>
                <li data-username="widget Statistic Data Table User card Chart" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-layers"></i></span><span class="pcoded-mtext">Widget</span><span class="pcoded-badge label label-info">100+</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="widget-statistic.html" class="">Statistic</a></li>
                        <li class=""><a href="widget-data.html" class="">Data</a></li>
                        <li class=""><a href="widget-chart.html" class="">Chart</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>UI Element</label>
                </li>
                <li data-username="basic components Button Alert Badges breadcrumb Paggination progress Tooltip popovers Carousel Cards Collapse Tabs pills Modal Grid System Typography Extra Shadows Embeds" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-box"></i></span><span class="pcoded-mtext">Basic</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="bc_alert.html" class="">Alert</a></li>
                        <li class=""><a href="bc_button.html" class="">Button</a></li>
                        <li class=""><a href="bc_badges.html" class="">Badges</a></li>
                        <li class=""><a href="bc_breadcrumb-pagination.html" class="">Breadcrumb & Paggination</a></li>
                        <li class=""><a href="bc_card.html" class="">Cards</a></li>
                        <li class=""><a href="bc_collapse.html" class="">Collapse</a></li>
                        <li class=""><a href="bc_carousel.html" class="">Carousel</a></li>
                        <li class=""><a href="bc_grid.html" class="">Grid System</a></li>
                        <li class=""><a href="bc_progress.html" class="">Progress</a></li>
                        <li class=""><a href="bc_modal.html" class="">Modal</a></li>
                        <li class=""><a href="bc_spinner.html" class="">Spinner<span class="pcoded-badge label label-danger">NEW</span></a></li>
                        <li class=""><a href="bc_tabs.html" class="">Tabs & pills</a></li>
                        <li class=""><a href="bc_typography.html" class="">Typography</a></li>
                        <li class=""><a href="bc_tooltip-popover.html" class="">Tooltip & popovers</a></li>
                        <li class=""><a href="bc_toasts.html" class="">Toasts<span class="pcoded-badge label label-danger">NEW</span></a></li>
                        <li class=""><a href="bc_extra.html" class="">Other</a></li>
                    </ul>
                </li>
                <li data-username="advance components Alert gridstack lightbox modal notification pnotify rating rangeslider slider syntax highlighter Tour Tree view Nestable Toolbar" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-gitlab"></i></span><span class="pcoded-mtext">Advance</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="ac_alert.html" class="">Sweet alert</a></li>
                        <li class=""><a href="ac-datepicker-componant.html" class="">Datepicker</a></li>
                        <li class=""><a href="ac_gridstack.html" class="">Gridstack</a></li>
                        <li class=""><a href="ac_lightbox.html" class="">Lightbox</a></li>
                        <li class=""><a href="ac_modal.html" class="">Modal</a></li>
                        <li class=""><a href="ac_notification.html" class="">Notification</a></li>
                        <li class=""><a href="ac_nestable.html" class="">Nestable</a></li>
                        <li class=""><a href="ac_pnotify.html" class="">pnotify</a></li>
                        <li class=""><a href="ac_rating.html" class="">Rating</a></li>
                        <li class=""><a href="ac_rangeslider.html" class="">Rangeslider</a></li>
                        <li class=""><a href="ac_slider.html" class="">Slider</a></li>
                        <li class=""><a href="ac_syntax_highlighter.html" class="">Syntax highlighter</a></li>
                        <li class=""><a href="ac_tour.html" class="">Tour</a></li>
                        <li class=""><a href="ac_treeview.html" class="">Tree view</a></li>
                        <li class=""><a href="ac_toolbar.html" class="">Toolbar</a></li>
                        <li class=""><a href="ac_usercard.html" class="">User card</a></li>
                        <li class=""><a href="timeline.html" class="">Timeline</a></li>
                    </ul>
                </li>
                <li data-username="extra components Session Timeout Session Idle Timeout Offline" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-package"></i></span><span class="pcoded-mtext">Extra</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="ec-session-timeout.html" class="">Session timeout</a></li>
                        <li class=""><a href="ec-session-idle-timeout.html" class="">Session idle timeout</a></li>
                        <li class=""><a href="ec-offline.html" class="">Offline</a></li>
                    </ul>
                </li>
                <li data-username="Animations" class="nav-item"><a href="animation.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-aperture"></i></span><span class="pcoded-mtext">Animations</span></a></li>
                <li data-username="icons Feather Fontawsome flag material simple line themify" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-feather"></i></span><span class="pcoded-mtext">Icons</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="icon-feather.html" class="">Feather<span class="pcoded-badge label label-danger">NEW</span></a></li>
                        <li class=""><a href="icon-fontawsome.html" class="">Font Awesome 5<span class="pcoded-badge label label-primary">1000+</span></a></li>
                        <li class=""><a href="icon-flag.html" class="">Flag</a></li>
                        <li class=""><a href="icon-material.html" class="">Material</a></li>
                        <li class=""><a href="icon-simple-line.html" class="">Simple line</a></li>
                        <li class=""><a href="icon-themify.html" class="">Themify</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Forms</label>
                </li>
                <li data-username="form elements advance componant validation masking wizard picker select" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-file-text"></i></span><span class="pcoded-mtext">Forms</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="form_elements.html" class="">Form elements</a></li>
                        <li class=""><a href="form-elements-advance.html" class="">Form advance</a></li>
                        <li class=""><a href="form-validation.html" class="">Form validation</a></li>
                        <li class=""><a href="form-masking.html" class="">Form masking</a></li>
                        <li class=""><a href="form-wizard.html" class="">Form wizard</a></li>
                        <li class=""><a href="form-picker.html" class="">Form picker</a></li>
                        <li class=""><a href="form-select.html" class="">Form select</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Table</label>
                </li>
                <li data-username="table basic sizing border styling" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-align-justify"></i></span><span class="pcoded-mtext">Bootstrap table</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="tbl_bootstrap.html" class="">Basic table</a></li>
                        <li class=""><a href="tbl_sizing.html" class="">Sizing table</a></li>
                        <li class=""><a href="tbl_border.html" class="">Border table</a></li>
                        <li class=""><a href="tbl_styling.html" class="">Styling table</a></li>
                    </ul>
                </li>
                <li data-username="table basic advance styling api ajax server plugin data sources" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-package"></i></span><span class="pcoded-mtext">Data table</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="dt_basic.html" class="">Basic initialization</a></li>
                        <li class=""><a href="dt_advance.html" class="">Advance initialization</a></li>
                        <li class=""><a href="dt_styling.html" class="">Styling</a></li>
                        <li class=""><a href="dt_api.html" class="">API</a></li>
                        <li class=""><a href="dt_ajax.html" class="">Ajax</a></li>
                        <li class=""><a href="dt_serverside.html" class="">Server side</a></li>
                        <li class=""><a href="dt_plugin.html" class="">Plug-in</a></li>
                        <li class=""><a href="dt_sources.html" class="">Data sources</a></li>
                    </ul>
                </li>
                <li data-username="table extensions autofill basic data export col reorder fixed columns header key responsive row scroller select" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-server"></i></span><span class="pcoded-mtext">DT extensions</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="dt_ext_autofill.html" class="">Autofill</a></li>
                        <li class="nav-item pcoded-hasmenu">
                            <a href="#!" class="nav-link"><span class="pcoded-mtext">Button</span></a>
                            <ul class="pcoded-submenu">
                                <li class=""><a href="dt_ext_basic_buttons.html" class="">Basic button</a></li>
                                <li class=""><a href="dt_ext_export_buttons.html" class="">Data export</a></li>
                            </ul>
                        </li>
                        <li class=""><a href="dt_ext_col_reorder.html" class="">Col reorder</a></li>
                        <li class=""><a href="dt_ext_fixed_columns.html" class="">Fixed columns</a></li>
                        <li class=""><a href="dt_ext_fixed_header.html" class="">Fixed header</a></li>
                        <li class=""><a href="dt_ext_key_table.html" class="">Key table</a></li>
                        <li class=""><a href="dt_ext_responsive.html" class="">Responsive</a></li>
                        <li class=""><a href="dt_ext_row_reorder.html" class="">Row reorder</a></li>
                        <li class=""><a href="dt_ext_scroller.html" class="">Scroller</a></li>
                        <li class=""><a href="dt_ext_select.html" class="">Select table</a></li>
                    </ul>
                </li>
                <li data-username="table editable" class="nav-item"><a href="editable_table.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-grid"></i></span><span class="pcoded-mtext">Editable table</span></a></li>
                <li data-username="table foo" class="nav-item"><a href="tbl_foo.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-list"></i></span><span class="pcoded-mtext">Foo table</span></a></li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Chart & Maps</label>
                </li>
                <li data-username="Charts AM Chart js Echart Google Highchart Knob Morris Nvd3 Peity Radial" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-pie-chart"></i></span><span class="pcoded-mtext">Chart</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="chart-am.html" class="">amChart 4</a></li>
                        <li class=""><a href="chart-chartjs.html" class="">Chart js</a></li>
                        <li class=""><a href="chart-echart.html" class="">Echart</a></li>
                        <li class=""><a href="chart-google.html" class="">Google</a></li>
                        <li class=""><a href="chart-highchart.html" class="">Highchart</a></li>
                        <li class=""><a href="chart-knob.html" class="">Knob</a></li>
                        <li class=""><a href="chart-morris.html" class="">Morris</a></li>
                        <li class=""><a href="chart-nvd3.html" class="">Nvd3</a></li>
                        <li class=""><a href="chart-peity.html" class="">Peity</a></li>
                        <li class=""><a href="chart-radial.html" class="">Radial</a></li>
                    </ul>
                </li>
                <li data-username="Maps Google Vector Google Map Search API Location" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-map"></i></span><span class="pcoded-mtext">Maps</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="map-google.html" class="">Google</a></li>
                        <li class=""><a href="map-vector.html" class="">Vector</a></li>
                        <li class=""><a href="map-api.html" class="">Gmap search API</a></li>
                        <li class=""><a href="map-location.html" class="">Location</a></li>
                    </ul>
                </li>
                <li data-username="landing page" class="nav-item"><a href="extra-pages/landingpage/index.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-navigation-2"></i></span><span class="pcoded-mtext">Landing
                            page</span></a></li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Pages</label>
                </li>
                <li data-username="Authentication Sign up Sign in reset password Change password Personal information profile settings map form subscribe" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-lock"></i></span><span class="pcoded-mtext">Authentication</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="auth-signup.html" class="">Sign up</a></li>
                        <li class=""><a href="auth-signup1.html" class="">Sign up v2<span class="pcoded-badge label label-primary">New</span></a></li>
                        <li class=""><a href="auth-signin.html" class="">Sign in</a></li>
                        <li class=""><a href="auth-signin-img-side.html" class="">Sign in v2<span class="pcoded-badge label label-primary">New</span></a></li>
                        <li class=""><a href="auth-signin-img-slider.html" class="">Sign in v3<span class="pcoded-badge label label-primary">New</span></a></li>
                        <li class=""><a href="auth-signin-img-slider2.html" class="">Sign in v4<span class="pcoded-badge label label-primary">New</span></a></li>
                        <li class=""><a href="auth-signin-img-tabs.html" class="">Sign in v5<span class="pcoded-badge label label-primary">New</span></a></li>
                        <li class=""><a href="auth-reset-password.html" class="">Reset password</a></li>
                        <li class=""><a href="auth-change-password.html" class="">Change password</a></li>
                        <li class=""><a href="auth-profile-settings.html" class="">Profile settings</a></li>
                    </ul>
                </li>
                <li data-username="Maintenance Error offline ui" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-sliders"></i></span><span class="pcoded-mtext">Maintenance</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="maint-error.html" class="">Error</a></li>

                        <li class=""><a href="maint-offline-ui.html" class="">Offline UI</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>App</label>
                </li>
                <li data-username="message" class="nav-item"><a href="message.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-message-circle"></i></span><span class="pcoded-mtext">Message</span></a></li>
                <li data-username="inbox read mail compose" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-mail"></i></span><span class="pcoded-mtext">Email</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="email_inbox.html" class="">Inbox</a></li>
                        <li class=""><a href="email_read.html" class="">Read mail</a></li>
                        <li class=""><a href="email_compose.html" class="">Compose mail</a></li>
                    </ul>
                </li>
                <li data-username="Task list board details" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-clipboard"></i></span><span class="pcoded-mtext">Task</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="task-list.html" class="">List</a></li>
                        <li class=""><a href="task-board.html" class="">Board</a></li>
                        <li class=""><a href="task-detail.html" class="">Detail</a></li>
                    </ul>
                </li>
                <li data-username="To-Do notes" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-check-square"></i></span><span class="pcoded-mtext">To-Do</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="todo.html" class="">To-Do</a></li>
                        <li class=""><a href="notes.html" class="">Notes</a></li>
                    </ul>
                </li>
                <li data-username="Gallery Grid Masonry Advance" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-image"></i></span><span class="pcoded-mtext">Gallery</span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="gallery-grid.html" class="">Grid</a></li>
                        <li class=""><a href="gallery-masonry.html" class="">Masonry</a></li>
                        <li class=""><a href="gallery-advance.html" class="">Advance</a></li>
                    </ul>
                </li>
                <li data-username="search1 search2 search2" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-search"></i></span><span class="pcoded-mtext">Search<span class="pcoded-badge label label-info">NEW</span></span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="search1.html" class="">Search1</a></li>
                        <li class=""><a href="search2.html" class="">Search2</a></li>
                        <li class=""><a href="search3.html" class="">Search3</a></li>
                    </ul>
                </li>
                <li data-username="search1 search2 search2" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">Helpdesk<span class="pcoded-badge label label-success">NEW</span></span></a>
                    <ul class="pcoded-submenu">
                        <li class=""><a href="hd-help-desk.html" class="">Helpdesk dashboard</a></li>
                        <li class=""><a href="hd-dashboard.html" class="">Customer dashboard</a></li>
                        <li class=""><a href="hd-cust-list.html" class="">Customer list</a></li>
                        <li class=""><a href="hd-detail.html" class="">Customer detail</a></li>
                        <li class=""><a href="hd-new-ticket.html" class="">Ticket</a></li>
                    </ul>
                </li>
                <li class="nav-item pcoded-menu-caption">
                    <label>Extension</label>
                </li>
                <li data-username="Editor CK-Editor Tinemce" class="nav-item pcoded-hasmenu">
                    <a href="#!" class="nav-link"><span class="pcoded-micon"><i class="feather icon-file-plus"></i></span><span class="pcoded-mtext">Editor</span></a>
                    <ul class="pcoded-submenu">
                        <li class="pcoded-hasmenu">
                            <a href="#!" class="">CK editor</a>
                            <ul class="pcoded-submenu">
                                <li class=""><a href="editor-classic.html" class="">Classic</a></li>
                                <li class=""><a href="editor-balloon.html" class="">Balloon</a></li>
                                <li class=""><a href="editor-inline.html" class="">Inline</a></li>
                                <li class=""><a href="editor-document.html" class="">Document</a></li>
                            </ul>
                        </li>
                        <li class=""><a href="editor-tinymce.html" class="">Tinymce editor</a></li>
                    </ul>
                </li>
                <li data-username="Full Calendar" class="nav-item"><a href="full-calendar.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-calendar"></i></span><span class="pcoded-mtext">Full calendar</span></a></li>
                <li data-username="File Upload" class="nav-item"><a href="file-upload.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-upload-cloud"></i></span><span class="pcoded-mtext">File upload</span></a></li>
                <li data-username="image cropper" class="nav-item"><a href="image_crop.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-scissors"></i></span><span class="pcoded-mtext">Image cropper</span></a></li>
                <li data-username="grid animation" class="nav-item"><a href="grid-animation.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-globe"></i></span><span class="pcoded-mtext">Grid animation</span><span class="pcoded-badge label label-info">NEW</span></a></li>
                <li data-username="minimal form" class="nav-item"><a href="minimal-form.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-book"></i></span><span class="pcoded-mtext">Minimal form</span><span class="pcoded-badge label label-danger">NEW</span></a></li>
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
                <li data-username="Documentation" class="nav-item"><a href="<?php echo base_url(); ?>doc/index.html" class="nav-link"><span class="pcoded-micon"><i class="feather icon-book"></i></span><span class="pcoded-mtext">Documentation</span></a></li>
                <li data-username="Need Support" class="nav-item"><a href="#" class="nav-link"><span class="pcoded-micon"><i class="feather icon-help-circle"></i></span><span class="pcoded-mtext">Need
                            support ?</span></a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- [ navigation menu ] end -->

<!-- [ Header ] start -->
<header class="navbar pcoded-header navbar-expand-lg navbar-light">
    <div class="container">
        <div class="m-header">
            <a class="mobile-menu" id="mobile-collapse1" href="#!"><span></span></a>
            <a href="index.html" class="b-brand">
                <!-- <div class="b-bg">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="b-title">Flash Able</span> -->
                <img src="<?php echo base_url(); ?>assets/images/logo-dark.svg" alt="" class="logo images">
                <img src="<?php echo base_url(); ?>assets/images/logo-icon-dark.svg" alt="" class="logo-thumb images">
            </a>
        </div>
        <a class="mobile-menu" id="mobile-header" href="#!">
            <i class="feather icon-more-horizontal"></i>
        </a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <div class="main-search open">
                        <div class="input-group">
                            <input type="text" id="m-search" class="form-control" placeholder="Search . . .">
                            <a href="#!" class="input-group-append search-close">
                                <i class="feather icon-x input-group-text"></i>
                            </a>
                            <span class="input-group-append search-btn btn btn-primary">
                                <i class="feather icon-search input-group-text"></i>
                            </span>
                        </div>
                    </div>
                </li>
                <!-- <li> -->
                <!-- [ breadcrumb ] start -->
                <!-- <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Horizontal Layout</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Page Layouts</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Horizontal Layout</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                <!-- [ breadcrumb ] end -->
                <!-- </li> -->
            </ul>
            <ul class="navbar-nav ml-auto">
                <li>
                    <div class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown"><i class="icon feather icon-bell"></i></a>
                        <div class="dropdown-menu dropdown-menu-right notification">
                            <div class="noti-head">
                                <h6 class="d-inline-block m-b-0">Notifications</h6>
                                <div class="float-right">
                                    <a href="#!" class="m-r-10">mark as read</a>
                                    <a href="#!">clear all</a>
                                </div>
                            </div>
                            <ul class="noti-body">
                                <li class="n-title">
                                    <p class="m-b-0">NEW</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>John Doe</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>5 min</span></p>
                                            <p>New ticket Added</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="n-title">
                                    <p class="m-b-0">EARLIER</p>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>10 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>12 min</span></p>
                                            <p>currently login</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>30 min</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Sara Soudein</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>1 hour</span></p>
                                            <p>currently login</p>
                                        </div>
                                    </div>
                                </li>
                                <li class="notification">
                                    <div class="media">
                                        <img class="img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                        <div class="media-body">
                                            <p><strong>Joseph William</strong><span class="n-time text-muted"><i class="icon feather icon-clock m-r-10"></i>2 hour</span></p>
                                            <p>Prchace New Theme and make payment</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="noti-footer">
                                <a href="#!">show all</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li><a href="#!" class="displayChatbox"><i class="icon feather icon-mail"></i></a></li>
                <li>
                    <div class="dropdown drp-user">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="icon feather icon-settings"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right profile-notification">
                            <div class="pro-head">
                                <img src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" class="img-radius" alt="User-Profile-Image">
                                <span>John Doe</span>
                                <a href="auth-signin.html" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out"></i>
                                </a>
                            </div>
                            <ul class="pro-body">
                                <li><a href="#!" class="dropdown-item"><i class="feather icon-settings"></i> Settings</a></li>
                                <li><a href="#!" class="dropdown-item"><i class="feather icon-user"></i> Profile</a></li>
                                <li><a href="message.html" class="dropdown-item"><i class="feather icon-mail"></i> My Messages</a></li>
                                <li><a href="auth-signin.html" class="dropdown-item"><i class="feather icon-lock"></i> Lock Screen</a></li>
                            </ul>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
<!-- [ Header ] end -->

<!-- [ chat user list ] start -->
<section class="header-user-list">
    <a href="#!" class="h-close-text"><i class="feather icon-x"></i></a>
    <ul class="nav nav-tabs" id="chatTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active text-uppercase" id="chat-tab" data-toggle="tab" href="#chat" role="tab" aria-controls="chat" aria-selected="true"><i class="feather icon-message-circle mr-2"></i>Chat</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-uppercase" id="user-tab" data-toggle="tab" href="#user" role="tab" aria-controls="user" aria-selected="false"><i class="feather icon-users mr-2"></i>User</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-uppercase" id="setting-tab" data-toggle="tab" href="#setting" role="tab" aria-controls="setting" aria-selected="false"><i class="feather icon-settings mr-2"></i>Setting</a>
        </li>
    </ul>
    <div class="tab-content" id="chatTabContent">
        <div class="tab-pane fade show active" id="chat" role="tabpanel" aria-labelledby="chat-tab">
            <div class="h-list-header">
                <div class="input-group">
                    <input type="text" id="search-friends" class="form-control" placeholder="Search Friend . . .">
                </div>
            </div>
            <div class="h-list-body">
                <div class="main-friend-cont scroll-div">
                    <div class="main-friend-list">
                        <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image ">
                                <div class="live-status">3</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                <div class="live-status">1</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                <div class="live-status">1</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-4.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image ">
                                <div class="live-status">3</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                <div class="live-status">1</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image">
                                <div class="live-status">1</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Alia<small class="d-block text-muted">10 min ago</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-4.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Suzen<small class="d-block text-muted">15 min ago</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image ">
                                <div class="live-status">3</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Josephin Doe<small class="d-block text-c-green">Typing . . </small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image">
                                <div class="live-status">1</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Lary Doe<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Alice<small class="d-block text-c-green">online</small></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="user" role="tabpanel" aria-labelledby="user-tab">
            <div class="h-list-body">
                <div class="main-friend-cont scroll-div">
                    <div class="main-friend-list">
                        <div class="media px-3 d-flex align-items-center mt-3">
                            <a class="media-left m-r-15" href="#!">
                                <div class="hei-50 wid-50 bg-primary img-radius d-flex text-white f-22 align-items-center justify-content-center"><i class="icon feather icon-users"></i></div>
                            </a>
                            <div class="media-body">
                                <p class="chat-header f-w-600 mb-0">New Group</p>
                            </div>
                        </div>
                        <div class="media p-3 d-flex align-items-center">
                            <a class="media-left m-r-15" href="#!">
                                <div class="hei-50 wid-50 bg-primary img-radius d-flex text-white f-22 align-items-center justify-content-center"><i class="icon feather icon-user-plus"></i></div>
                            </a>
                            <div class="media-body">
                                <p class="chat-header f-w-600 mb-0">New Contact</p>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image "></a>
                            <div class="media-body">
                                <p class="chat-header">Josephin Doe<small class="d-block">i am not what happened . .</small></p>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Lary Doe<small class="d-block">Avalable</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="3" data-status="online" data-username="Alice">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-3.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Alice<small class="d-block">hear using Flash Able</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="4" data-status="offline" data-username="Alia">
                            <a class="media-left" href="#!">
                                <div class="hei-50 wid-50 img-radius bg-success d-flex text-white f-22 align-items-center justify-content-center">A</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Alia<small class="d-block text-muted">Avalable</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="5" data-status="offline" data-username="Suzen">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-4.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Suzen<small class="d-block text-muted">Avalable</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="1" data-status="online" data-username="Josephin Doe">
                            <a class="media-left" href="#!">
                                <div class="hei-50 wid-50 bg-danger img-radius d-flex text-white f-22 align-items-center justify-content-center">JD</div>
                            </a>
                            <div class="media-body">
                                <h6 class="chat-header">Josephin Doe<small class="d-block text-muted">Don't send me image</small></h6>
                            </div>
                        </div>
                        <div class="media userlist-box" data-id="2" data-status="online" data-username="Lary Doe">
                            <a class="media-left" href="#!"><img class="media-object img-radius" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image"></a>
                            <div class="media-body">
                                <h6 class="chat-header">Lary Doe<small class="d-block text-muted">not send free msg</small></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="setting" role="tabpanel" aria-labelledby="setting-tab">
            <div class="p-4 main-friend-cont scroll-div">
                <h6 class="mt-2"><i class="feather icon-monitor mr-2"></i>Desktop settings</h6>
                <hr>
                <div class="form-group mb-0">
                    <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="cn-p-1" checked>
                        <label for="cn-p-1" class="cr"></label>
                    </div>
                    <label class="f-w-600">Allow desktop notification</label>
                </div>
                <p class="text-muted ml-5">you get lettest content at a time when data will updated</p>
                <div class="form-group mb-0">
                    <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="cn-p-5">
                        <label for="cn-p-5" class="cr"></label>
                    </div>
                    <label class="f-w-600">Store Cookie</label>
                </div>
                <h6 class="mb-0 mt-5"><i class="feather icon-layout mr-2"></i>Application settings</h6>
                <hr>
                <div class="form-group mb-0">
                    <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="cn-p-3" checked>
                        <label for="cn-p-3" class="cr"></label>
                    </div>
                    <label class="f-w-600">Backup Storage</label>
                </div>
                <p class="text-muted mb-0 ml-5">Automaticaly take backup as par schedule</p>
                <div class="form-group mb-4">
                    <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="cn-p-4" checked>
                        <label for="cn-p-4" class="cr"></label>
                    </div>
                    <label class="f-w-600">Allow guest to print file</label>
                </div>
                <h6 class="mb-0 mt-5"><i class="feather icon-globe mr-2"></i>System settings</h6>
                <hr>
                <div class="form-group mb-0">
                    <div class="switch switch-primary d-inline m-r-10">
                        <input type="checkbox" id="cn-p-2">
                        <label for="cn-p-2" class="cr"></label>
                    </div>
                    <label class="f-w-600">View other user chat</label>
                </div>
                <p class="text-muted ml-5">Allow to show public user message</p>
            </div>
        </div>
    </div>
</section>
<!-- [ chat user list ] end -->

<!-- [ chat message ] start -->
<section class="header-chat">
    <div class="h-list-header">
        <h6>Josephin Doe</h6>
        <a href="#!" class="h-back-user-list"><i class="feather icon-chevron-left"></i></a>
    </div>
    <div class="h-list-body">
        <div class="main-chat-cont scroll-div">
            <div class="main-friend-chat">
                <div class="media chat-messages">
                    <a class="media-left photo-table" href="#!"><img class="media-object img-radius img-radius m-t-5" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image"></a>
                    <div class="media-body chat-menu-content">
                        <div class="">
                            <p class="chat-cont">hello tell me something</p>
                            <p class="chat-cont">about yourself?</p>
                        </div>
                        <p class="chat-time">8:20 a.m.</p>
                    </div>
                </div>
                <div class="media chat-messages">
                    <div class="media-body chat-menu-reply">
                        <div class="">
                            <p class="chat-cont">Ohh! very nice</p>
                        </div>
                        <p class="chat-time">8:22 a.m.</p>
                    </div>
                    <a class="media-right photo-table" href="#!"><img class="media-object img-radius img-radius m-t-5" src="<?php echo base_url(); ?>assets/images/user/avatar-1.jpg" alt="Generic placeholder image"></a>
                </div>
                <div class="media chat-messages">
                    <a class="media-left photo-table" href="#!"><img class="media-object img-radius img-radius m-t-5" src="<?php echo base_url(); ?>assets/images/user/avatar-2.jpg" alt="Generic placeholder image"></a>
                    <div class="media-body chat-menu-content">
                        <div class="">
                            <p class="chat-cont">can you help me?</p>
                        </div>
                        <p class="chat-time">8:20 a.m.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-list-footer">
        <div class="input-group">
            <input type="file" class="chat-attach" style="display:none">
            <a href="#!" class="input-group-prepend btn btn-success btn-attach">
                <i class="feather icon-paperclip"></i>
            </a>
            <input type="text" name="h-chat-text" class="form-control h-send-chat" placeholder="Write hear . . ">
            <button type="submit" class="input-group-append btn-send btn btn-primary">
                <i class="feather icon-message-circle"></i>
            </button>
        </div>
    </div>
</section>
<!-- [ chat message ] end -->

<!-- [ Main Content ] start -->
<div class="pcoded-main-container">
    <div class="pcoded-wrapper container">
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Horizontal Layout 2</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="index.html"><i class="feather icon-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Page Layout</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Horizontal Layout 2</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ Main Content ] start -->
                        <div class="row">
                            <!-- [ horizontal-layout ] start -->
                            <div class="col-sm-12">

                                <div class="card">
                                    <div class="card-header">
                                        <h5>Page Layout</h5>
                                        <div class="card-header-right">
                                            <div class="btn-group card-option">
                                                <button type="button" class="btn dropdown-toggle btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="feather icon-more-horizontal"></i>
                                                </button>
                                                <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                                                    <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                                                    <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                                                    <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                                                    <li class="dropdown-item close-card"><a href="#!"><i class="feather icon-trash"></i> remove</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <?php $this->load->view($module . "/" . $view); ?>
                                    </div>
                                </div>
                            </div>
                            <!-- [ horizontal-layout ] end -->
                        </div>
                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

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