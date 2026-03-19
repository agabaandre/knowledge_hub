<!-- Main-content -->
<style>
    /* Fix notification dropdown positioning to prevent overflow */
    #unified-notification-dropdown {
        position: relative;
    }
    
    #notification-dropdown-menu {
        right: 0 !important;
        left: auto !important;
        max-width: min(400px, calc(100vw - 30px)) !important;
        display: none !important;
        flex-direction: column !important;
        padding: 0 !important;
        max-height: 600px;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.15s linear, visibility 0.15s linear;
    }
    
    #notification-dropdown-menu.show {
        display: flex !important;
        visibility: visible;
        opacity: 1;
    }
    
    #notification-items-list {
        flex: 1 1 auto;
        overflow-y: auto;
        min-height: 0;
    }
    
    @media (max-width: 768px) {
        #notification-dropdown-menu {
            max-width: calc(100vw - 20px) !important;
        }
    }
</style>
@php $adminNavStyle = settings()->admin_nav_style ?? 'colored'; @endphp
<div class="main-header main-header-fixed" style="background: {{ $adminNavStyle === 'light' ? '#f8fafc' : 'var(--theme-color-primary)' }}; color: {{ $adminNavStyle === 'light' ? '#334155' : '#FFF' }} !important;">
    <div class=" container">
        <div class="main-header-left ">
            <a class="animated-arrow hor-toggle horizontal-navtoggle"><span></span></a><!-- sidebar-toggle-->
            <!-- <a class="header-brand" href="{{ url('/') }}">
    <img src="{{ settings()->logo }}" id="change-image"  width=150>
   </a> -->
        </div>
        <div class="main-header-center">
            @php $defaultAdminLogoPx = (int)(settings()->logo_scale ?? 80); $defaultAdminLogoPx = in_array($defaultAdminLogoPx, [40,50,60,70,80,100,120]) ? $defaultAdminLogoPx : 80; @endphp
            <div class="d-flex align-items-center" style="min-height: {{ $defaultAdminLogoPx }}px;">
                <img src="{{ settings()->logo }}" id="change-image" alt="Logo"
                    style="border-radius:2px; background:#FFF; height:{{ $defaultAdminLogoPx }}px; max-height:{{ $defaultAdminLogoPx }}px; width: auto;">
            </div>
        </div>
        <div class="main-header-right">
            <div class="dropdown main-header-message right-toggle">
                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link menu-icons fullscreen" href="#"><svg class="svg-icon"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                            </path>
                        </svg></a>
                    <a class="new nav-link full-screen-link exit-fullscreen" href="#"><svg class="svg-icon"
                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path
                                d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3">
                            </path>
                        </svg></a>
                </div>
            </div><!-- Full-screen closed -->
            
            <!-- Unified Notification Bell -->
            <div class="dropdown nav-item main-header-message" id="unified-notification-dropdown">
                <a class="new nav-link menu-icons position-relative" href="#" data-toggle="dropdown" id="notification-bell">
                    <svg class="svg-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    @if ($total_pending_count > 0)
                        <span class="badge badge-danger badge-pill" id="notification-count-badge" style="position:absolute;top:-4px;right:-6px;min-width:20px;background:#dc3545!important;color:#fff!important;">{{ $total_pending_count }}</span>
                    @endif
                </a>
                <div class="dropdown-menu animated fadeInUp dropdown-menu-right" style="min-width:380px;max-width:400px;right:0;left:auto;transform:translateX(0);margin-right:0;flex-direction:column;padding:0;max-height:600px;" id="notification-dropdown-menu">
                    <div class="menu-header-content text-left p-3 border-bottom" style="background-color: #fff;flex-shrink:0;">
                        <h6 class="menu-header-title mb-2" style="font-weight: 600; color: #2d3748;">Pending Approvals</h6>
                        <div id="notification-breakdown" class="small">
                            @if ($total_pending_count > 0)
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Forums:</span>
                                    <strong>{{ $pending_forums_count }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Publications:</span>
                                    <strong>{{ $pending_publications_count }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Forum Comments:</span>
                                    <strong>{{ $pending_forum_comments_count }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Publication Comments:</span>
                                    <strong>{{ $pending_publication_comments_count }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>COP Approvals:</span>
                                    <strong>{{ $pending_cop_approvals_count ?? 0 }}</strong>
                                </div>
                                @can('view_content_requests')
                                @if(isset($pending_content_requests_count) && $pending_content_requests_count > 0)
                                <div class="d-flex justify-content-between mt-1">
                                    <span>Content requests:</span>
                                    <strong>{{ $pending_content_requests_count }}</strong>
                                </div>
                                @endif
                                @endcan
                            @else
                                <p class="text-muted mb-0">No pending approvals</p>
                            @endif
                        </div>
                    </div>
                    <div class="main-message-list chat-scroll" style="flex:1 1 auto;overflow-y:auto;min-height:0;max-height:none;" id="notification-items-list">
                        @if ($total_pending_count > 0)
                            @php
                                // Use sorted notifications if available, otherwise fall back to old method
                                $notifications = isset($sorted_notifications) && $sorted_notifications->count() > 0 
                                    ? $sorted_notifications 
                                    : collect();
                            @endphp
                            
                            @if($notifications->count() > 0)
                                @foreach ($notifications as $notification)
                                    @php
                                        $type = $notification['type'];
                                        $item = $notification['item'];
                                        $createdAt = $notification['created_at'];
                                    @endphp
                                    
                                    @if($type === 'forum')
                                        <a href="{{ url('admin/forums/moderate') }}?id={{ $item->id }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-comments text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">Forum</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($item->forum_title ?? 'Untitled', 50) }}</p>
                                                <small class="text-muted">By: {{ $item->user->name ?? 'Unknown' }}</small>
                                            </div>
                                        </a>
                                    @elseif($type === 'publication')
                                        <a href="{{ url('admin/publications/details') }}?id={{ $item->id }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-file-alt text-success"></i>
                                </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">Publication</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                        @endif
                                                    </small>
                                    </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($item->title ?? 'Untitled', 50) }}</p>
                                                <small class="text-muted">By: {{ $item->author->name ?? ($item->user->name ?? 'Unknown') }}</small>
                                </div>
                            </a>
                                    @elseif($type === 'forum_comment')
                                        <a href="{{ url('admin/forums/moderate') }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-comment-dots text-info"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">Forum Comment</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                        @endif
                                                    </small>
                    </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit(strip_tags($item->comment ?? ''), 50) }}</p>
                                                <small class="text-muted">By: {{ $item->user->name ?? 'Anonymous' }}</small>
                    </div>
                                        </a>
                                    @elseif($type === 'publication_comment')
                                        <a href="{{ url('admin/publications/moderate') }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-comment text-warning"></i>
                </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">Publication Comment</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                    @endif
                                                    </small>
                                                </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit(strip_tags($item->comment ?? ''), 50) }}</p>
                                                <small class="text-muted">By: {{ $item->user->name ?? 'Anonymous' }}</small>
                                            </div>
                                        </a>
                                    @elseif($type === 'cop_approval')
                                        @php
                                            $approval = $item;
                                            $community = $approval['community'] ?? null;
                                            $communityId = $community ? $community->id : ($approval['member']->community_of_practice_id ?? 0);
                                            $communityName = $community ? $community->community_name : 'Unknown Community';
                                            
                                            // Try to get community name directly if relationship failed
                                            if (!$community && isset($approval['member']) && $approval['member']->community_of_practice_id) {
                                                $directCommunity = \App\Models\CommunityOfPractice::find($approval['member']->community_of_practice_id);
                                                if ($directCommunity) {
                                                    $communityName = $directCommunity->community_name;
                                                    $communityId = $directCommunity->id;
                                                }
                                            }
                                        @endphp
                                        <a href="{{ route('admin.commsofpractice.details', $communityId) }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-users text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">COP Member Approval</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $communityName }}</p>
                                                <small class="text-muted">User: {{ $approval['user']->name ?? 'Unknown' }}</small>
                                            </div>
                                        </a>
                                    @elseif($type === 'content_request')
                                        <a href="{{ route('admin.content-requests.index') }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-bell text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">Content request</h6>
                                                    <small class="text-muted">
                                                        @if($createdAt)
                                                            {{ \Carbon\Carbon::parse($createdAt)->diffForHumans() }}
                                                        @endif
                                                    </small>
                                                </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($item->subject ?? 'No subject', 50) }}</p>
                                                <small class="text-muted">{{ $item->email ?? '' }}</small>
                                            </div>
                                        </a>
                                    @endif
                                @endforeach
                            @else
                                {{-- Fallback to old display method if sorted_notifications not available --}}
                                @foreach ($pending_forums->take(3) as $forum)
                                    <a href="{{ url('admin/forums/moderate') }}?id={{ $forum->id }}" class="p-3 d-flex border-bottom notification-item">
                                        <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-comments text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1" style="font-size:0.875rem;">Forum</h6>
                                                <small class="text-muted">
                                                    @if($forum->created_at)
                                                        {{ \Carbon\Carbon::parse($forum->created_at)->diffForHumans() }}
                            @endif
                                                </small>
                                            </div>
                                            <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($forum->forum_title ?? 'Untitled', 50) }}</p>
                                            <small class="text-muted">By: {{ $forum->user->name ?? 'Unknown' }}</small>
                                        </div>
                                    </a>
                                @endforeach
                                
                                @foreach ($pending_publications->take(3) as $publication)
                                    <a href="{{ url('admin/publications/details') }}?id={{ $publication->id }}" class="p-3 d-flex border-bottom notification-item">
                                        <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                            <i class="fa fa-file-alt text-success"></i>
                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1" style="font-size:0.875rem;">Publication</h6>
                                                <small class="text-muted">
                                                    @if($publication->created_at)
                                                        {{ \Carbon\Carbon::parse($publication->created_at)->diffForHumans() }}
                                                    @endif
                                                </small>
                    </div>
                                            <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ \Illuminate\Support\Str::limit($publication->title ?? 'Untitled', 50) }}</p>
                                            <small class="text-muted">By: {{ $publication->author->name ?? ($publication->user->name ?? 'Unknown') }}</small>
                                </div>
                                    </a>
                                @endforeach
                                
                                @if(isset($pending_cop_approvals) && $pending_cop_approvals->count() > 0)
                                    @foreach ($pending_cop_approvals->take(3) as $approval)
                                        @php
                                            $community = $approval['community'] ?? null;
                                            $communityId = $community ? $community->id : ($approval['member']->community_of_practice_id ?? 0);
                                            $communityName = $community ? $community->community_name : 'Unknown Community';
                                            
                                            if (!$community && isset($approval['member']) && $approval['member']->community_of_practice_id) {
                                                $directCommunity = \App\Models\CommunityOfPractice::find($approval['member']->community_of_practice_id);
                                                if ($directCommunity) {
                                                    $communityName = $directCommunity->community_name;
                                                    $communityId = $directCommunity->id;
                                                }
                                            }
                                        @endphp
                                        <a href="{{ route('admin.commsofpractice.details', $communityId) }}" class="p-3 d-flex border-bottom notification-item">
                                            <div class="drop-img cover-image mr-3" style="width:40px;height:40px;background:#e2e8f0;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                                <i class="fa fa-users text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1" style="font-size:0.875rem;">COP Member Approval</h6>
                                                    <small class="text-muted">
                                                        @if($approval['created_at'])
                                                            {{ \Carbon\Carbon::parse($approval['created_at'])->diffForHumans() }}
                                                        @endif
                                                    </small>
                                    </div>
                                                <p class="mb-0 text-muted" style="font-size:0.8rem;">{{ $communityName }}</p>
                                                <small class="text-muted">User: {{ $approval['user']->name ?? 'Unknown' }}</small>
                                </div>
                            </a>
                        @endforeach
                                @endif
                            @endif
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="fa fa-check-circle fa-2x mb-2"></i>
                                <p class="mb-0">All caught up! No pending approvals.</p>
                    </div>
                        @endif
                    </div>
                </div>
            </div><!-- Unified Notification Bell closed -->

            <!-- Main-header-message closed -->
            <div class="dropdown main-profile-menu nav nav-item nav-link">

                <a class="">
                        @if(!empty(current_user()->photo))
                            <img class="rounded-circle notranslate user-avatar-img"
                                src="{{ current_user()->photo }}"
                                style="width: 45px; height: 45px; border-radius: 50%; background-color: #f1f5f9; display: inline-block; object-fit: cover;"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                            <span class="user-avatar-fallback" style="display:none; width: 45px; height: 45px; border-radius: 50%; background-color: #f1f5f9; border: 1px solid #e2e8f0; align-items: center; justify-content: center; color: #334155; font-size: 18px;"><i class="fa fa-user" aria-hidden="true"></i></span>
                        @else
                            <span class="user-avatar-fallback" style="display:inline-flex; width: 45px; height: 45px; border-radius: 50%; background-color: #f1f5f9; border: 1px solid #e2e8f0; align-items: center; justify-content: center; color: #334155; font-size: 18px;"><i class="fa fa-user" aria-hidden="true"></i></span>
                        @endif
                        <span>{{ ' ' . ucwords(@current_user()->name) ?? '' }}</span></a>
                <div class="dropdown-menu animated fadeInUp">

                    <a class="dropdown-item" href="{{ route('home') }}" target="_blank"><i class="bx bx-link"></i>
                        View Khub Website</a>
                    <a class="dropdown-item" href="{{ url('permissions/profile') }}"><i
                            class="bx bx-user-circle"></i> My Profile</a>
                    <a class="dropdown-item" href="{{ url('logout') }}"><i class="bx bx-log-out-circle"></i> Log
                        Out</a>
                </div>
            </div><!-- Main-profile-menu closed -->
            <button class="navbar-toggler navresponsive-toggler" type="button" data-toggle="collapse"
                data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon bx bx-dots-vertical-rounded"></span>
            </button><!-- Navresponsive closed -->
        </div>
    </div>
</div>
<!-- Main-header closed -->
