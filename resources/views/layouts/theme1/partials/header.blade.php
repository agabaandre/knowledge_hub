<!-- Header Container
================================================== -->
<header id="header-container" class="fullwidth bg-white">

    <div id="langauge-container">
        <div class="p-3">
            <div class="container" style="min-width: 90%;">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-2 d-none d-md-block">
                        <div><a class="nav-brand" href="{{ url('/') }}">
                                <img src="{{ settings()->logo }}" class="logo" alt=""
                                    style="height:85px; margin-bottom:-16px;">
                            </a>
                        </div>
                        <div class="mt-1 text-secondary">
                            <!-- slogan -->
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 text-center ">
                        <h3 style="color:black !important; font-weight:bold; margin-bottom: 7px;" class="notranslate">
                            {{ settings()->site_name }}</h3>
                        <h6 class="slogan fw-bold" style="font-size: 14px; margin-bottom: 7px; margin-left: 20px;">
                            {{ settings()->slogan }}</h6>
                    </div>
                    <div class="col-lg-4 col-md-4 text-end d-none d-md-block justify-content-end">
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
                        <li><a href="#">Categories</a>
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


                            </ul>
                        </li>
                        @if (states_enabled())
                        @endif

                        <li><a href="{{ url('forums') }}">Forums</a></li>
                        @if (states_enabled())
                            <li><a href="{{ url('countries') }}">Member States</a></li>
                        @else
                            <li><a href="{{ url('adminunits') }}">Administrative Units</a></li>
                        @endif
                        <li><a href="{{ url('faqs') }}">FAQs</a></li>
                        <li><a href="{{ url('tools') }}">Tools</a></li>
                        @include('partials.account.authlinks', ['class' => 'mobileonly'])

                        <li><a href="{{ url('courses') }}">Courses</a></li>
                        <li><a href="{{ url('communities') }}">Communities</a></li>
                        <li><a href="{{ url('publications/request-content') }}">Content Request</a></li>
                        @if (!auth()->check())
                            <li><a href="{{ url('/login') }}">Login</a></li>
                        @endauth

                </ul>

            </nav>
            <div class="clearfix"></div>
            <!-- Main Navigation / End -->

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
                                <div class="user-avatar status-online">
                                    <img src="{{ current_user()->photo }}" alt="">
                                </div>
                            </a>
                        </div>

                        <!-- Dropdown -->
                        <div class="header-notifications-dropdown">

                            <!-- User Status -->
                            <div class="user-status">

                                <!-- User Name / Avatar -->
                                <div class="user-details">
                                    <div class="user-avatar status-online"><img src="{{ current_user()->photo }}"
                                            alt=""></div>
                                    <div class="user-name">
                                        {{ ' ' . ucwords(current_user()->name) }}
                                    </div>
                                    <div class="user-role">
                                        {{ current_user()->role }}
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
                                        Our Publications</a></li>
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

        </div>

        <!-- Right Side Content / End -->


    </div>
</div>
<!-- Header / End -->
</header>
<!-- Header Container / End -->
