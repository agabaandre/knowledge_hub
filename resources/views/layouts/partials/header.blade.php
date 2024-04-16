	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
<div id="langauge-container">
<div class="row d-flex" style="border-bottom: solid 0.4px var(--text-color-primary); border-top: solid 0.4px grey; box-shadow: 0 2px 0 grey, 0 2px 0 grey; width: 100%;" >
		
				    <div  class="ml-5 col-md-4 d-flex">
					<a class="nav-brand" href="{{ url('/') }}">
						<img src="{{ settings()->logo }}" class="logo" alt="" style="width:120px; height:85px; margin-bottom:-18px;">
					    <div class="slogan fw-bold" style="font-size:14px; text-align:center;"><small>{{ settings()->slogan}}</small></div>
					
					</a>
					</div>
					<div class="ml-5 mt-lg-5 col-md-4 d-flex justify-content-end pt-0" id="language">
							@include('layouts.partials.language')
					</div>

</div>
<div class="header">
		
		<div class="container">
			
			<nav id="navigation" class="navigation navigation-landscape">
				
				<div class="nav-header">
					<a class="nav-brand" href="{{ url('/') }}">
						
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
						<li class="categories "><a href="javascript:void(0);">Categories<span class="submenu-indicator"></span></a>
							<ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
						
								@foreach($data_categories as $category)

									@if(strlen($category->required_permission) > 0)
										
									   @auth
											@can($category->required_permission)
											<li>
											<a href="{{ url($category->url_path)}}?slug={{$category->slug}}">{{$category->category_name}}</a>
											</li>
											@endcan
										@endauth

									@else

									<li>
										<a href="{{ url($category->url_path)}}?slug={{$category->slug}}">{{$category->category_name}}</a>
									</li>

									@endif
								@endforeach


							</ul>
						</li>
						@if(states_enabled())
					
						@endif
						
							<li><a href="{{ url('forums')}}">Forums</a></li>
						@if(states_enabled())
							<li><a href="{{ url('countries')}}">Member States</a></li>
						@else
							<li><a href="{{ url('adminunits')}}">Administrative Units</a></li>
						@endif
							<li><a href="{{ url('faqs')}}">FAQs</a></li>
							<li><a href="{{ url('tools')}}">Tools</a></li>
						
						@include('partials.account.authlinks', ['class' => 'mobileonly'])

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