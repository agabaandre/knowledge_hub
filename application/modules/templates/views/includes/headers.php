<head>
    <title><?php echo setting()->title; ?></title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="<?php echo setting()->site_description; ?>" />
    <meta name="keywords" content="<?php echo setting()->seo_keywords; ?>">
    <meta name="author" content="Africa CDC" />

    <!-- Favicon icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="<?php echo base_url(); ?>assets/images/icon_Africa_cdc.png" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/animation/css/animate.min.css">
    <!-- prism css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/prism/css/prism.min.css">
    <!-- vendor css -->
    <!-- select2 css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/select2/css/select2.min.css">
    <!-- multi-select css -->

    <!-- material datetimepicker css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/material-datetimepicker/css/bootstrap-material-datetimepicker.css">
    <!-- Bootstrap datetimepicker css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/bootstrap-datetimepicker/css/bootstrap-datepicker3.min.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/fonts/material/css/materialdesignicons.min.css">
    <!-- minicolors css -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/mini-color/css/jquery.minicolors.css">

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/multi-select/css/multi-select.css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style.css">
    <script src="<?php echo base_url() ?>node_modules/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/highcharts.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/highcharts-more.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/modules/solid-gauge.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/modules/exporting.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/modules/export-data.js"></script>
    <script src="<?php echo base_url() ?>node_modules/highcharts/modules/accessibility.js"></script>
<<<<<<< HEAD
    <style>
        .select2-close-mask {
            z-index: 2099;
        }

        .select2-dropdown {
            z-index: 3051;
        }
    </style>
=======

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/blockui.min.js"></script>
    
    <script type="text/javascript">
        function showLoader(){
            try{
            $.blockUI({ message: '<h1><img src="<?php echo base_url(); ?>assets/images/loader.gif" /> Please wait...</h1>' });
           }catch(err){
                console.log(err);
           }
        }

        function hideLoader(){
            try{
            $.unblockUI();
            }catch(err){
                console.log(err);
           }
        }
    </script>
>>>>>>> 889f410cf8027ef5803c6cbe57cfa855f8e8eabf
</head>

<body class="" style="overflow-x: hidden;">