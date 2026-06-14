@php
    $primaryColor = settings()->primary_color ?? '#119A48';
@endphp

<aside class="community-detail-sidebar" aria-label="Community sidebar">
    <div class="community-detail-sidebar-card">
        <div class="community-detail-sidebar-card__head community-detail-sidebar-card__head--forums">
            <span class="community-detail-sidebar-card__icon community-detail-sidebar-card__icon--blue" aria-hidden="true">
                <i class="fa fa-comments"></i>
            </span>
            <div>
                <h2 class="community-detail-sidebar-card__title">Recent forums</h2>
                <p class="community-detail-sidebar-card__hint">Discussions linked to this community.</p>
            </div>
        </div>
        <div class="community-detail-sidebar-card__body">
            @if($forums->count() > 0)
                @foreach($forums as $forum)
                    <a href="{{ forum_thread_url($forum) }}" class="community-sidebar-forum-item">
                        @if(!empty($forum->forum_image) && is_image($forum->forum_image))
                            <img class="community-sidebar-forum-item__thumb" src="{{ $forum->forum_image }}" alt="" loading="lazy">
                        @else
                            <span class="community-sidebar-forum-item__thumb"><i class="fa fa-comments"></i></span>
                        @endif
                        <span>
                            <span class="community-sidebar-forum-item__title notranslate" translate="no">
                                {{ Str::limit(strip_tags($forum->forum_title ?? 'Untitled'), 72) }}
                            </span>
                            <span class="community-sidebar-forum-item__meta notranslate" translate="no">
                                <i class="fa fa-user mr-1"></i>{{ $forum->user->name ?? 'Unknown' }}
                                · <i class="fa fa-clock-o mr-1"></i>{{ time_ago($forum->created_at) }}
                            </span>
                        </span>
                    </a>
                @endforeach
                <a href="{{ url('forums') }}?community_id={{ $community->id }}" class="btn btn-sm btn-outline-primary btn-block mt-2">
                    View all forums <i class="fa fa-arrow-right ml-1"></i>
                </a>
            @else
                <p class="text-muted mb-0">No forum discussions yet.</p>
            @endif
        </div>
    </div>

    @if(isset($communityEvents) && $communityEvents->count() > 0)
    <div class="community-detail-sidebar-card">
        <div class="community-detail-sidebar-card__head">
            <span class="community-detail-sidebar-card__icon community-detail-sidebar-card__icon--green" aria-hidden="true">
                <i class="fa fa-calendar"></i>
            </span>
            <div>
                <h2 class="community-detail-sidebar-card__title">Upcoming events</h2>
                <p class="community-detail-sidebar-card__hint">Community meetings and webinars.</p>
            </div>
        </div>
        <div class="community-detail-sidebar-card__body">
            @foreach($communityEvents->take(5) as $event)
                <div class="community-sidebar-community-link" style="cursor:default;">
                    <div class="community-sidebar-community-link__name">{{ $event->title }}</div>
                    <div class="community-sidebar-stats">
                        <span><i class="fa fa-clock-o mr-1"></i>{{ \Carbon\Carbon::parse($event->startdate)->format('M d, Y H:i') }}</span>
                        @if($event->venue)<span class="notranslate" translate="no"><i class="fa fa-map-marker mr-1"></i>{{ Str::limit($event->venue, 40) }}</span>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div id="community-other-communities-root" aria-live="polite"></div>

    <div class="community-detail-sidebar-card">
        <div class="community-detail-sidebar-card__head community-detail-sidebar-card__head--members">
            <span class="community-detail-sidebar-card__icon community-detail-sidebar-card__icon--purple" aria-hidden="true">
                <i class="fa fa-id-badge"></i>
            </span>
            <div>
                <h2 class="community-detail-sidebar-card__title">Community members</h2>
                <p class="community-detail-sidebar-card__hint">Search and browse active members.</p>
            </div>
        </div>
        <div class="community-detail-sidebar-card__body">
            <form id="memberSearchForm" class="mb-2" onsubmit="return false;">
                <div class="input-group input-group-sm">
                    <input type="text" id="memberSearchInput" class="form-control" placeholder="Search members...">
                    <button type="button" class="btn btn-outline-secondary"><i class="fa fa-search"></i></button>
                </div>
            </form>
            <ul id="communityMembersList" class="list-unstyled mb-2"></ul>
            <div id="communityMembersEmpty" class="text-muted mb-0" style="display:none;">No members found.</div>
            <div id="communityMembersLoader" class="text-center text-muted small py-2" style="display:none;">Loading members...</div>
            <div id="communityMembersEnd" class="text-center text-muted small py-2" style="display:none;">End of members list.</div>
            <div id="communityMembersSentinel"></div>
        </div>
    </div>

    <div class="community-detail-sidebar-card">
        <div class="community-detail-sidebar-card__head community-detail-sidebar-card__head--badges">
            <span class="community-detail-sidebar-card__icon community-detail-sidebar-card__icon--gold" aria-hidden="true">
                <i class="fa fa-trophy"></i>
            </span>
            <div>
                <h2 class="community-detail-sidebar-card__title">Contribution badges</h2>
                <p class="community-detail-sidebar-card__hint">Lifetime hub impact and monthly stars.</p>
            </div>
        </div>
        <div class="community-detail-sidebar-card__body">
            @if(isset($badgeTypes) && $badgeTypes->count() > 0)
                @foreach($badgeTypes as $badgeType)
                    <div class="mb-3 p-2 badge-type-block" style="border-left:3px solid {{ $badgeType->badge_color }};background:{{ $badgeType->badge_color }}10;border-radius:4px;">
                        <div class="d-flex align-items-center mb-1">
                            <span style="font-size:1.2em;margin-right:8px;">
                                @if($badgeType->slug === 'silver')🥈
                                @elseif($badgeType->slug === 'gold')🥇
                                @elseif($badgeType->slug === 'platinum')💎
                                @elseif($badgeType->slug === 'diamond')💠
                                @else🏅
                                @endif
                            </span>
                            <strong style="color:{{ $badgeType->badge_color }};">{{ $badgeType->name }}</strong>
                        </div>
                        <div class="small text-muted">{{ $badgeType->contribution_threshold }}+ lifetime contributions</div>
                    </div>
                @endforeach
                <div class="mt-2 p-2 badge-note-block" style="background:#f8f9fa;border-radius:4px;font-size:0.85rem;">
                    <strong>Note:</strong> Monthly community activity appears as stars on your public contributor profile.
                </div>
            @endif
        </div>
    </div>
</aside>
