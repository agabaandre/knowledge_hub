<!-- Main-content -->
<div class="main-header main-header-fixed">
	<div class="container">
		<div class="main-header-left ">
			<a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
			<a class="header-brand" href="index.html">
				<img src="<?php echo base_url() ?>assets/img/brand/logo.png" class="desktop-logo" id="change-image">
			</a>
		</div>
		<div class="main-header-center">
			<div class="responsive-logo">
				<a href="index.html"><img src="<?php echo base_url() ?>assets/img/brand/logo.png" class="mobile-logo" alt="logo"></a>
			</div>
		</div>
		<div class="main-header-right">
			<div id="input-search" class="input-search header-search nav-item my-auto">
				<form class="d-flex">
					<input class="search-input form-control" placeholder="Search for anything..." type="search">
					<span class="search-icon"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"></circle>
							<line x1="21" y1="21" x2="16.65" y2="16.65"></line>
						</svg></span>
				</form>
			</div><!-- search closed -->
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
				<a class="new nav-link menu-icons" href="#"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
						<polyline points="22,6 12,13 2,6"></polyline>
					</svg><span class=" pulse-danger"></span></a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="menu-header-content text-left d-flex">
						<div class="">
							<h6 class="menu-header-title mb-0">5 new Messages</h6>
						</div>
						<div class="my-auto ml-auto">
							<a class="badge badge-pill badge-teal float-right" href="#">Mark All Read</a>
						</div>
					</div>
					<div class="main-message-list chat-scroll ">
						<a href="#" class="p-3 d-flex border-bottom">
							<div class="  drop-img  cover-image  " data-image-src="<?php echo base_url() ?>assets/img/faces/11.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>

							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name">Paul Molive</h5>
									<p class="time mb-0 text-right ml-auto float-right">10 min ago</p>
								</div>
								<p class="mb-0 desc">I'm sorry but i'm not sure how to help you with that..</p>
							</div>
						</a>
						<a href="#" class="p-3 d-flex border-bottom">
							<div class="drop-img cover-image" data-image-src="<?php echo base_url() ?>assets/img/faces/2.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>
							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name">Sahar Dary</h5>
									<p class="time mb-0 text-right ml-auto float-right">13 min ago</p>
								</div>
								<p class="mb-0 desc">Are you ready to pickup your Delivery...</p>
							</div>
						</a>
						<a href="#" class="p-3 d-flex border-bottom">
							<div class="drop-img cover-image" data-image-src="<?php echo base_url() ?>assets/img/faces/9.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>
							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name">Khadija Mehr</h5>
									<p class="time mb-0 text-right ml-auto float-right">20 min ago</p>
								</div>
								<p class="mb-0 desc">Here are some products i found for you form database...</p>
							</div>
						</a>
						<a href="#" class="p-3 d-flex border-bottom">
							<div class="drop-img cover-image" data-image-src="<?php echo base_url() ?>assets/img/faces/12.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>
							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name">Barney Cull</h5>
									<p class="time mb-0 text-right ml-auto float-right">30 min ago</p>
								</div>
								<p class="mb-0 desc">All set! Now, time to get to you now...</p>
							</div>
						</a>
						<a href="#" class="p-3 d-flex border-bottom">
							<div class="drop-img cover-image" data-image-src="<?php echo base_url() ?>assets/img/faces/5.jpg">
								<span class="avatar-status bg-teal"></span>
							</div>
							<div class="wd-90p">
								<div class="d-flex">
									<h5 class="mb-1 name">Petey Cruiser</h5>
									<p class="time mb-0 text-right ml-auto float-right">35 min ago</p>
								</div>
								<p class="mb-0 desc">I'm sorry but i'm not sure how...</p>
							</div>
						</a>
					</div>
					<div class="text-center dropdown-footer">
						<a href="text-center">VIEW ALL</a>
					</div>
				</div>
			</div><!-- Main-header-message closed -->
			<div class="dropdown nav-item main-header-notification">
				<a class="new nav-link menu-icons" href="#"><svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
						<path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
					</svg><span class=" pulse"></span></a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="menu-header-content text-left d-flex">
						<p class="main-notification-text mb-0">You have 1 unread notification<a class="badge badge-pill badge-success ml-2 float-right" href="#">Mark All Read</a></p>
					</div>
					<div class="main-notification-list Notification-scroll">
						<a class="d-flex pl-3 pr-3  border-bottom" href="#">
							<div class="media new">
								<div class="main-img-user online"><img alt="avatar" src="<?php echo base_url() ?>assets/img/faces/5.jpg"></div>
								<div class="media-body">
									<p>Congratulate <strong> Olivia James </strong> for New template start</p><span>Oct 15 12:32pm</span>
								</div>
							</div>
						</a>
						<a class="d-flex pl-3 pr-3  border-bottom" href="#">
							<div class="media">
								<div class="main-img-user"><img alt="avatar" src="<?php echo base_url() ?>assets/img/faces/13.jpg"></div>
								<div class="media-body">
									<p> <strong> Joshua Gray </strong> New Message Received</p><span>Oct 13 02:56am</span>
								</div>
							</div>
						</a>
						<a class="d-flex pl-3 pr-3  border-bottom" href="#">
							<div class="media">
								<div class="main-img-user online"><img alt="avatar" src="<?php echo base_url() ?>assets/img/faces/17.jpg"></div>
								<div class="media-body">
									<p> <strong> Reuben Lewis </strong> added new schedule realease</p><span>Oct 12 10:40pm</span>
								</div>
							</div>
						</a>
						<a class="d-flex pl-3 pr-3  border-bottom" href="#">
							<div class="media">
								<div class="main-img-user online"><img alt="avatar" src="<?php echo base_url() ?>assets/img/faces/10.jpg"></div>
								<div class="media-body">
									<p><strong>Reanne Carnation</strong> Commented on you post</p><span>Sept 30 05:24pm</span>
								</div>
							</div>
						</a>
						<a class="d-flex pl-3 pr-3  border-bottom" href="#">
							<div class="media">
								<div class="main-img-user online"><img alt="avatar" src="<?php echo base_url() ?>assets/img/faces/4.jpg"></div>
								<div class="media-body">
									<p><strong>Vinny Gret </strong> Shared your post</p><span>Sept 12 9:05am</span>
								</div>
							</div>
						</a>
					</div>
					<div class="dropdown-footer">
						<a href="">VIEW ALL</a>
					</div>
				</div>
			</div><!-- Notification closed -->
			<div class="dropdown main-header-message right-toggle">
				<a class="nav-link menu-icons" data-toggle="sidebar-right" data-target=".sidebar-right">
					<svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<line x1="3" y1="12" x2="21" y2="12"></line>
						<line x1="3" y1="6" x2="21" y2="6"></line>
						<line x1="3" y1="18" x2="21" y2="18"></line>
					</svg>
				</a>
			</div><!-- Main-header-message closed -->
			<div class="dropdown main-profile-menu nav nav-item nav-link">
				<a class="profile-user" href=""><img alt="" src="<?php echo base_url() ?>assets/img/faces/6.jpg"></a>
				<div class="dropdown-menu animated fadeInUp">
					<div class="main-header-profile header-img">
						<h6>Mack Adamia</h6><span>Premium Member</span>
					</div>
					<a class="dropdown-item" href="profile.html"><i class="bx bx-user-circle"></i> My Profile</a>
					<a class="dropdown-item" href="editprofile.html"><i class="bx bxs-edit"></i> Edit Profile</a>
					<a class="dropdown-item" href="chat.html"><i class="bx bx-envelope"></i> Chat</a>
					<a class="dropdown-item" href="account-setting.html"><i class="bx bx-cog"></i> Account Settings</a>
					<a class="dropdown-item" href="signin.html"><i class="bx bx-log-out-circle"></i> Sign Out</a>
				</div>
			</div><!-- Main-profile-menu closed -->
			<button class="navbar-toggler navresponsive-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon bx bx-dots-vertical-rounded"></span>
			</button><!-- Navresponsive closed -->
		</div>
	</div>
</div>
<!-- Main-header closed -->