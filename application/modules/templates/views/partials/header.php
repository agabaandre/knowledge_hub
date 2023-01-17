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

							<li><a href="#">Publications</a>
									<ul class="nav-dropdown nav-submenu">
										<li>
										<a href="<?php echo base_url('account/publications'); ?>">
										   <i class="fa fa-list mr-1"></i> Our Publications
										</a>
										</li>
										<li>
										<a href="<?php echo base_url('records/favourites'); ?>">
										   <i class="fa fa-star mr-1"></i> My Favourites
										</a>
										</li>
										<li>
										<a href="<?php echo base_url('account/publish'); ?>">
											<i class="lni lni-circle-plus mr-1"></i>Publish a resource
										</a>
										</li>
									</ul>
								</li>

							<li>
								<a href="<?php echo base_url('logout'); ?>" class="ft-medium">
									Logout
								</a>
							</li>
						<?php endif; ?>

					</ul>
					<div class="align-to-right language-css" style="position:relative; margin-top:22px; margin-right:40px">
						<div id="google_translate_element" style="display: none;"></div>
						<select class="selectpicker" data-width="fit" onchange="translateLanguage(this.value);" style="border:#FFF;"com>
							<option data-content='English' value="English"><span class="flag-icon flag-icon-us"></span>English</option>
							<option data-content='<span class="flag-icon flag-icon-ar"></span> Arabic' value="Arabic">Arabic</option>
							<option data-content='<span class="flag-icon flag-icon-fr"></span> French' value="French">French</option>
							<option data-content='<span class="flag-icon flag-icon-pt"></span> Portuguese' value="Portuguese">Portuguese</option>

						</select>
					</div>
				</div>
			</nav>
		</div>
	</div>
	<!-- End Navigation -->
	<div class="clearfix"></div>
	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->