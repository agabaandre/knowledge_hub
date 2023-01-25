<!-- Main-content -->
<div class="main-header main-header-fixed" style="background:rgba(52, 143, 65, 1); color:#FFF !important;">
	<div class=" container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="<?php echo base_url() ?>">
				<img src="<?php echo base_url() ?>assets/images/logo.png" id="change-image" style="filter: brightness(0) invert(1);" width=200>
			</a>
		</div>
		<div class="main-header-center">
			<div class="responsive-logo">
				<img src="<?php echo base_url() ?>assets/images/logo.png" id="change-image" style="filter: brightness(0) invert(1);" width=200px>
			</div>
		</div>
		<div class="main-header-right">
			<div class="dropdown main-header-message right-toggle">
				<div class="nav-item full-screen fullscreen-button">
					<a class="new nav-link full-screen-link menu-icons fullscreen" href="#"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
						</svg></a>
					<a class="new nav-link full-screen-link exit-fullscreen" href="#"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3"></path>
						</svg></a>
				</div>
			</div><!-- Full-screen closed -->
			<div class="dropdown  nav-item main-header-message ">
				<a class="new nav-link menu-icons" href="#">
					<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
					<?php if($pending_forum_comments_count > 0): ?>
						<span class=" pulse-danger"></span>
					<?php endif; ?>
				</a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="menu-header-content text-left d-flex">
						<div class="">
							
							<!-- If comment_count > 0 -->
							<?php if($pending_forum_comments_count > 0): ?>
								<h6 class="menu-header-title mb-0">You have <?php echo $pending_forum_comments_count; ?> forum comments requiring approval</h6>
							<?php else: ?>
								<h6 class="menu-header-title mb-0">You have no pending forum comments</h6>
							<?php endif; ?>
						</div>
						<!-- <div class="my-auto ml-auto">
							<a class="badge badge-pill badge-teal float-right" href="#">Mark All Read</a>
						</div> -->
					</div>
					<div class="main-message-list chat-scroll" style="overflow-y: scroll;">
						<?php foreach($pending_forum_comments as $pending_forum_comment): ?>
						<a href="<?php echo base_url() ?>forum/moderate" class="p-3 d-flex border-bottom">
							<div class="  drop-img  cover-image  " data-image-src="<?php echo base_url() ?>assets/img/faces/11.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>

							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name"><?php echo $pending_forum_comment->created_by->name; ?></h5>
									<p class="time mb-0 text-right ml-auto float-right"><?php echo $pending_forum_comment->created_at; ?></p>
								</div>
								<p class="mb-0 desc"><?php echo $pending_forum_comment->comment; ?></p>
							</div>
						</a>
						<?php endforeach; ?>
					</div>
					<div class="text-center dropdown-footer">
						<a href="<?php echo base_url() ?>forums/moderate">VIEW ALL</a>
					</div>
				</div>
			</div><!-- Main-header-message closed -->
			<div class="dropdown  nav-item main-header-message ">
				<a class="new nav-link menu-icons" href="#">
					<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg>
					<?php if($pending_publication_comments_count > 0): ?>
						<span class=" pulse-danger"></span>
					<?php endif; ?>
				</a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="menu-header-content text-left d-flex">
						<div class="">
							
							<!-- If comment_count > 0 -->
							<?php if($pending_publication_comments_count > 0): ?>
								<h6 class="menu-header-title mb-0">You have <?php echo $pending_publication_comments_count; ?> publication comment requiring approval</h6>
							<?php else: ?>
								<h6 class="menu-header-title mb-0">You have no pending publication comments</h6>
							<?php endif; ?>
						</div>
						<!-- <div class="my-auto ml-auto">
							<a class="badge badge-pill badge-teal float-right" href="#">Mark All Read</a>
						</div> -->
					</div>
					<div class="main-message-list chat-scroll ">
						<?php foreach($pending_publication_comments as $pending_publication_comment): ?>
						<a href="<?php echo base_url() ?>publication/moderate" class="p-3 d-flex border-bottom">
							<div class="  drop-img  cover-image  " data-image-src="<?php echo base_url() ?>assets/img/faces/11.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>

							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name"><?php echo $pending_publication_comment->created_by->name ?? 'Anonymous' ?></h5>
									<p class="time mb-0 text-right ml-auto float-right"><?php echo $pending_publication_comment->created_at; ?></p>
								</div>
								<p class="mb-0 desc"><?php echo $pending_publication_comment->comment; ?></p>
							</div>
						</a>
						<?php endforeach; ?>
					</div>
					<div class="text-center dropdown-footer">
						<a href="<?php echo base_url() ?>forums/moderate">VIEW ALL</a>
					</div>
				</div>
			</div><!-- Main-header-message closed -->
			
			<!-- <div class="dropdown main-header-message right-toggle">
				<a class="nav-link menu-icons" data-toggle="sidebar-right" data-target=".sidebar-right">
					<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="3" y1="12" x2="21" y2="12"></line>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<line x1="3" y1="18" x2="21" y2="18"></line>
					</svg>
				</a>
			</div> -->
			<!-- Main-header-message closed -->
			<div class="dropdown main-profile-menu nav nav-item nav-link">
				<a  class=""><img class="rounded-circle" src="<?php echo base_url(); ?>assets/images/user.jpg" style="width:35px;"></a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="main-header-profile header-img">
						<h6><?php echo $this->session->userdata('user')->name; ?></h6><span><?php echo ucwords($this->session->userdata('user')->group_name); ?></span>
					</div>
					<a class="dropdown-item" href="auth/profile"><i class="bx bx-user-circle"></i> My Profile</a>
					<a class="dropdown-item" href="auth/profile"><i class="bx bx-cog"></i> Change Password</a>
					<a class="dropdown-item" href="auth/logout"><i class="bx bx-log-out-circle"></i> Log Out</a>
				</div>
			</div><!-- Main-profile-menu closed -->
			<button class="navbar-toggler navresponsive-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon bx bx-dots-vertical-rounded"></span>
			</button><!-- Navresponsive closed -->
		</div>
	</div>
</div>
<!-- Main-header closed -->