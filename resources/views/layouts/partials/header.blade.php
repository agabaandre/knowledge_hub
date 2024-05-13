	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
<div id="langauge-container" style="margin-bottom:-4px">
<div class="p-3 bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-3 col-md-3 d-none d-md-block">
        <div><a class="nav-brand" href="{{ url('/') }}">
                <img src="{{ settings()->logo }}" class="logo" alt="" style="height:85px; margin-bottom:-16px;">
            </a>
		</div>
          <div class="mt-1 text-secondary">
			<!-- slogan -->
          </div>
        </div>
        <div class="col-lg-6 col-md-6 text-center">
          <h3 style="color:black !important; font-weight:bold; margin-bottom: 7px;">{{ settings()->site_name }}</h3>
          <h6 class="slogan fw-bold" style="font-size: 14px; margin-bottom: 7px; margin-left: 20px;">{{ settings()->slogan }}</h6>
        </div>
        <div class="col-lg-3 col-md-3 text-end d-none d-md-block justify-content-end">
		  @include('layouts.partials.langselect')
		</div>
        </div>
      </div>
    </div>
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