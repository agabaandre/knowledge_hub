<!DOCTYPE html>
<html lang="en">

<head>

  <title>Africa CDC Knoledge Hub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Flash Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
  <meta name="keywords" content="admin templates, bootstrap admin templates, bootstrap 4, dashboard, dashboard templets, sass admin templets, html admin templates, responsive, bootstrap admin templates free download,premium bootstrap admin templates, Flash Able, Flash Able bootstrap admin template">
  <meta name="author" content="Codedthemes" />

  <!-- Favicon icon -->
  <link rel="icon" href="<?php echo base_url(); ?>assets/images/icon_Africa_cdc.png" type="image/x-icon">
  <!-- fontawesome icon -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/fonts/fontawesome/css/fontawesome-all.min.css">
  <!-- animation css -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/animation/css/animate.min.css">
  <!-- vendor css -->
  <link rel="stylesheet" href="<?php echo base_url() ?>assets/css/style.css">
</head>
<!-- [ auth-signin ] start -->
<div class="auth-wrapper">
  <div class="auth-content container">
    <div class="card">
      <div class="row align-items-center">
        <div class="col-md-6">
          <div class="card-body">
            <img src="<?php echo base_url() ?>assets/images/cdc_square.png.png" alt="" class="img-fluid mb-4">
            <h3 class="mb-3 f-w-400 justify-content-center">Sign In</h3>
            <?php echo form_open_multipart(base_url('auth/login'), array('id' => 'filetypes', 'class' => 'filetypes')); ?>


            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="feather icon-mail"></i></span>
              </div>
              <input type="text" name="username" class="form-control" placeholder="Email address">
            </div>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="feather icon-lock"></i></span>
              </div>
              <input type="password" name="password" class="form-control" placeholder="Password">
              <input type="hidden" name="route" class="form-control" value="<?php echo $this->uri->segment(1) ?>/<?php echo $this->uri->segment(2) ?>" placeholder="Password">
            </div>

            <div class="form-group text-left mt-2">
              <div class="checkbox checkbox-primary d-inline">
                <input type="checkbox" name="checkbox-fill-1" id="checkbox-fill-a1" checked="">
                <label for="checkbox-fill-a1" class="cr"> Save credentials</label>
              </div>
            </div>
            <button class="btn btn-primary mb-4">Login</button>

            </form>
            <p class="mb-2 text-muted">Forgot password? <a href="auth-reset-password.html" class="f-w-400">Reset</a></p>

          </div>
        </div>
        <div class="col-md-6 d-none d-md-block">

          <img src="<?php echo base_url() ?>assets/images/image_login.png" alt="" class="img-fluid">
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ auth-signin ] end -->

<!-- Required Js -->
<script src="<?php echo base_url() ?>assets/js/vendor-all.min.js"></script>
<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>



</body>

</html>