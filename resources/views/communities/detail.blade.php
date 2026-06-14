@php
    $hide_search = true;
    // SEO Meta Tags for Community Detail Page
    $pageTitle = ($community->community_name ?? 'Community') . ' - Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = Str::limit(strip_tags($community->description ?? ''), 160) ?: ($community->community_name . ' - A professional community of practice focused on public health topics.');
    $pageKeywords = 'community of practice, ' . ($community->community_name ?? '') . ', public health, ' . (settings()->seo_keywords ?? '');
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = community_detail_url($community);
    $ogType = 'profile';

    $detailFaceLimit = communities_listing_show_participants() ? communities_listing_max_faces() : 0;
    $highlightParticipantNames = collect($community->listing_contributor_faces ?? [])
        ->take($detailFaceLimit)
        ->map(fn ($f) => $f['user']->name ?? '')
        ->filter()
        ->unique()
        ->values();
    if ($highlightParticipantNames->isNotEmpty()) {
        $pageKeywords .= ', ' . $highlightParticipantNames->implode(', ');
        $pageDescription = \Illuminate\Support\Str::limit(
            trim($pageDescription) . ' Featured participants: ' . $highlightParticipantNames->implode(', ') . '.',
            320
        );
    }

    // Get community stats
    $memberCount = $community->approved_members_count ?? $community->members_count ?? 0;
    $forumCount = $community->community_forums_count ?? $community->forums_count ?? 0;
    $publicationCount = $community->community_publications_count ?? $community->publications_count ?? 0;
@endphp

@extends('layouts.app')

@section('structured_data')
@if(!empty($communityOrganizationLd))
<script type="application/ld+json">{!! json_encode($communityOrganizationLd, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endif
@endsection

@section('styles')
@include('communities.partials.detail_styles')
@endsection

@php
    $activeTab = $activeTab ?? (request()->filled('pcr_page') ? 'processed' : 'publications');
    $wallPostTotal = isset($communityWallPosts) && method_exists($communityWallPosts, 'total')
        ? $communityWallPosts->total()
        : ($communityComments ?? collect())->count();
    $forumTabTotal = isset($communityForums) && method_exists($communityForums, 'total')
        ? $communityForums->total()
        : ($forums ?? collect())->count();
    $publicationTabTotal = isset($publications) && method_exists($publications, 'total')
        ? $publications->total()
        : 0;
    $processedTabTotal = isset($processedCommunityContentRequests) && method_exists($processedCommunityContentRequests, 'total')
        ? $processedCommunityContentRequests->total()
        : 0;
    if ($openWallPostForm ?? false) {
        $activeTab = 'wall';
    }
@endphp

@section('content')
@php
    $isCommunityMember = $isCommunityMember ?? true;
    $isPendingMember = $isPendingMember ?? false;
@endphp
{{-- Custom Header Section (replaces search bar) --}}
<div class="pt-5 pt-0 custom-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div style="text-align: center; padding: 2rem 0;">
                    <h1 class="notranslate" translate="no" style="font-size: 2rem; font-weight: 700; margin: 0 0 0.5rem 0; color: white;" itemprop="name">
                        {{ $community->community_name }}
                    </h1>
                    <p style="margin: 0 0 1rem 0; color: rgba(255, 255, 255, 0.95); font-size: 1rem;">
                        {!! \Illuminate\Support\Str::words(strip_tags($community->description ?? ''), 50, '...') !!}
                    </p>
                    <div class="community-detail-hero__metrics">
                        <span class="community-detail-hero__metric community-detail-hero__metric--members">
                            <i class="fa fa-users"></i>{{ $community->approved_members_count ?? $community->members_count ?? 0 }} Members
                        </span>
                        <span class="community-detail-hero__metric community-detail-hero__metric--forums">
                            <i class="fa fa-comments"></i>{{ $community->community_forums_count ?? $community->forums_count ?? 0 }} Forums
                        </span>
                        <span class="community-detail-hero__metric community-detail-hero__metric--publications">
                            <i class="fa fa-book"></i>{{ $community->community_publications_count ?? $community->publications_count ?? 0 }} Publications
                        </span>
                        @if($isCommunityMember ?? false)
                        <span class="community-detail-hero__metric community-detail-hero__metric--comments">
                            <i class="fa fa-heart"></i>{{ $wallPostTotal }} Wall posts
                        </span>
                        @endif
                    </div>
                    @if($isCommunityMember)
                    <div class="mt-3 d-flex justify-content-center flex-wrap" style="gap: 8px;">
                        <button type="button" class="btn btn-sm text-light community-hero-post-btn" style="background-color: {{ settings()->primary_color ?? '#119A48' }}; border-color: {{ settings()->primary_color ?? '#119A48' }};" onclick="window.openCommunityWallPost && window.openCommunityWallPost();">
                            <i class="fa fa-pencil-square-o mr-1"></i>Post on community wall
                        </button>
                        <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#inviteColleaguesModal">
                            <i class="fa fa-envelope mr-1"></i>Invite colleagues (max 5)
                        </button>
                        @if(!empty($isCommunityAdmin))
                        <button type="button" class="btn btn-sm text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }}; border-color: {{ settings()->primary_color ?? '#119A48' }};" data-toggle="modal" data-target="#createCommunityEventModal">
                            <i class="fa fa-calendar-plus-o mr-1"></i>Create community event
                        </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Secondary Navigation Below Banner --}}
@include('partials.secondary_navigation', ['forceShow' => true])

<div class="community-detail-shell">
<div class="container community-detail-page publication-feed-card-scope">
    @if(! $isCommunityMember)
        <div class="alert {{ $isPendingMember ? 'alert-warning' : 'alert-info' }} mb-4">
            @if($isPendingMember)
                <strong><i class="fa fa-clock-o mr-1"></i>Membership pending.</strong>
                Your join request is awaiting approval. You will see publications, forums, and member tools here once approved.
            @elseif(Auth::check())
                <strong><i class="fa fa-users mr-1"></i>Join this community</strong> to access publications, forum discussions, events, and member collaboration tools.
                <div class="mt-3">
                    <button type="button" class="btn btn-primary join-community-detail-btn" data-community-id="{{ $community->id }}">
                        <i class="fa fa-plus-circle mr-1"></i>Request to join
                    </button>
                    <a href="{{ route('community.index') }}" class="btn btn-outline-secondary ml-2">Browse all communities</a>
                </div>
            @else
                <strong><i class="fa fa-sign-in mr-1"></i>Sign in to join</strong> this community and access its resources and discussions.
                <div class="mt-3">
                    <a href="{{ route('login', ['redirect' => community_detail_url($community)]) }}" class="btn btn-primary">Log in to join</a>
                    <a href="{{ route('community.index') }}" class="btn btn-outline-secondary ml-2">Browse communities</a>
                </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h5 mb-3">About this community</h2>
                <div class="community-preview-description" style="line-height: 1.7;">
                    {!! detect_and_embed_video_links($community->description ?? '', 180, 180) !!}
                </div>
                @if($community->organisation || $community->department)
                    <p class="text-muted small mt-3 mb-0">
                        @if($community->organisation)<span class="mr-3 notranslate" translate="no"><i class="fa fa-building mr-1"></i>{{ $community->organisation }}</span>@endif
                        @if($community->department)<span class="notranslate" translate="no"><i class="fa fa-sitemap mr-1"></i>{{ $community->department }}</span>@endif
                    </p>
                @endif
                @if(communities_listing_show_participants() && collect($community->listing_contributor_faces ?? [])->isNotEmpty())
                    @include('communities.partials.participant_faces', ['faces' => collect($community->listing_contributor_faces ?? [])->take(communities_listing_max_faces())])
                @endif
            </div>
        </div>
    @else
    <div class="row">
        <div class="col-lg-8">
            <div class="community-about-card">
                <h2 class="community-about-card__title"><i class="fa fa-info-circle mr-2 theme-text"></i>About this community</h2>
                <div style="line-height:1.7;text-align:justify;">
                    {!! detect_and_embed_video_links($community->description ?? '', 180, 180) !!}
                </div>
                @if($community->organisation || $community->department)
                    <p class="text-muted small mt-3 mb-0">
                        @if($community->organisation)<span class="mr-3 notranslate" translate="no"><i class="fa fa-building mr-1"></i>{{ $community->organisation }}</span>@endif
                        @if($community->department)<span class="notranslate" translate="no"><i class="fa fa-sitemap mr-1"></i>{{ $community->department }}</span>@endif
                    </p>
                @endif
                @if(communities_listing_show_participants() && collect($community->listing_contributor_faces ?? [])->isNotEmpty())
                    @include('communities.partials.participant_faces', ['faces' => collect($community->listing_contributor_faces ?? [])->take(communities_listing_max_faces())])
                @endif
            </div>

            @if(isset($pendingCommunityContentRequests) && $pendingCommunityContentRequests->isNotEmpty())
            <div class="card mb-4 border-warning" style="border-width: 2px;">
                <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0">
                        <i class="fa fa-inbox theme-text mr-2"></i>Open content requests
                    </h5>
                    <span class="badge badge-warning text-dark">{{ $pendingCommunityContentRequests->count() }} pending</span>
                </div>
                <div class="card-body p-0">
                    <p class="text-muted small px-3 pt-3 mb-2">These requests were referred to this community for discussion. They are not yet marked as fully processed in the hub.</p>
                    @foreach($pendingCommunityContentRequests as $cr)
                        <div class="border-top px-3 py-3 community-content-request-card open-cr-row" style="background: #fffbeb;">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <h6 class="mb-1 font-weight-bold">{{ $cr->subject }}</h6>
                                    <div class="small text-muted">
                                        @if($cr->country)<span class="mr-2 notranslate" translate="no"><i class="fa fa-globe mr-1"></i>{{ $cr->country->name }}</span>@endif
                                        @if($cr->referred_at)<span><i class="fa fa-share mr-1"></i>Referred {{ $cr->referred_at->format('M j, Y') }}</span>@endif
                                        @if($cr->referredByUser)<span class="ml-2 notranslate" translate="no">by {{ $cr->referredByUser->name }}</span>@endif
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap shrink-0" style="gap: 6px;">
                                    <a href="{{ $cr->discussionUrlForCommunity($community->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-comments mr-1"></i>Forum thread
                                    </a>
                                    @if($cr->userMayMarkReferralAsProcessed(auth()->user()))
                                        <a href="{{ $cr->discussionUrlForCommunity($community->id) }}#mark-processed" class="btn btn-sm btn-success">
                                            <i class="fa fa-check mr-1"></i>Mark processed
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @if($cr->referral_notes)
                                <p class="small mb-0 mt-2 text-muted"><strong>Note:</strong> {{ Str::limit(strip_tags($cr->referral_notes), 200) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Activity tabs (React — no full page reload) -->
            <div id="community-detail-tabs-root"></div>

            <!-- Tab Content -->
            <div class="tab-content" id="communityTabsContent">
                <div class="tab-pane fade {{ $activeTab === 'wall' ? 'show active' : '' }}" id="wall-posts" role="tabpanel" @if($activeTab !== 'wall') style="display:none;" @endif>
                    @include('communities.partials.community_comments', [
                        'community' => $community,
                        'communityWallPosts' => $communityWallPosts ?? $communityComments ?? collect(),
                        'isCommunityMember' => $isCommunityMember,
                        'openWallPostForm' => $openWallPostForm ?? false,
                        'embeddedInTab' => true,
                    ])
                </div>

                <!-- Publications Tab -->
                <div class="tab-pane fade {{ $activeTab === 'publications' ? 'show active' : '' }}" id="publications" role="tabpanel" @if($activeTab !== 'publications') style="display:none;" @endif>
                    @if(($hasRecentWallPosts ?? false) && ($wallPostTotal ?? 0) > 0)
                        <div class="community-recent-wall-notice mb-3">
                            <i class="fa fa-bolt mr-1"></i>
                            There {{ $wallPostTotal === 1 ? 'is' : 'are' }} recent activity on the
                            <a href="#" role="button" onclick="event.preventDefault(); if(window.communityDetailSwitchTab){window.communityDetailSwitchTab('wall');}">community wall</a>
                            from the last 7 days.
                        </div>
                    @endif
                    @if($publications->count() > 0)
                        @foreach($publications as $index => $publication)
                            @include('partials.publications.publication_feed_card', [
                                'row' => $publication,
                                'i' => (($publications->currentPage() - 1) * $publications->perPage()) + $index + 1,
                            ])
                        @endforeach
                        <div class="mt-4">
                            {{ $publications->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i>No publications found in this community.
                        </div>
                    @endif
                </div>

                <!-- Forums Tab -->
                <div class="tab-pane fade {{ $activeTab === 'forums' ? 'show active' : '' }}" id="forums" role="tabpanel" @if($activeTab !== 'forums') style="display:none;" @endif>
                    @if(isset($communityForums) && $communityForums->count() > 0)
                        @foreach($communityForums as $forum)
                            @include('communities.partials.community_forum_card', ['forum' => $forum])
                        @endforeach
                        <div class="mt-3">{{ $communityForums->links() }}</div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle mr-2"></i>No forum discussions linked to this community yet.
                        </div>
                    @endif
                </div>

                <!-- Processed content requests -->
                <div class="tab-pane fade {{ $activeTab === 'processed' ? 'show active' : '' }}" id="processed-content-requests" role="tabpanel" @if($activeTab !== 'processed') style="display:none;" @endif>
                    @if(isset($processedCommunityContentRequests) && $processedCommunityContentRequests->total() > 0)
                        <p class="text-muted small mb-3">Requests that were referred to this community and later completed through the Knowledge Hub (resources shared with the requester).</p>
                        @foreach($processedCommunityContentRequests as $cr)
                            <div class="community-featured-card community-content-request-card mb-3">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <h5 class="community-featured-title mb-1">{{ $cr->subject }}</h5>
                                        <div class="community-featured-meta">
                                            @if($cr->country)<span class="mr-2 notranslate" translate="no"><i class="fa fa-globe mr-1"></i>{{ $cr->country->name }}</span>@endif
                                            <span><i class="fa fa-check mr-1"></i>Processed {{ $cr->processed_at ? $cr->processed_at->format('M j, Y') : '—' }}</span>
                                            @if($cr->processedBy)<span class="ml-2 notranslate" translate="no"><i class="fa fa-user mr-1"></i>{{ $cr->processedBy->name }}</span>@endif
                                        </div>
                                    </div>
                                    <a href="{{ $cr->discussionUrlForCommunity($community->id) }}" class="btn btn-sm btn-outline-primary shrink-0">
                                        <i class="fa fa-comments mr-1"></i>Thread
                                    </a>
                                </div>
                                @if($cr->content_links)
                                    <div class="small mt-2 p-2 bg-light rounded" style="white-space: pre-wrap; max-height: 120px; overflow-y: auto;">{{ Str::limit(strip_tags($cr->content_links), 400) }}</div>
                                @endif
                                @if($cr->admin_comments)
                                    <p class="small text-muted mb-0 mt-2"><strong>Admin comments:</strong> {{ Str::limit(strip_tags($cr->admin_comments), 200) }}</p>
                                @endif
                            </div>
                        @endforeach
                        <div class="mt-3">
                            {{ $processedCommunityContentRequests->links() }}
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fa fa-info-circle mr-2"></i>No processed content requests yet for this community. When referred requests are completed in the hub, they will appear here.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @include('communities.partials.detail_sidebar', [
                'community' => $community,
                'forums' => $forums,
                'otherCommunities' => $otherCommunities,
                'badgeTypes' => $badgeTypes ?? collect(),
                'communityEvents' => $communityEvents ?? collect(),
            ])
        </div>
    </div>
    @endif
</div>
</div>

@if($isCommunityMember)
<div class="modal fade" id="inviteColleaguesModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Invite colleagues</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form id="inviteColleaguesForm">
                @csrf
                <div class="modal-body">
                    <label>Emails (max 5)</label>
                    <textarea class="form-control" name="emails" rows="3" placeholder="name1@example.com, name2@example.com" required></textarea>
                    <small class="text-muted">Separate by comma, space, or new line.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send invitations</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($isCommunityAdmin))
<div class="modal fade" id="createCommunityEventModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Create community event</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form id="createCommunityEventForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group"><label>Title</label><input class="form-control" name="title" required></div>
                    <div class="form-group"><label>Type</label>
                        <select class="form-control" name="event_category" required>
                            <option value="Meeting">Meeting</option>
                            <option value="Webinar">Webinar</option>
                            <option value="Workshop">Workshop</option>
                            <option value="Training">Training</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
                    <div class="form-group"><label>Start date/time</label><input type="datetime-local" class="form-control" name="startdate" required></div>
                    <div class="form-group"><label>End date/time</label><input type="datetime-local" class="form-control" name="enddate"></div>
                    <div class="form-group"><label>Venue</label><input class="form-control" name="venue"></div>
                    <div class="form-group"><label>Event link (optional)</label><input type="url" class="form-control" name="event_link"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }}; border-color: {{ settings()->primary_color ?? '#119A48' }};">Create event</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@else
<div class="modal fade" id="joinCommunityDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Join community</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">Request to join <strong class="notranslate" translate="no">{{ $community->community_name }}</strong>? An administrator may need to approve your membership.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmJoinCommunityDetail">Join</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@if($isCommunityMember)
@php
    $communityDetailReactConfig = [
        'activeTab' => $activeTab,
        'defaultTab' => $activeTab,
        'primaryColor' => settings()->primary_color ?? '#119A48',
        'paneIds' => [
            'wall' => 'wall-posts',
            'publications' => 'publications',
            'forums' => 'forums',
            'processed' => 'processed-content-requests',
        ],
        'tabs' => [
            [
                'id' => 'wall',
                'label' => 'Wall posts',
                'icon' => 'fa-heart',
                'count' => (int) $wallPostTotal,
                'showNew' => (bool) ($hasRecentWallPosts ?? false),
            ],
            [
                'id' => 'publications',
                'label' => 'Publications',
                'icon' => 'fa-book',
                'count' => (int) $publicationTabTotal,
                'showNew' => false,
            ],
            [
                'id' => 'forums',
                'label' => 'Forums',
                'icon' => 'fa-comments',
                'count' => (int) $forumTabTotal,
                'showNew' => false,
            ],
            [
                'id' => 'processed',
                'label' => 'Processed requests',
                'icon' => 'fa-check-circle',
                'count' => (int) $processedTabTotal,
                'showNew' => false,
            ],
        ],
        'otherCommunities' => ($otherCommunities ?? collect())->map(function ($c) {
            return [
                'id' => (int) $c->id,
                'name' => $c->community_name,
                'url' => community_detail_url($c),
                'description' => \Illuminate\Support\Str::words(strip_tags($c->description ?? ''), 16, '...'),
                'publications' => (int) ($c->community_publications_count ?? 0),
                'forums' => (int) ($c->community_forums_count ?? 0),
                'members' => (int) ($c->approved_members_count ?? 0),
            ];
        })->values()->all(),
    ];
@endphp
<script>window.communityDetailReactConfig = @json($communityDetailReactConfig);</script>
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="{{ asset('assets/js/community-detail-react.js') }}"></script>
<script>
    (function () {
        var membersState = {
            page: 1,
            perPage: 20,
            hasMore: true,
            loading: false,
            query: '',
            isCommunityAdmin: {{ !empty($isCommunityAdmin) ? 'true' : 'false' }}
        };

        function escapeHtml(v) {
            return String(v || '').replace(/[&<>"']/g, function (m) {
                return ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'})[m];
            });
        }

        function memberItemHtml(item) {
            var adminBadge = item.is_admin
                ? '<span class="badge text-light" style="background-color: {{ settings()->primary_color ?? '#119A48' }};">Admin</span>'
                : '<span class="badge badge-secondary">Member</span>';
            var activeBadge = item.is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            var actionHtml = '';
            if (membersState.isCommunityAdmin && parseInt(item.user_id, 10) !== {{ (int)(auth()->id() ?? 0) }}) {
                if (item.is_active) {
                    actionHtml = '<div class="mt-2"><button class="btn btn-sm btn-outline-danger js-member-toggle" data-member-id="' + item.membership_id + '" data-action="deactivate" type="button">Mark inactive</button></div>';
                } else {
                    actionHtml = '<div class="mt-2"><button class="btn btn-sm btn-outline-success js-member-toggle" data-member-id="' + item.membership_id + '" data-action="activate" type="button">Mark active</button></div>';
                }
            }
            var avatarInner = item.photo_url
                ? '<img src="' + escapeHtml(item.photo_url) + '" alt="" onerror="this.style.display=\'none\';var s=this.nextElementSibling;if(s){s.style.display=\'flex\';}">'
                    + '<span style="display:none;align-items:center;justify-content:center;width:100%;height:100%;">' + escapeHtml(item.initials || '?') + '</span>'
                : escapeHtml(item.initials || '?');
            var avatarHtml = item.profile_url
                ? '<a href="' + escapeHtml(item.profile_url) + '" class="member-item-avatar mr-3" title="View profile: ' + escapeHtml(item.name) + '" aria-label="View profile: ' + escapeHtml(item.name) + '">' + avatarInner + '</a>'
                : '<span class="member-item-avatar mr-3" aria-hidden="true">' + avatarInner + '</span>';
            var nameHtml = item.profile_url
                ? '<a href="' + escapeHtml(item.profile_url) + '" class="member-name-link notranslate" translate="no">' + escapeHtml(item.name) + '</a>'
                : '<span class="notranslate" translate="no">' + escapeHtml(item.name) + '</span>';
            return '' +
                '<li class="member-item">' +
                '  <div class="d-flex align-items-start">' +
                avatarHtml +
                '    <div class="flex-grow-1 min-width-0">' +
                '      <div class="member-name notranslate" translate="no"><span class="badge badge-secondary mr-1">' + item.rank + '</span>' + nameHtml + '</div>' +
                '      <div class="member-title notranslate" translate="no"><i class="fa fa-briefcase mr-1"></i>' + escapeHtml(item.job_title || 'Not specified') + '</div>' +
                '      <div class="small text-muted mt-1"><i class="fa fa-envelope mr-1"></i>' + escapeHtml(item.email) + '</div>' +
                '      <div class="mt-1">' + adminBadge + ' ' + activeBadge + ' <span class="badge badge-info">' + parseInt(item.publication_count, 10) + ' Publications</span></div>' +
                actionHtml +
                '    </div>' +
                '  </div>' +
                '</li>';
        }

        function setMembersUiState() {
            var listCount = jQuery('#communityMembersList').children().length;
            jQuery('#communityMembersEmpty').toggle(!membersState.loading && listCount === 0);
            jQuery('#communityMembersLoader').toggle(membersState.loading);
            jQuery('#communityMembersEnd').toggle(!membersState.loading && !membersState.hasMore && listCount > 0);
        }

        function fetchMembers(reset) {
            if (membersState.loading) return;
            if (!membersState.hasMore && !reset) return;

            if (reset) {
                membersState.page = 1;
                membersState.hasMore = true;
                jQuery('#communityMembersList').empty();
            }

            membersState.loading = true;
            setMembersUiState();

            jQuery.get('{{ route('community.members-data', $community->id) }}', {
                page: membersState.page,
                per_page: membersState.perPage,
                q: membersState.query
            }).done(function (res) {
                var items = Array.isArray(res.items) ? res.items : [];
                membersState.hasMore = !!res.has_more;
                if (typeof res.is_community_admin !== 'undefined') {
                    membersState.isCommunityAdmin = !!res.is_community_admin;
                }
                if (items.length > 0) {
                    var html = items.map(memberItemHtml).join('');
                    jQuery('#communityMembersList').append(html);
                }
                if (membersState.hasMore) {
                    membersState.page += 1;
                }
            }).fail(function () {
                // keep current items on failure
            }).always(function () {
                membersState.loading = false;
                setMembersUiState();
            });
        }

        var inviteForm = document.getElementById('inviteColleaguesForm');
        if (inviteForm) {
            inviteForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(inviteForm);
                fetch('{{ route('community.invite', $community->id) }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: fd
                }).then(r => r.json()).then(function (res) {
                    alert(res.message || 'Done');
                    if (res.status === 'success') location.reload();
                }).catch(function () { alert('Failed to send invitations.'); });
            });
        }

        jQuery(document).on('click', '.js-member-toggle', function () {
            var fd = new FormData();
            fd.append('member_id', this.dataset.memberId);
            fd.append('action', this.dataset.action);
            fetch('{{ route('community.member-status', $community->id) }}', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                body: fd
            }).then(r => r.json()).then(function (res) {
                alert(res.message || 'Done');
                if (res.status === 'success') location.reload();
            }).catch(function () { alert('Failed to update member status.'); });
        });

        var eventForm = document.getElementById('createCommunityEventForm');
        if (eventForm) {
            eventForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(eventForm);
                fetch('{{ route('community.events.create', $community->id) }}', {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: fd
                }).then(r => r.json()).then(function (res) {
                    alert(res.message || 'Done');
                    if (res.status === 'success') location.reload();
                }).catch(function () { alert('Failed to create event.'); });
            });
        }

        var searchTimer = null;
        jQuery('#memberSearchInput').on('input', function () {
            var val = jQuery(this).val() || '';
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                membersState.query = val.trim();
                fetchMembers(true);
            }, 300);
        });

        var sentinel = document.getElementById('communityMembersSentinel');
        if (sentinel && 'IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        fetchMembers(false);
                    }
                });
            }, { root: null, rootMargin: '120px 0px', threshold: 0.01 });
            observer.observe(sentinel);
        }

        fetchMembers(true);

    })();
</script>
@include('common.attachment_js')
@include('communities.partials.community_comment_scripts')
@else
<script>
    (function () {
        var communityId = {{ (int) $community->id }};
        $('.join-community-detail-btn').on('click', function () {
            $('#joinCommunityDetailModal').modal('show');
        });
        $('#confirmJoinCommunityDetail').on('click', function () {
            $.ajax({
                url: '{{ route('community.join') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function (response) {
                    $('#joinCommunityDetailModal').modal('hide');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        alert(response.message || 'Request submitted.');
                        window.location.reload();
                    }
                },
                error: function (xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Could not submit join request.';
                    alert(msg);
                }
            });
        });
    })();
</script>
@endif
@include('publications.partials.preview_modal')
@include('partials.publications.publication_feed_card_scripts')
@endsection

