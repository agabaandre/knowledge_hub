<!DOCTYPE html>
<html lang="en">

<head>

  <title>Africa CDC Knoledge Hub</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <<meta name="description" content="<?php echo setting()->site_description; ?>" />
  <meta name="robots" content="noindex">
  <meta name="author" content="Africa CDC" />
  	<!-- Title -->
	<title><?php echo $title; ?></title>

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
  <div class="auth-content container mt-100">
    <div class="row align-items-center">
      <div class="col-md-6 offset-md-3 align-self-center">
        <div class="card">
          <div class="card-body">
            <img src="<?php echo base_url() ?>assets/images/cdc_square.png.png" alt="" class="img-fluid mb-4">
            <h3 class="mb-3 f-w-400 justify-content-center">Sign In</h3>
            <?php echo form_open_multipart(base_url('auth/login'), array('id' => 'filetypes', 'class' => 'filetypes')); ?>

            <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error_message'); ?>
                </div>
            <?php endif; ?>

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
            <button class="btn btn-success mb-4">Login</button>

            </form>
            <p class="mb-2 text-muted">Forgot password? <a href="auth-reset-password.html" class="f-w-400">Reset</a></p>

          </div>
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