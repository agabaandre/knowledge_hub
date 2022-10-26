<!-- Required Js -->
<script src="<?php echo base_url() ?>assets/js/vendor-all.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/pcoded.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>

<!-- material datetimepicker Js -->
<script src="http://momentjs.com/downloads/moment-with-locales.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<!-- minicolors Js -->
<script src="<?php echo base_url() ?>assets/plugins/mini-color/js/jquery.minicolors.min.js"></script>

<!-- select2 Js -->
<script src="<?php echo base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>

<!-- multi-select Js -->
<script src="<?php echo base_url() ?>assets/plugins/multi-select/js/jquery.quicksearch.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/multi-select/js/jquery.multi-select.js"></script>
<!-- sweet alert Js -->
<script src="<?php echo base_url() ?>assets/plugins/sweetalert/js/sweetalert.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/pages/ac-alert.js"></script>

<!-- form-select-custom Js -->
<script src="<?php echo base_url() ?>assets/js/pages/form-select-custom.js"></script>

<!-- form-picker-custom Js -->
<script src="<?php echo base_url() ?>assets/js/pages/form-picker-custom.js"></script>

<!-- prism Js -->
<script src="<?php echo base_url() ?>assets/plugins/prism/js/prism.min.js"></script>




<script>
    // Active Color
    $('.active-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        $('.active-color > a').removeClass('active');
        $(this).addClass('active');
        if (temp == "active-default") {
            $('.pcoded-navbar').removeClassPrefix('active-');
        } else {
            $('.pcoded-navbar').removeClassPrefix('active-');
            $('.pcoded-navbar').addClass(temp);
        }
    });
    // Caption Hide
    $('#caption-hide').change(function() {
        if ($(this).is(":checked")) {
            $('.pcoded-navbar').addClass('caption-hide');
        } else {
            $('.pcoded-navbar').removeClass('caption-hide');
        }
    });
    // title Color
    $('.title-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        $('.title-color > a').removeClass('active');
        $(this).addClass('active');
        if (temp == "title-default") {
            $('.pcoded-navbar').removeClassPrefix('title-');
        } else {
            $('.pcoded-navbar').removeClassPrefix('title-');
            $('.pcoded-navbar').addClass(temp);
        }
    });
    // Menu Icon Color
    $('#icon-colored').change(function() {
        if ($(this).is(":checked")) {
            $('.pcoded-navbar').addClass('icon-colored');
        } else {
            $('.pcoded-navbar').removeClass('icon-colored');
        }
    });
    // Box layouts
    $('#box-layouts').change(function() {
        if ($(this).is(":checked")) {
            $('body').addClass('container');
            $('body').addClass('box-layout');
        } else {
            $('body').removeClass('container');
            $('body').removeClass('box-layout');
        }
    });
    // rtl layouts
    $('#theme-rtl').change(function() {
        $('head').append('<link rel="stylesheet" class="rtl-css" href="">');
        if ($(this).is(":checked")) {
            $('.rtl-css').attr("href", "<?php echo base_url() ?>assets/css/layouts/rtl.css");
        } else {
            $('.rtl-css').attr("href", "");
        }
    });
    // brand Color
    $('.brand-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        $('.brand-color > a').removeClass('active');
        $(this).addClass('active');
        $('.pcoded-navbar').removeClassPrefix('brand-');
        $('.pcoded-navbar').addClass(temp);
    });
    // Header Color
    $('.header-color > a').on('click', function() {
        var temp = $(this).attr('data-value');
        $('.header-color > a').removeClass('active');
        $(this).addClass('active');
        if (temp == "header-default") {
            $('.pcoded-header').removeClassPrefix('header-');
        } else {
            $('.pcoded-header').removeClassPrefix('header-');
            $('.pcoded-header').addClass(temp);
        }
    });
    // Menu Dropdown icon
    function drpicon(temp) {
        if (temp == "style1") {
            $('.pcoded-navbar').removeClassPrefix('drp-icon-');
        } else {
            $('.pcoded-navbar').removeClassPrefix('drp-icon-');
            $('.pcoded-navbar').addClass('drp-icon-' + temp);
        }
    }
    // Menu subitem icon
    function menuitemicon(temp) {
        if (temp == "style1") {
            $('.pcoded-navbar').removeClassPrefix('menu-item-icon-');
        } else {
            $('.pcoded-navbar').removeClassPrefix('menu-item-icon-');
            $('.pcoded-navbar').addClass('menu-item-icon-' + temp);
        }
    }
    $.fn.removeClassPrefix = function(prefix) {
        this.each(function(i, it) {
            var classes = it.className.split(" ").map(function(item) {
                return item.indexOf(prefix) === 0 ? "" : item;
            });
            it.className = classes.join(" ");
        });
        return this;
    };
    $('.select2').select2();
</script>

<!-- <div class="footer-fab">
    <div class="b-bg">
        <i class="fas fa-question"></i>
    </div>
    <div class="fab-hover">
        <ul class="list-unstyled">
            <li><a href="<?php echo base_url() ?>doc/index-bc-package.html" data-text="UI Kit" class="btn btn-icon btn-rounded btn-info m-0"><i class="feather icon-layers"></i></a></li>
            <li><a href="<?php echo base_url() ?>doc/index.html" data-text="Document" class="btn btn-icon btn-rounded btn-primary m-0"><i class="feather icon feather icon-book"></i></a></li>
        </ul>
    </div>
</div> -->

<!-- select2 Js -->
<script src="<?php echo base_url() ?>assets/plugins/select2/js/select2.full.min.js"></script>

<!-- multi-select Js -->
<script src="<?php echo base_url() ?>assets/plugins/multi-select/js/jquery.quicksearch.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/multi-select/js/jquery.multi-select.js"></script>

<script src="<?php echo base_url() ?>assets/js/analytics.js"></script>

</body>

</html>