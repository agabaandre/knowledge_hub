@php
    $navStyle = settings()->nav_style ?? 'colored';
    $logoScale = (int)(settings()->logo_scale ?? 80);
    $logoPx = in_array($logoScale, [40,50,60,70,80,100,120]) ? $logoScale : 80;
@endphp
<header id="header" class="header sticky-top border-bottom {{ $navStyle === 'light' ? 'nav-light' : 'nav-colored' }}">
    <div class="container">
        <nav class="p-0 navbar navbar-expand-lg navbar-light">
            <a href="{{ url('/') }}" class="navbar-brand p-0 m-0 d-flex align-items-center">
                @if(settings()->logo ?? null)
                    <img src="{{ settings()->logo }}" alt="{{ settings()->site_name }}" class="{{ (settings()->header_logo_inverse ?? false) ? 'logo-inverse' : '' }}" style="max-height: {{ $logoPx }}px; width: auto;">
                @else
                    <span class="fw-semibold text-body">{{ Str::limit(settings()->site_name ?? 'Knowledge Hub', 24) }}</span>
                @endif
            </a>

            <div class="d-flex gap-2 align-items-center ms-auto me-3 order-lg-3">
                <div class="d-none d-md-block">@include('layouts.partials.langselect')</div>
                @auth
                    <div class="dropdown">
                        <button class="btn btn-icon btn-sm btn-light rounded-circle p-0 overflow-hidden user-avatar-btn" type="button" data-bs-toggle="dropdown" aria-label="Account" style="width:36px;height:36px;">
                            @if(!empty(current_user()->photo))
                                <img src="{{ current_user()->photo }}" alt="" class="rounded-circle w-100 h-100" style="object-fit:cover;" onerror="this.style.display='none';var s=this.nextElementSibling;if(s){s.classList.remove('d-none');s.classList.add('d-inline-flex','user-avatar-placeholder-show');s.style.display='inline-flex';}">
                                <span class="user-avatar-placeholder d-none align-items-center justify-content-center rounded-circle w-100 h-100" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:0.9rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                            @else
                                <span class="user-avatar-placeholder d-inline-flex align-items-center justify-content-center rounded-circle w-100 h-100" style="background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;font-size:0.9rem;"><i class="fa fa-user" aria-hidden="true"></i></span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="px-3 py-2 border-bottom">
                                <h6 class="mb-0">{{ ucwords(current_user()->name) }}</h6>
                                <small class="text-body-secondary">{{ current_user()->email ?? '' }}</small>
                            </div>
                            @if (is_admin())
                            <a href="{{ route('admin.index') }}" class="dropdown-item"><i class="fa fa-th-large me-2"></i> Admin Panel</a>
                            @endif
                            <a href="{{ route('account.profile') }}" class="dropdown-item"><i class="fa fa-user me-2"></i> My Profile</a>
                            <a href="{{ route('account.publications') }}" class="dropdown-item"><i class="fa fa-list me-2"></i> Publications</a>
                            <a href="{{ route('account.my-forums') }}" class="dropdown-item"><i class="fa fa-comments me-2"></i> My Forums</a>
                            <a href="{{ route('account.my-communities') }}" class="dropdown-item"><i class="fa fa-users me-2"></i> My Communities</a>
                            <a href="{{ route('account.favourites') }}" class="dropdown-item"><i class="fa fa-star me-2"></i> My Favourites</a>
                            <a href="{{ route('account.chats') }}" class="dropdown-item"><i class="fa fa-comments me-2"></i> My Chats</a>
                            <a href="{{ route('account.publish') }}" class="dropdown-item"><i class="fa fa-plus me-2"></i> Publish resource</a>
                            <hr class="dropdown-divider">
                            <a href="{{ url('logout') }}" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
                        </div>
                    </div>
                @else
                    <a href="{{ url('login') }}" class="btn btn-outline-primary btn-sm d-none d-md-inline-block">Log in</a>
                    <a href="{{ url('register') }}" class="btn btn-primary btn-sm">Sign up</a>
                @endauth
                <button class="navbar-toggler border-0 py-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#theme1Navbar" aria-label="Menu">
                    <i class="fa fa-bars fs-5"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse order-lg-2" id="theme1Navbar">
                <ul class="navbar-nav nav-underline gap-1 mx-auto">
                    <li class="nav-item"><a class="nav-link {{ request()->is('/') && !request()->is('records*') && !request()->is('forums*') && !request()->is('communities*') ? 'active' : '' }}" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ (request()->is('records*') && !request()->has('tag')) || request()->is('health-topics*') || request()->is('countries*') || request()->is('adminunits*') || request()->is('categories/*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Browse</a>
                        <ul class="dropdown-menu dropdown-menu-lg">
                            <li><a class="dropdown-item" href="{{ url('/health-topics') }}">Health Topics</a></li>
                            @foreach ($data_categories ?? [] as $category)
                                @if($category->is_special ?? false)
                                    @auth
                                        <li><a class="dropdown-item" href="{{ url($category->url_path ?? '') }}?slug={{ $category->slug }}">{{ $category->category_name }}</a></li>
                                    @endauth
                                @else
                                    @if(strlen($category->required_permission ?? '') > 0)
                                        @auth
                                            @can($category->required_permission)
                                                <li><a class="dropdown-item" href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
                                            @endcan
                                        @endauth
                                    @else
                                        <li><a class="dropdown-item" href="{{ url('/records') }}?category={{ $category->id }}">{{ $category->category_name }}</a></li>
                                    @endif
                                @endif
                            @endforeach
                            @if(states_enabled())
                                <li><a class="dropdown-item" href="{{ url('countries') }}">Member States</a></li>
                            @else
                                <li><a class="dropdown-item" href="{{ url('adminunits') }}">Administrative Units</a></li>
                            @endif
                        </ul>
                    </li>
                    @php
                        $filteredTags = isset($tags) ? $tags->filter(fn($tag) => $tag->is_health_emergency ?? false)->values() : collect();
                    @endphp
                    @if($filteredTags->isNotEmpty())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->has('tag') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Health Emergencies</a>
                        <ul class="dropdown-menu">
                            @foreach($filteredTags as $tag)
                                <li><a class="dropdown-item" href="{{ tag_records_url($tag) }}">{{ $tag->tag_text }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    @endif
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('tools') || (isset($staticLinks) && count($staticLinks) > 0 && collect($staticLinks)->pluck('link')->contains(url()->current())) ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">Key Links</a>
                        <ul class="dropdown-menu">
                            @if(isset($staticLinks) && count($staticLinks) > 0)
                                @foreach($staticLinks as $link)
                                    <li><a class="dropdown-item" href="{{ $link->link }}" @if($link->open_in_new_tab ?? false) target="_blank" rel="noopener" @endif>{{ $link->title }}</a></li>
                                @endforeach
                            @endif
                            <li><a class="dropdown-item" href="{{ url('tools') }}">Tools</a></li>
                        </ul>
                    </li>
                    @php $counts = get_menu_counts(auth()->id()); @endphp
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('forums*') || request()->is('communities*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            Discussions
                            @if(($counts['total'] ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill ms-1">{{ $counts['total'] }}</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('forums') }}">Forums @if(($counts['forums'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['forums'] }}</span>@endif</a></li>
                            <li><a class="dropdown-item" href="{{ url('communities') }}">Communities @if(($counts['communities'] ?? 0) > 0)<span class="badge bg-danger ms-1">{{ $counts['communities'] }}</span>@endif</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Learning</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('courses') }}">Courses</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Support</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ url('faqs') }}">FAQs</a></li>
                            <li><a class="dropdown-item" href="{{ url('publications/request-content') }}">Content Request</a></li>
                        </ul>
                    </li>
                    @include('layouts.theme1.partials.create_menu')
                </ul>
            </div>
        </nav>
    </div>
</header>
