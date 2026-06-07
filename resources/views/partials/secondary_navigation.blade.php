@php
    $isHomePage = request()->is('/');
    $isContentRequestPage = request()->routeIs('content-request') || request()->is('publications/request-content');
    $isFaqsPage = request()->is('faqs*') || request()->routeIs('faqs*');
    $isForumsPage = request()->is('forums*') || request()->routeIs('forums.*');
    $isCommunitiesDetailPage = request()->is('communities/detail*') || request()->routeIs('community.detail');
    $isPublishPage = request()->routeIs('account.publish') || request()->routeIs('account.publication') || request()->is('account/publish*');
    $isMyDiscussionsPage = request()->routeIs('account.my-discussions') || request()->routeIs('account.my-discussions.edit');
    $isPublicationsAccountPage = request()->routeIs('account.publications') || request()->routeIs('account.publications.edit');
    $isFederatedBrowsePage = request()->is('federated*');
    $isAuthenticated = auth()->check();
    // If $forceShow is set to true, bypass the page check (used when explicitly included in content)
    $forceShow = $forceShow ?? false;
    $excludedPages = $isContentRequestPage || $isFaqsPage || $isForumsPage || $isCommunitiesDetailPage || $isPublishPage || $isFederatedBrowsePage;
    // Only show from layout if NOT on excluded pages AND not force-showing
    $shouldShow = !$isHomePage && $isAuthenticated && (!$excludedPages || $forceShow);
@endphp

@if($shouldShow)
@php
    // Get user-specific counts using cached helper
    $userForumsCount = 0;
    $userCommunitiesCount = 0;
    
    if ($isAuthenticated && auth()->id()) {
        $userId = auth()->id();
        $counts = get_menu_counts($userId);
        $userForumsCount = $counts['forums'];
        $userCommunitiesCount = $counts['communities'];
    }
@endphp
<style>
    .secondary-nav {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-bottom: 2px solid #e9ecef;
        padding: 0.75rem 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 100;
        margin-bottom: {{ $forceShow ? '4px' : '0' }};
    }
    
    .secondary-nav-container {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    
    .secondary-nav-link {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        color: #495057;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9375rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        position: relative;
        white-space: nowrap;
    }
    
    .secondary-nav-link i {
        margin-right: 0.5rem;
        font-size: 1rem;
        transition: transform 0.3s ease;
    }
    
    .secondary-nav-link:hover {
        background-color: rgba(17, 154, 72, 0.1);
        color: var(--theme-color-primary, #119A48);
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(17, 154, 72, 0.15);
    }
    
    .secondary-nav-link:hover i {
        transform: scale(1.15);
    }
    
    .secondary-nav-link.active {
        background-color: var(--theme-color-primary, #119A48);
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(17, 154, 72, 0.25);
    }
    
    .secondary-nav-link.active:hover {
        background-color: var(--theme-color-primary, #119A48);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.3);
    }
    
    .secondary-nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 60%;
        height: 3px;
        background: linear-gradient(90deg, transparent, #ffffff, transparent);
        border-radius: 2px;
    }
    
    .secondary-nav-badge {
        background: #ef4444;
        color: white;
        border-radius: 10px;
        padding: 2px 6px;
        font-size: 0.75rem;
        font-weight: bold;
        margin-left: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        line-height: 1;
    }
    
    @media (max-width: 767.98px) {
        .secondary-nav {
            padding: 0.5rem 0;
        }
        
        .secondary-nav-container {
            gap: 0.75rem;
            padding: 0 0.5rem;
        }
        
        .secondary-nav-link {
            padding: 0.4rem 0.75rem;
            font-size: 0.875rem;
        }
        
        .secondary-nav-link i {
            font-size: 0.9rem;
            margin-right: 0.4rem;
        }
    }
    
    @media (max-width: 575.98px) {
        .secondary-nav-container {
            justify-content: center;
        }
        
        .secondary-nav-link {
            flex: 1 1 auto;
            min-width: calc(50% - 0.375rem);
            justify-content: center;
            text-align: center;
        }
    }

    /* Dark mode: secondary nav */
    html[data-bs-theme="dark"] .secondary-nav {
        background: linear-gradient(135deg, #242628 0%, #2d3136 100%) !important;
        border-bottom-color: #3e4348 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    html[data-bs-theme="dark"] .secondary-nav-link {
        color: #d1d5db !important;
    }
    html[data-bs-theme="dark"] .secondary-nav-link i {
        color: inherit;
    }
    html[data-bs-theme="dark"] .secondary-nav-link:hover {
        background-color: rgba(17, 154, 72, 0.2) !important;
        color: var(--theme-color-primary, #119A48) !important;
    }
    html[data-bs-theme="dark"] .secondary-nav-link.active {
        background-color: var(--theme-color-primary, #119A48) !important;
        color: #ffffff !important;
    }
    html[data-bs-theme="dark"] .secondary-nav-link.active:hover {
        background-color: var(--theme-color-primary, #119A48) !important;
        color: #ffffff !important;
    }
    html[data-bs-theme="dark"] .secondary-nav-badge {
        background: #ef4444 !important;
        color: white !important;
    }
</style>

<nav class="secondary-nav">
    <div class="container">
        <div class="secondary-nav-container">
            <a href="{{ route('account.my-communities') }}" 
               class="secondary-nav-link {{ request()->routeIs('account.my-communities') ? 'active' : '' }}"
               title="My Communities">
                <i class="fa fa-users"></i>
                <span>My Communities</span>
                @if($userCommunitiesCount > 0)
                    <span class="secondary-nav-badge">{{ $userCommunitiesCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('account.my-forums') }}" 
               class="secondary-nav-link {{ request()->routeIs('account.my-forums') ? 'active' : '' }}"
               title="My Forums">
                <i class="fa fa-comments"></i>
                <span>My Forums</span>
                @if($userForumsCount > 0)
                    <span class="secondary-nav-badge">{{ $userForumsCount }}</span>
                @endif
            </a>
            
            <a href="{{ route('account.chats') }}" 
               class="secondary-nav-link {{ request()->routeIs('account.chats') ? 'active' : '' }}"
               title="My Chats">
                <i class="fa fa-comment-dots"></i>
                <span>My Chats</span>
            </a>
            
            <a href="{{ route('account.publications') }}" 
               class="secondary-nav-link {{ $isPublicationsAccountPage || request()->routeIs('account.publish') || request()->routeIs('account.publication') ? 'active' : '' }}"
               title="My publications">
                <i class="fa fa-plus-circle"></i>
                <span>Publish Resource</span>
            </a>
            
            <a href="{{ route('account.my-discussions') }}" 
               class="secondary-nav-link {{ $isMyDiscussionsPage || request()->routeIs('forums.create') ? 'active' : '' }}"
               title="My forum posts">
                <i class="fa fa-comments"></i>
                <span>Start Discussion</span>
            </a>
        </div>
    </div>
</nav>
@endif

