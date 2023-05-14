	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
	<div id="langauge-container">
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
								@guest
								<a href="{{ route('account.profile')}}"  class="theme-cl fs-lg">
									<i class="lni lni-user"></i>
								</a>
								@else

								<a href="{{ route('login')}}"  class="theme-cl fs-lg">
									<i class="lni lni-user"></i>
								</a>

								@endguest
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

						@include('partials.account.authlinks',['class'=>'mobileonly'])

					</ul>

					<ul class="nav-menu nav-menu-social align-to-right">
						
					@include('partials.account.authlinks')

					</ul>
					<!-- <div class="align-to-right language-css" style="position:relative; margin-top:22px; margin-right:40px">
					  @include('layouts.partials.language')
					</div> -->
				</div>
			</nav>
		</div>
	</div>
	<!-- End Navigation -->
	<div class="clearfix"></div>
	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->