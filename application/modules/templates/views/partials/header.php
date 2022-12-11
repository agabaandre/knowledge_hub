	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
	<div class="header">
		<div class="container">
			<nav id="navigation" class="navigation navigation-landscape">
				<div class="nav-header">
					<a class="nav-brand" href="<?php echo base_url() ?>">
						<img src="<?php echo base_url() ?>assets/images/logo.png" class="logo" alt="" style="width:280px;" />
					</a>
					<div class="nav-toggle"></div>
					<div class="mobile_nav">
						<ul>
							<li>
								<a href="#" data-toggle="modal" data-target="#login" class="theme-cl fs-lg">
									<i class="lni lni-user"></i>
								</a>
							</li>

						</ul>
					</div>
				</div>
				<div class="nav-menus-wrapper" style="transition-property: none;">

					<!-- Use CSS to replace link text with flag icons -->

					<ul class="nav-menu">

						<li><a href="<?php echo base_url(); ?>">Home</a></li>
						<li><a href="<?php echo base_url('records/search'); ?>">Search</a></li>
						<li><a href="<?php echo base_url('forums/index'); ?>">Forums</a></li>
						<li><a href="<?php echo base_url('faqs'); ?>">FAQs</a></li>



					</ul>

					<ul class="nav-menu nav-menu-social align-to-right">
						<?php if (is_guest()) : ?>
							<li>
								<a href="#" data-toggle="modal" data-target="#login" class="ft-medium text-bold">
									<i class="lni lni-user mr-2"></i>Sign In
								</a>
							</li>

							<li>
								<a href="<?php echo base_url('account/register'); ?>" class="ft-medium text-bold">
									Register
								</a>
							</li>

						<?php else : ?>

							<li>
								<a href="<?php echo base_url('account/publications'); ?>" class="ft-medium text-success">
									Our Publications
								</a>
							</li>

							<li class="add-listing theme-bg bg-central">
								<a href="<?php echo base_url('account/publish'); ?>">
									<i class="lni lni-circle-plus mr-1"></i>Publish a resource
								</a>
							</li>
							<li>
								<a href="<?php echo base_url('logout'); ?>" class="ft-medium">
									Logout
								</a>
							</li>
						<?php endif; ?>
						<div id="google_translate_element" style="display: none;"></div>
						<select class="selectpicker" data-width="fit" onchange="translateLanguage(this.value);">
							<option data-content='<span class="flag-icon flag-icon-us"></span> English' value="English">English</option>
							<option data-content='<span class="flag-icon flag-icon-ar"></span> Arabic' value="Arabic">Arabic</option>
							<option data-content='<span class="flag-icon flag-icon-fr"></span> French' value="French">French</option>
							<option data-content='<span class="flag-icon flag-icon-pt"></span> Portuguese' value="Portuguese">Portuguese (Portugal)</option>

						</select>
						<div>
					</ul>
				</div>
			</nav>
		</div>
	</div>
	<!-- End Navigation -->
	<div class="clearfix"></div>
	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->