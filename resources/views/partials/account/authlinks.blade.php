                       @guest
                           <li class=" {{ $class ?? '' }}">
                               <a href="{{ route('login') }}" class="ft-medium text-bold">
                                   <i class="lni lni-user mr-2"></i>Sign In
                               </a>
                           </li>

                           <!-- <li class=" {{ $class ?? '' }}">
                <a href="{{ route('register') }}" class="ft-medium text-bold">
                 Register
                </a>
               </li> -->
                       @else
                           <li class="{{ $class ?? '' }}"><a href="#"><img class="rounded-circle notranslate"
                                       src="{{ current_user()->photo }}"
                                       style=" width: 25px; height: 25px; border-radius: 50%;  border: 1px solid #7e7d80;">{{ ' ' . ucwords(current_user()->name) }}</a>
                               <ul class="nav-dropdown">
                                   @if (is_admin())
                                       <li class=" {{ $class ?? '' }}">
                                           <a href="{{ route('admin.index') }}">
                                               <i class="fa fa-link mr-1"></i>Admin Panel
                                           </a>
                                       </li>
                                   @endif
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.profile') }}">
                                           <i class="fa fa-user mr-1"></i> My Profile
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publications') }}">
                                           <i class="fa fa-list mr-1"></i> Our Publications
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.favourites') }}">
                                           <i class="fa fa-star mr-1"></i> My Favourites
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publish') }}">
                                           <i class="lni lni-circle-plus mr-1"></i>Publish a resource
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ url('logout') }}">
                                           <i class="fa fa-circle-arrow-right"></i>Log Out
                                       </a>
                                   </li>
                               </ul>
                           </li>

                           <!-- <li class=" {{ $class ?? '' }}">
                <a href="{{ url('logout') }}" class="ft-medium">
                 Logout
                </a>
               </li> -->
                       @endguest
