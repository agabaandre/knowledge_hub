<!-- Header Container
================================================== -->
<header id="header-container" class="fullwidth bg-white">

    <div id="langauge-container" style="margin-bottom:-4px">
        <div class="p-3 bg-light">
            <div class="container" style="min-width: 90%;">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-3 d-none d-md-block">
                        <div><a class="nav-brand" href="{{ url('/') }}">
                                <img src="{{ settings()->logo }}" class="logo" alt=""
                                    style="height:85px; margin-bottom:-16px;">
                            </a>
                        </div>
                        <div class="mt-1 text-secondary">
                            <!-- slogan -->
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-5 col-12 text-center">
                        <h3 style="color:black !important; font-weight:bold; margin-bottom: 7px;" class="notranslate">
                            {{ settings()->site_name }}</h3>
                        <h6 class="slogan fw-bold" style="font-size: 14px; margin-bottom: 7px; margin-left: 0;">
                            {{ settings()->slogan }}</h6>
                    </div>
                    <div class="col-lg-4 col-md-4 d-none d-md-block" style="padding-right: 10px; display: flex; align-items: center; justify-content: flex-end;">
                        @include('layouts.partials.langselect')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div id="header">
        <div class="container">

            <!-- Left Side Content -->
            <div class="left-side">


                <!-- Main Navigation -->
                <nav id="navigation">

                    <ul id="responsive">

                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="#">Browse</a>
                            <ul class="dropdown-nav">

                                @foreach ($data_categories as $category)
                                    @if ($category->is_special)
                                        @auth
                                            <li>
                                                <a
                                                    href="{{ url($category->url_path) }}?slug={{ $category->slug }}">{{ $category->category_name }}</a>
                                            </li>
                                        @endauth
                                    @else
                                        @if (strlen($category->required_permission) > 0)
                                            @auth
                                                @can($category->required_permission)
                                                    <li>
                                                        <a
                                                            href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a>
                                                    </li>
                                                @endcan
                                            @endauth
                                        @else
                                            <li>
                                                <a
                                                    href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach

                                @if (states_enabled())
                                    <li><a href="{{ url('countries') }}">Member States</a></li>
                                @else
                                    <li><a href="{{ url('adminunits') }}">Administrative Units</a></li>
                                @endif

                            </ul>
                        </li>

                        @php $menuIconsEnabled = settings()->menu_icons_enabled ?? 0; @endphp
                        
                        @php
                            // Use cached counts helper for better performance
                            $userId = auth()->check() ? auth()->id() : null;
                            $counts = get_menu_counts($userId);
                            
                            $totalForums = $counts['forums'];
                            $totalCommunities = $counts['communities'];
                            $discussionsBadgeCount = $counts['total'];
                            $forumsBadgeCount = $counts['forums'];
                            $communitiesBadgeCount = $counts['communities'];
                        @endphp
                        
                        <li class="{{ (request()->is('forums*') || request()->is('communities*')) ? 'active' : '' }}">
                            <a href="javascript:void(0);">
                                @if($menuIconsEnabled)<i class="fa fa-comments"></i> @endif 
                                Discussions
                                @if($discussionsBadgeCount > 0)
                                    <span class="menu-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; margin-left: 4px;">{{ $discussionsBadgeCount }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-nav">
                                <li>
                                    <a href="{{ url('forums') }}">
                                        Forums
                                        @if($forumsBadgeCount > 0)
                                            <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; float: right;">{{ $forumsBadgeCount }}</span>
                                        @endif
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('communities') }}">
                                        Communities
                                        @if($communitiesBadgeCount > 0)
                                            <span class="menu-item-badge" style="background: #ef4444; color: white; border-radius: 10px; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; float: right;">{{ $communitiesBadgeCount }}</span>
                                        @endif
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li><a href="#">Learning</a>
                            <ul class="dropdown-nav">
                                <li><a href="{{ url('courses') }}">Courses</a></li>
                            </ul>
                        </li>

                        <li><a href="#">Support</a>
                            <ul class="dropdown-nav">
                                <li><a href="{{ url('faqs') }}">FAQs</a></li>
                                <li><a href="{{ url('publications/request-content') }}">Content Request</a></li>
                            </ul>
                        </li>

                        @auth
                        <li><a href="javascript:void(0);">Create</a>
                            <ul class="dropdown-nav">
                                <li><a href="{{ route('forums.create') }}">Forum Discussion</a></li>
                                <li><a href="{{ route('account.publish') }}">Resource Publication</a></li>
                            </ul>
                        </li>
                        @endauth
                        
                        @include('partials.account.authlinks', ['class' => 'mobileonly'])
                        
                        @if (!auth()->check())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                        @endauth

                </ul>
            </nav>

        </div>
        <!-- Left Side Content / End -->

        <!-- Right Side Content / End -->

        <div class="right-side">

            @if (auth()->check())
                <!-- User Menu -->
                <div class="header-widget">

                    <!-- Messages -->
                    <div class="header-notifications user-menu">
                        <div class="header-notifications-trigger">
                            <a href="#">
                                <div class="user-avatar status-online" style="width: 40px; height: 40px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #e2e8f0;">
                                    @if(!empty(current_user()->photo))
                                        <img src="{{ current_user()->photo }}" alt="" class="user-avatar-img" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="user-avatar-fallback" style="display:none; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                                    @else
                                        <span class="user-avatar-fallback" style="display:flex; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                                    @endif
                                </div>
                            </a>
                        </div>

                        <!-- Dropdown -->
                        <div class="header-notifications-dropdown">

                            <!-- User Status -->
                            <div class="user-status">

                                <!-- User Name / Avatar -->
                                <div class="user-details" style="display: flex; flex-direction: column; align-items: center; padding: 15px;">
                                    <div class="user-avatar status-online" style="width: 60px; height: 60px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background-color: #e2e8f0; margin-bottom: 10px;">
                                        @if(!empty(current_user()->photo))
                                            <img src="{{ current_user()->photo }}" alt="" class="user-avatar-img" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span class="user-avatar-fallback" style="display:none; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                                        @else
                                            <span class="user-avatar-fallback" style="display:flex; width: 100%; height: 100%; align-items: center; justify-content: center; background-color: #e2e8f0; color: #718096; border-radius: 50%;"><i class="fa fa-user"></i></span>
                                        @endif
                                    </div>
                                    <div class="user-name" style="font-weight: 600; font-size: 1rem; color: #2d3748; margin-bottom: 5px;">
                                        {{ ucwords(current_user()->name) }}
                                    </div>
                                    <div class="user-role" style="font-size: 0.875rem; color: #718096;">
                                        {{ current_user()->role ?? 'User' }}
                                    </div>
                                </div>
                            </div>

                            <ul class="user-menu-small-nav">
                                <li><a href="{{ route('admin.index') }}"><i
                                            class="icon-material-outline-dashboard"></i>
                                        Admin Panel</a></li>
                                <li><a href="{{ route('account.profile') }}"><i
                                            class="icon-material-outline-settings"></i>
                                        My Profile</a></li>
                                <li><a href="{{ route('account.publications') }}"><i
                                            class="icon-material-outline-assignment"></i>
                                        Publications</a></li>
                                <li><a href="{{ route('account.my-forums') }}"><i
                                            class="icon-material-outline-forum"></i>
                                        My Forums</a></li>
                                <li><a href="{{ route('account.my-communities') }}"><i
                                            class="icon-material-outline-group"></i>
                                        My Communities</a></li>
                                <li><a href="{{ route('account.favourites') }}"><i
                                            class="icon-material-outline-favorite-border"></i>
                                        My Favourites</a></li>
                                <li><a href="{{ route('account.publish') }}"><i
                                            class="icon-material-outline-add-circle-outline"></i>
                                        Publish a resource</a></li>
                                <li><a href="{{ route('logout') }}"><i
                                            class="icon-material-outline-power-settings-new"></i> Logout</a></li>
                            </ul>

                        </div>
                    </div>

                </div>
                <!-- User Menu / End -->
            @endif

            <!-- Mobile Navigation Button -->
            <span class="mmenu-trigger">
                <button class="hamburger hamburger--collapse" type="button">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </span>
            
            {{-- Mobile Language Selector (phones only, not tablets) --}}
            <div class="mobile-language-selector d-block d-md-none" style="position: absolute; right: 60px; top: 50%; transform: translateY(-50%); z-index: 100;">
                @include('layouts.partials.langselect')
            </div>

        </div>

        <!-- Right Side Content / End -->


    </div>
</div>
<!-- Header / End -->
</header>
<!-- Header Container / End -->
