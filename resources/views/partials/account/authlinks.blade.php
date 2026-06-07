                       @guest
                           <li class="{{ $class ?? '' }} notranslate">
                               <a href="{{ route('login') }}" class="ft-medium text-bold">
                                   <span class="kh-nav-item-stack">
                                       <i class="fas fa-user kh-nav-icon" aria-hidden="true"></i>
                                       <span class="kh-nav-label khub-i18n-text" data-khub-i18n="ui_body.account">{{ __('ui_body.account') }}</span>
                                   </span>
                               </a>
                           </li>

                           <!-- <li class=" {{ $class ?? '' }}">
                <a href="{{ route('register') }}" class="ft-medium text-bold">
                 Register
                </a>
               </li> -->
                       @else
                           <li class="{{ $class ?? '' }} notranslate">
                               <a href="#">
                                   @if(!empty(current_user()->photo))
                                       <img class="rounded-circle notranslate user-avatar-img"
                                           src="{{ current_user()->photo }}"
                                           alt=""
                                           onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                       <span class="user-avatar-fallback" style="display:none; width: 28px; height: 28px; border-radius: 50%; border: 1px solid #cbd5e1; background-color: #e2e8f0; align-items: center; justify-content: center; color: #718096; font-size: 14px;"><i class="fas fa-user"></i></span>
                                   @else
                                       <span class="user-avatar-fallback" style="display:inline-flex; width: 28px; height: 28px; border-radius: 50%; border: 1px solid #cbd5e1; background-color: #e2e8f0; align-items: center; justify-content: center; color: #718096; font-size: 14px;"><i class="fas fa-user"></i></span>
                                   @endif
                                   <span class="kh-nav-label">{{ ucwords(current_user()->name) }}</span>
                               </a>
                               <ul class="nav-dropdown nav-submenu">
                                   @if (is_admin())
                                       <li class=" {{ $class ?? '' }}">
                                           <a href="{{ route('admin.index') }}">
                                               <i class="fa fa-th-large mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.admin_panel">{{ __('ui_body.admin_panel') }}</span>
                                           </a>
                                       </li>
                                   @endif
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.profile') }}">
                                           <i class="fa fa-user mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.my_profile">{{ __('ui_body.my_profile') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publications') }}">
                                           <i class="fa fa-list mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.publications">{{ __('ui_body.publications') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.my-forums') }}">
                                           <i class="fa fa-comments mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.my_forums">{{ __('ui_body.my_forums') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.my-communities') }}">
                                           <i class="fa fa-users mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.my_communities">{{ __('ui_body.my_communities') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.favourites') }}">
                                           <i class="fa fa-star mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.my_favourites">{{ __('ui_body.my_favourites') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.chats') }}">
                                           <i class="fa fa-comments mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.my_chats">{{ __('ui_body.my_chats') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ route('account.publish') }}">
                                           <i class="fa fa-plus mr-1"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.publish_a_resource">{{ __('ui_body.publish_a_resource') }}</span>
                                       </a>
                                   </li>
                                   <li class=" {{ $class ?? '' }}">
                                       <a href="{{ url('logout') }}">
                                           <i class="fa fa-sign-out-alt"></i><span class="khub-i18n-text" data-khub-i18n="ui_body.log_out">{{ __('ui_body.log_out') }}</span>
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
