<!-- Top header -->
<style>

/* Mega Menu Styling for mega Items */
.menu-container{
    min-width: 95%;
    max-width: 95%;
    display: flex;
    justify-content: center;
}
.mega-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: none;
    z-index: 10000;
    border-top: 3px solid #00a651;
}

.mega-menu-container {
    width: 100%;
    min-width: 100%;
    max-width: 100%;
    margin: 0 auto;
    padding: 30px 20px;
}

.mega-menu-grid {
    display: flex;
}

.mega-menu-sidebar {
    width: 220px;
    padding-right: 20px;
    border-right: 1px solid #f0f0f0;
}

.mega-menu-sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.mega-menu-sidebar ul li {
    margin-bottom: 10px;
}

.mega-menu-sidebar ul li a {
    display: block;
    padding: 8px 15px;
    color: #333;
    text-decoration: none;
    font-size: 14px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.mega-menu-sidebar ul li a:hover,
.mega-menu-sidebar ul li a.active {
    background-color: #f5f5f5;
    color: #00a651;
}

.mega-menu-content {
    flex: 1;
    padding-left: 30px;
}

.mega-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}


.mega-item {
    flex: 1 1 calc(25% - 20px); /* 4 items per row minus gap compensation */
    box-sizing: border-box;
    min-width: 200px; /* prevents items from getting too small */
    background-color: #fff;
    border-radius: 6px;
    overflow: hidden;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.mega-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.mega-image {
    height: 160px;
    overflow: hidden;
}

.mega-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.5s ease;
}

.mega-item:hover .mega-image img {
    transform: scale(1.05);
}

.mega-info {
    padding: 15px;
}

.mega-title {
    font-size: 14px;
    font-weight: 600;
    margin: 0 0 10px;
    color: #00a651;
    line-height: 1.4;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.mega-meta {
    display: flex;
    font-size: 12px;
    color: #777;
}

.mega-date {
    margin-right: 15px;
}

.mega-comments {
    margin-right: 15px;
}

.mega-comments:before {
    content: '💬 ';
}

.mega-views:before {
    content: '👁️ ';
}

/* Show mega menu on hover */
.has-mega-menu:hover .mega-menu {
    display: block;
    animation: fadeInDown 0.3s ease;
}


.nav-menu > li:hover .nav-dropdown {
    display: block;
    animation: fadeInDown 0.3s ease;
}

.has-mega-menu .submenu-indicator {
    margin-left: 6px;
    display: inline-block;
    transition: all 0.3s;
}


.has-mega-menu .submenu-indicator:after {
    content: '';
    display: inline-block;
    width: 6px;
    height: 6px;
    border-right: 2px solid #333;
    border-bottom: 2px solid #333;
    transform: rotate(45deg);
    position: relative;
    top: -10px!important;
}


@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Social menu */
.nav-menu-social {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
}

.nav-menu-social li {
    margin-left: 5px;
}

.nav-menu-social li a {
    display: block;
    padding: 20px 10px;
    color: #333;
    text-decoration: none;
}

.search-btn {
    font-size: 18px;
}

/* Mobile styles */
.nav-toggle {
    display: none;
    cursor: pointer;
    font-size: 24px;
}

.mobile_nav {
    display: none;
}

@media (max-width: 1200px) {
    .mega-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 1024px) {
    .mega-item {
        flex: 1 1 calc(33.333% - 20px);
    }
}

@media (max-width: 768px) {
    .mega-item {
        flex: 1 1 calc(50% - 20px);
    }
}

@media (max-width: 480px) {
    .mega-item {
        flex: 1 1 100%;
    }
}

/*
@media (max-width: 992px) {
    .nav-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 15px 0;
    }
    
    .nav-toggle {
        display: block;
    }
    
    .mobile_nav {
        display: block;
    }
    
    .nav-menus-wrapper {
        position: fixed;
        top: 60px;
        left: 0;
        width: 100%;
        height: calc(100vh - 60px);
        background-color: #fff;
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
        overflow-y: auto;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
    }
    
    .nav-menus-wrapper.active {
        transform: translateX(0);
    }
    
    .nav-menu {
        flex-direction: column;
        width: 100%;
    }
    
    .nav-dropdown {
        position: static;
        box-shadow: none;
        width: 100%;
        border-top: none;
        padding-left: 20px;
    }
    
    .mega-menu {
        position: static;
        box-shadow: none;
        width: 100%;
        border-top: none;
    }
    
    .mega-menu-grid {
        flex-direction: column;
    }
    
    .mega-menu-sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    
    .mega-menu-content {
        padding-left: 0;
    }
    
    .mega-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .nav-menu-social {
        margin-top: 20px;
        width: 100%;
        justify-content: flex-start;
    }
}

@media (max-width: 576px) {
    .mega-grid {
        grid-template-columns: 1fr;
    }
}*/
</style>

<!-- Top header -->
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
                <div class="col-lg-5 col-md-5 text-center ">
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

<!-- Main Navigation -->
<div class="header modern-header justify-content-center" style="display: flex; justify-content: center;" >
    <div class="menu-container">
        <nav id="navigation" class="navigation navigation-landscape">
            <div class="nav-header">
                <a class="nav-brand" href="{{ url('/') }}"></a>
                <div class="nav-toggle"></div>
                <div class="mobile_nav">
                    <ul>
                        <li>
                            @guest
                                <a href="{{ route('account.profile') }}" class="theme-cl fs-lg">
                                    <i class="lni lni-user"></i>
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="theme-cl fs-lg">
                                    <i class="lni lni-user"></i>
                                </a>
                            @endguest
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="nav-menus-wrapper" style="transition-property: none;">
                <ul class="nav-menu">
                    <li class="active"><a href="{{ url('/') }}">Home</a></li>
                    
                     <li class="categories "><a href="javascript:void(0);">Data Categories<span
                                class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu" style="right: auto; display: none;">

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
                        <li><a href="{{ url('countries') }}">Member States</a></li>
                    @else
                        <li><a href="{{ url('adminunits') }}">Administrative Units</a></li>
                    @endif
                    
                    <li class="categories {{(count($health_emergencies)>0)?'has-mega-menu':''}}">
                        <a href="javascript:void(0);">Health Emergencies <span class="submenu-indicator"></span></a>

                        @include('layouts.partials.tags_menu')
                        
                    </li>
                    
                     <!--  <li class="categories">
                        <a href="javascript:void(0);">Scientific Publications<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                             <li><a href="#">Registered Protocols</a></li>
                            <li><a href="#">Scientific Articles</a></li>
                        </ul>
                    </li>

                     <li class="categories">
                        <a href="javascript:void(0);">Data & Analytics<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                             <li><a href="#">Surveillance Data</a></li>
                            <li><a href="#">R&D Data</a></li>
                            <li><a href="#">Surveys</a></li>
                            <li><a href="#">Assessments</a></li>
                        </ul>
                    </li> -->


                     <li class="categories">
                        <a href="javascript:void(0);">Guidelines & Frameworks<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                             <li><a href="#">Africa CDC statute</a></li>
                            <li><a href="#">AU declarartions</a></li>
                            <li><a href="#">Statements</a></li>
                            <li><a href="#">MS guidelines</a></li>
                           <li><a href="{{ url('tools') }}">Tools</a></li>
                        </ul>
                    </li>

               
                    <li class="categories">
                        <a href="javascript:void(0);">Discussions<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                             <li><a href="{{ url('forums') }}">Forums</a></li>
                            <li><a href="{{ url('communities') }}">Communities</a></li>
                            <li><a href="#">Press Releases</a></li>
                        </ul>
                    </li>


                    <li class="categories">
                        <a href="javascript:void(0);">Learning<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="{{ url('courses') }}">Courses</a></li>  
                        </ul>
                    </li>

                    <li class="categories">
                        <a href="javascript:void(0);">Help<span class="submenu-indicator"></span></a>
                        <ul class="nav-dropdown nav-submenu">
                            <li><a href="{{ url('faqs') }}">FAQs</a></li> 
                            <li><a href="{{ url('publications/request-content') }}">Content Request</a></li>
                        </ul>
                    </li>

                    @include('partials.account.authlinks', ['class' => 'mobileonly'])
                    
                    
                </ul>

                <ul class="nav-menu nav-menu-social align-to-right">
                    @include('partials.account.authlinks')
                </ul>
            </div>
        </nav>
    </div>
</div>
<div class="clearfix"></div>