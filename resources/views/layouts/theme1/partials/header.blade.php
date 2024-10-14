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

                    </ul>

                    <ul class="nav-menu nav-menu-social align-to-right">

                        @include('partials.account.authlinks')

                    </ul>
                </nav>
                <div class="clearfix"></div>
                <!-- Main Navigation / End -->

            </div>
            <!-- Left Side Content / End -->


        </div>
    </div>
    <!-- Header / End -->
</header>
<div class="clearfix"></div>
<!-- Header Container / End -->
