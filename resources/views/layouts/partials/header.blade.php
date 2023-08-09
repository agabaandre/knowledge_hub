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
						<li class="categories"><a href="javascript:void(0);">Categories<span class="submenu-indicator"></span></a>
							<ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
							   
							   

								@foreach($data_categories as $category)

									@if(!empty($category->required_permission))
										@can($category->required_permission)
										<li>
										 <a href="{{ url($category->url_path)}}?slug={{$category->slug}}">{{$category->category_name}}</a>
										</li>
										@endcan
									@else

									<li>
										<a href="{{ url($category->url_path)}}?slug={{$category->slug}}">{{$category->category_name}}</a>
									</li>

									@endif
								@endforeach

							</ul>
						</li>

						<li class="dashboards"><a href="javascript:void(0);">Dashboards<span class="submenu-indicator"></span></a>
							<ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
							<li>
								<a href="{{ url('dashboards')}}">RCCs</a>
							</li>
							<!-- <li>
								<a href="{{ url('dashboards/kpi')}}">Indicator Comparison</a>
							</li> -->
							</ul>
						</li>
						
						<li><a href="{{ url('forums')}}">Forums</a></li>
						<li><a href="{{ url('countries')}}">Member States</a></li>
						<li><a href="{{ url('faqs')}}">Need Help?</a></li>
						
						@include('partials.account.authlinks',['class'=>'mobileonly'])

					</ul>

					<ul class="nav-menu nav-menu-social align-to-right">
						
					@include('partials.account.authlinks')

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