	<!-- ============================================================== -->
	<!-- Top header  -->
	<!-- ============================================================== -->
	<!-- Start Navigation -->
<div id="langauge-container">
<div class="header">
	<div class="header-top text-white">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="ht-left d-flex">
                    <div class="mail-service mr-2">
                       
                    </div>
                    <div class="phone-service">
                       
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ht-right">
                   
                 
                    <div class="top-social" style="color:;">

                      
						
						
						
						Langauge:	@include('layouts.partials.language')
					
						
                    </div>
                </div>
            </div>
        </div>
    </div>
	</div>
	
			<div class="row mr-5 " style="max-height:200px; border-bottom:solid 1px var(--theme-color-primary) ; width:100%;  " >
			<div class="col-md-12 row" >
				    <div id="logo" class="ml-5 mb-0 col-md-12">
					<a class="nav-brand" href="{{ url('/') }}">
						<img src="{{ settings()->logo }}" class="logo" alt="" style="width:220px; margin-bottom:-25px;" />
					</a>
					<p class="slogan mt-2 fw-bold"><small>Unveiling all the hidden information across Africa</small></p>
					
					</div>
				

			</div>
		</div>

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
						<li class="categories"><a href="javascript:void(0);">Categories<span class="submenu-indicator"></span></a>
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
						<!-- <li class="dashboards"><a href="javascript:void(0);">Dashboards<span class="submenu-indicator"></span></a>
							<ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">
							<li>
								<a href="{{ url('dashboards')}}">RCCs</a>
							</li>
							@foreach($dashboard_categories as $cat)

							@if(strlen($cat->required_permission) > 0)
								
							@auth
									@can($cat->required_permission)
									<li>
									<a href="{{ url($cat->url_path)}}?slug={{$cat->slug}}">{{$cat->category_name}}</a>
									</li>
									@endcan
								@endauth

							@else

							<li>
								<a href="{{ url($cat->url_path)}}?slug={{$cat->slug}}">{{$cat->category_name}}</a>
							</li>

							@endif
							@endforeach
							
							</ul>
						</li> -->
						@endif
						
							<li><a href="{{ url('forums')}}">Forums</a></li>
						@if(states_enabled())
							<li><a href="{{ url('countries')}}">Member States</a></li>
						@else
							<li><a href="{{ url('adminunits')}}">Administrative Units</a></li>
						@endif
							<li><a href="{{ url('faqs')}}">FAQs</a></li>
						
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