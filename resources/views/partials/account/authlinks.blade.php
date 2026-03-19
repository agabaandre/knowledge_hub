                       @guest
                           <li class=" {{ $class ?? '' }} notranslate">
                               <a href="{{ route('login') }}" class="ft-medium text-bold">
                                   <i class="fa fa-user mr-2"></i>{{ __('ui_body.account') }}
                               </a>
                           </li>

                           <!-- <li class=" {{ $class ?? '' }}">
                <a href="{{ route('register') }}" class="ft-medium text-bold">
                 Register
                </a>
               </li> -->
                       @else
                           <li class="{{ $class ?? '' }} notranslate"><a href="#">
                                       @if(!empty(current_user()->photo))
                                           <img class="rounded-circle notranslate user-avatar-img"
                                               src="{{ current_user()->photo }}"
                                               style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid #7e7d80; display: inline-block;"
                                               onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                           <span class="user-avatar-fallback" style="display:none; width: 30px; height: 30px; border-radius: 50%; border: 1px solid #7e7d80; background-color: #e2e8f0; align-items: center; justify-content: center; color: #718096; font-size: 14px;"><i class="fa fa-user"></i></span>
                                       @else
                                           <span class="user-avatar-fallback" style="display:inline-flex; width: 30px; height: 30px; border-radius: 50%; border: 1px solid #7e7d80; background-color: #e2e8f0; align-items: center; justify-content: center; color: #718096; font-size: 14px;"><i class="fa fa-user"></i></span>
                                       @endif
                                       {{ ' ' . ucwords(current_user()->name) }}</a>
                               <ul class="nav-dropdown">
                                   @if (is_admin())
                                       <li class=" {{ $class ?? '' }}">
                                           <a href="{{ route('admin.index') }}">
                                               <i class="fa fa-th-large mr-1"></i>{{ __('ui_body.admin_panel') }}
                                           </a>
                                       </li>
                                   @endif
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.profile') }}">
                                           <i class="fa fa-user mr-1"></i> {{ __('ui_body.my_profile') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publications') }}">
                                           <i class="fa fa-list mr-1"></i> {{ __('ui_body.publications') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.my-forums') }}">
                                           <i class="fa fa-comments mr-1"></i> {{ __('ui_body.my_forums') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.my-communities') }}">
                                           <i class="fa fa-users mr-1"></i> {{ __('ui_body.my_communities') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.favourites') }}">
                                           <i class="fa fa-star mr-1"></i> {{ __('ui_body.my_favourites') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.chats') }}">
                                           <i class="fa fa-comments mr-1"></i> {{ __('ui_body.my_chats') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publish') }}">
                                           <i class="fa fa-plus mr-1"></i>{{ __('ui_body.publish_a_resource') }}
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ url('logout') }}">
                                           <i class="fa fa-sign-out-alt"></i> {{ __('ui_body.log_out') }}
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
