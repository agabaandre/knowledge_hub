	<div class="gray  mb-5">
    <!-- ======================= Publication Info ======================== -->
	<div class="bg-light  rounded py-5" style="background-image: url(<?php echo base_url() ?>resources/img/dots.png); background-repeat:repeat-x; background-size:contain;">
		<div class="container">
			<div class="row">
				<div class="col-xl-12 col-lg-12 col-md-12 col-12">
					<div class="jbd-01 d-flex align-items-center justify-content-between">
						<div class="jbd-flex d-flex align-items-center justify-content-start">
							<div class="jbd-01-thumb">
								
							</div>
							<div class="jbd-01-caption pl-3">
								<div class="tbd-title">
									<h4 class="mb-0 ft-medium fs-lg">
										Register for an account
									</h4>
								</div>
								
							</div>
						</div>
                        <div class="jbd-01-right text-right">
							<div class="jbl_button mb-2"><a href="#login" data-toggle="modal" class="btn btn-md rounded theme-bg-light theme-cl fs-sm ft-medium">Login Instead</a></div>
                    </div>
					</div>
                    
				</div>
			</div>
		</div>
	</div>
	<!-- ======================= Publication Info ======================== -->
	<div class="container">

	<!-- All faqs -->
<div class="row justify-content-center">

<?php echo form_open(base_url('account/register'),['class'=>'card col-lg-6','style'=>'margin-top:-50px!important;']); ?>
<div class="card-body">

<input type="hidden" name="route" value="front" />

<div class="row mb-4 mt-2">
                     
  <div class="form-group col-lg-12">
    <label for="orgtoggle" class="form-label ">Register as</label>
    <select id="orgtoggle" class="form-control form-select" onchange="toggleOrgInfo($(this).val())">
    <option value="1">Individual</option>
    <option>Organization</option>
    </select>
    </div>
</div>

<div class="row mt-2" id="orgInfo" style="display: none;">
                      
    <div class="form-group col-lg-12">
    <label for="orgName">Organization Name</label>
    <input type="text" class="form-control" id="orgName" value="<?php echo old('orgName')?>" placeholder="Organization Name" name="orgName" >
    </div>

    <h4 class="mt-3 d-block px-3">Contact Information</h4>
</div>
<div class="form-group">
    <label>First Name</label>
    <input type="text" name="firstname" class="form-control" value="<?php echo old('firstname')?>" placeholder="First Name*" required>
</div>

<div class="form-group">
    <label>Other Names</label>
    <input type="text" name="othernames" class="form-control" value="<?php echo old('othernames')?>" placeholder="Other Names*" required>
</div>

<div class="form-group">
    <label>Email</label>
    <input type="text" name="email" class="form-control"  value="<?php echo old('email')?>"placeholder="Email*" required>
</div>

<div class="form-group">
    <label>Password</label>
    <input type="password" name="password" class="form-control" placeholder="Password*" required>
</div>

<div class="form-group">
    <label>Confirm Password</label>
    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password*" required>
</div>

<div class="form-group">
    <div class="d-flex align-items-center justify-content-between">
    
        <div class="eltio_k2">
            <a href="#login" data-toggle="modal" class="theme-cl">Login Instead</a>
        </div>
    </div>
</div>

<div class="form-group">
    <button type="submit" name="submit" value="1" class="btn btn-md full-width theme-bg text-light fs-md ft-medium">Register</button>
</div>
</div>
<?php echo form_close(); ?>

    </div>
</div>
<br>
<br>
</div>

<script type="text/javascript">
     
     function toggleOrgInfo(option) {

         console.log(option);

         if (parseInt(option) === 1){
            $('#orgInfo').hide();
           } else {
            $('#orgInfo').show();
          }
     }
</script>