                       @guest
							<li class=" {{ $class ?? ''}}">
								<a href="{{ route('login')}}"  class="ft-medium text-bold">
									<i class="lni lni-user mr-2"></i>Sign In
								</a>
							</li>

							<!-- <li class=" {{ $class ?? ''}}">
								<a href="{{ route('register')}}" class="ft-medium text-bold">
									Register
								</a>
							</li> -->

						@else

							<li class=" {{ $class ?? ''}}"><a href="#">My Account</a>
									<ul class="nav-dropdown nav-submenu">
										@if(is_admin())
										<li class=" {{ $class ?? ''}}">
										<a href="{{ route('admin.index')}}">
										   <i class="fa fa-link mr-1"></i>Admin Panel
										</a>
										</li>
										@endif
										<li class=" {{ $class ?? ''}}">
										<a href="{{ route('account.profile')}}">
										   <i class="fa fa-user mr-1"></i> My Profile
										</a>
										</li>
										<li class=" {{ $class ?? ''}}">
										<a href="{{ route('account.publications')}}">
										   <i class="fa fa-list mr-1"></i> Our Publications
										</a>
										</li>
										<li class=" {{ $class ?? ''}}">
										<a href="{{ route('account.favourites')}}">
										   <i class="fa fa-star mr-1"></i> My Favourites
										</a>
										</li>
										<li class=" {{ $class ?? ''}}">
										<a href="{{ route('account.publish')}}">
											<i class="lni lni-circle-plus mr-1"></i>Publish a resource
										</a>
										</li>
									</ul>
								</li>

							<li class=" {{ $class ?? ''}}">
								<a href="{{ url('logout')}}" class="ft-medium">
									Logout
								</a>
							</li>
						@endguest