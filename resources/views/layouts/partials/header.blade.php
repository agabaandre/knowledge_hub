	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
	<div class="header">
		<div class="container">
			<nav id="navigation" class="navigation navigation-landscape">
				<div class="nav-header">
					<a class="nav-brand" href="{{ url('/') }}">
						<img src="{{ asset('assets/images/logo.png') }}" class="logo" alt="" style="width:280px;" />
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

						<li><a href="{{ url('/')}}">Home</a></li>
						<li><a href="{{ url('records/search')}}">Search</a></li>
						<li class=""><a href="javascript:void(0);">Data Categories<span class="submenu-indicator"></span></a>
							<ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
							   
							    @can('view_experts')
								 <li><a href="{{ url('experts')}}">Work Force Rosters</a></li>
								@endcan

								@foreach($asset_types as $type)
								<li><a href="{{ url('healthassets')}}?slug={{trim($type->slug)}}">{{$type->type_name}}</a></li>
								@endforeach

							</ul>
						</li>
						<li><a href="{{ url('forums')}}">Forums</a></li>
						<li><a href="{{ url('faqs')}}">FAQs</a></li>
						<li><a href="{{ url('privacy')}}">Privacy</a></li>

					</ul>

					<ul class="nav-menu nav-menu-social align-to-right">
						@guest
							<li>
								<a href="{{ route('login')}}"  class="ft-medium text-bold">
									<i class="lni lni-user mr-2"></i>Sign In
								</a>
							</li>

							<li>
								<a href="{{ route('register')}}" class="ft-medium text-bold">
									Register
								</a>
							</li>

						@else

							<li><a href="#">My Account</a>
									<ul class="nav-dropdown nav-submenu">
										@if(is_admin())
										<li>
										<a href="{{ route('admin.index')}}">
										   <i class="fa fa-link mr-1"></i>Admin Panel
										</a>
										</li>
										@endif
										<li>
										<a href="{{ route('account.profile')}}">
										   <i class="fa fa-user mr-1"></i> My Profile
										</a>
										</li>
										<li>
										<a href="{{ route('account.publications')}}">
										   <i class="fa fa-list mr-1"></i> Our Publications
										</a>
										</li>
										<li>
										<a href="{{ route('account.favourites')}}">
										   <i class="fa fa-star mr-1"></i> My Favourites
										</a>
										</li>
										<li>
										<a href="{{ route('account.publish')}}">
											<i class="lni lni-circle-plus mr-1"></i>Publish a resource
										</a>
										</li>
									</ul>
								</li>

							<li>
								<a href="{{ url('logout')}}" class="ft-medium">
									Logout
								</a>
							</li>
						@endguest

					</ul>
					<div class="align-to-right language-css" style="position:relative; margin-top:22px; margin-right:40px">
					  @include('layouts.partials.language')
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