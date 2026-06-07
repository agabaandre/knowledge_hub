@php
    $pinned = $pinned ?? false;
    $detailUrl = community_detail_url($community);
    $descPlain = strip_tags($community->description ?? '');
    $lastAt = $community->listing_last_activity ?? null;
    $faces = $community->listing_contributor_faces ?? collect();
    $moreMembers = (int) ($community->listing_more_members_not_shown ?? 0);
    $roleLabel = function (string $role) {
        if ($role === 'creator') {
            return 'Community creator';
        }
        if ($role === 'admin') {
            return 'Community admin';
        }
        if ($role === 'contributor') {
            return 'Contributor';
        }

        return 'Member';
    };
    $canEnterCommunity = Auth::check() && ($community->user_joined ?? false);
@endphp
<div class="community-room-card {{ $canEnterCommunity ? 'community-room-card--clickable' : '' }}"
    @if($canEnterCommunity)
        onclick="window.location.href='{{ $detailUrl }}'"
        role="link"
        tabindex="0"
        onkeydown="if(event.key==='Enter'){window.location.href='{{ $detailUrl }}'}"
    @endif
>
    <div class="community-room-card__head">
        <div class="community-room-card__title-row">
            @if($pinned)
                <span class="community-room-card__pin" title="Recommended for you"><i class="fa fa-thumb-tack" aria-hidden="true"></i></span>
            @endif
            <h2 class="community-room-card__title">
                <a href="{{ $detailUrl }}" class="community-room-card__title-link notranslate" translate="no" onclick="event.stopPropagation();">{{ $community->community_name }}</a>
            </h2>
            <span class="community-room-card__star" title="Community"><i class="fa fa-star-o" aria-hidden="true"></i></span>
        </div>
        <div class="community-room-card__badges">
            @if(isset($community->is_public) && $community->is_public)
                <span class="community-room-card__badge community-room-card__badge--access">Open to all users</span>
            @else
                <span class="community-room-card__badge community-room-card__badge--private">Members only</span>
            @endif
            @if($lastAt)
                <span class="community-room-card__badge community-room-card__badge--activity">Last activity {{ $lastAt->diffForHumans() }}</span>
            @elseif($community->created_at)
                <span class="community-room-card__badge community-room-card__badge--activity">Created {{ $community->created_at->diffForHumans() }}</span>
            @else
                <span class="community-room-card__badge community-room-card__badge--activity">Created date not available</span>
            @endif
        </div>
    </div>

    <p class="community-room-card__desc">{{ Str::limit($descPlain, 156) }}</p>

    @php
        $coverageBits = [];
        if (!$community->region_id && !$community->country_id) {
            $coverageBits[] = 'Whole of Africa';
        } elseif ($community->region_id && !$community->country_id) {
            $coverageBits[] = $community->region->region_name ?? 'Region';
        } elseif ($community->country_id) {
            $coverageBits[] = $community->country->name ?? 'Country';
        }
    @endphp
    @if(count($coverageBits))
        <p class="community-room-card__coverage notranslate" translate="no"><i class="fa fa-globe"></i> {{ $coverageBits[0] }}</p>
    @endif

    @if($faces->isNotEmpty() || $moreMembers > 0)
    <div class="community-room-card__avatar-carousel" onclick="event.stopPropagation();" role="region" aria-label="Contributors and members">
        <button type="button" class="community-room-card__avatar-nav community-room-card__avatar-nav--prev" aria-label="Scroll contributors left">
            <i class="fa fa-chevron-left" aria-hidden="true"></i>
        </button>
        <div class="community-room-card__avatar-track">
            <div class="community-room-card__avatar-slides">
                @foreach($faces as $face)
                    @php
                        $u = $face['user'];
                        $showImg = community_user_has_profile_image($u);
                        $jobTitle = community_user_display_job_title($u);
                        $profileUrl = user_author_publications_url($u);
                        $hoverTip = $u->name;
                        if ($jobTitle !== '') {
                            $hoverTip .= ' — ' . $jobTitle;
                        }
                        $hoverTip .= ' · ' . $roleLabel($face['role']);
                        if ($profileUrl) {
                            $hoverTip .= ' · View profile';
                        }
                        $avatarTag = $profileUrl ? 'a' : 'span';
                        $avatarAttrs = $profileUrl
                            ? 'href="' . e($profileUrl) . '" title="' . e($hoverTip) . '" aria-label="' . e('View profile: ' . $u->name) . '"'
                            : 'title="' . e($hoverTip) . '"';
                    @endphp
                    <{{ $avatarTag }} {!! $avatarAttrs !!} class="community-room-card__avatar-wrap notranslate {{ $profileUrl ? 'community-room-card__avatar-wrap--linked' : '' }} {{ !empty($face['online']) ? 'community-room-card__avatar-wrap--online' : '' }}" translate="no">
                        @if($showImg)
                            <img src="{{ $u->photo }}" alt="" class="community-room-card__avatar" loading="lazy" width="32" height="32" decoding="async"
                                onerror="this.style.display='none';var el=this.nextElementSibling;if(el){el.style.display='flex';}">
                            <span class="community-room-card__avatar-initials community-room-card__avatar-initials--fallback notranslate" style="display:none" aria-hidden="true" translate="no">{{ community_user_initials($u->name) }}</span>
                        @else
                            <span class="community-room-card__avatar-initials notranslate" translate="no">{{ community_user_initials($u->name) }}</span>
                        @endif
                    </{{ $avatarTag }}>
                @endforeach
                @if($moreMembers > 0)
                    <span class="community-room-card__more-members">+{{ $moreMembers }} more</span>
                @endif
            </div>
        </div>
        <button type="button" class="community-room-card__avatar-nav community-room-card__avatar-nav--next" aria-label="Scroll contributors right">
            <i class="fa fa-chevron-right" aria-hidden="true"></i>
        </button>
    </div>
    @endif

    <div class="community-room-card__footer">
        <a href="{{ $detailUrl }}" class="community-room-card__more-link" onclick="event.stopPropagation();">More info</a>
        <span class="community-room-card__stats">
            <i class="fa fa-comments" aria-hidden="true"></i>
            <span>{{ number_format((int) ($community->forums_count ?? 0)) }}</span>
            <span class="community-room-card__stats-sep">·</span>
            <i class="fa fa-book" aria-hidden="true"></i>
            <span>{{ number_format((int) ($community->publications_count ?? 0)) }}</span>
            <span class="community-room-card__stats-sep">·</span>
            <i class="fa fa-users" aria-hidden="true"></i>
            <span>{{ number_format((int) ($community->members_count ?? 0)) }}</span>
        </span>
    </div>

    <div class="community-room-card__actions" onclick="event.stopPropagation();">
        @if (Auth::check())
            @if (!$community->user_joined && !$community->user_pending_approval)
                <button type="button" class="btn btn-sm community-room-card__btn-join join-btn" data-community-id="{{ $community->id }}" data-detail-url="{{ $detailUrl }}">Join</button>
            @elseif ($community->user_pending_approval)
                <button type="button" class="btn btn-sm btn-warning" disabled>Pending</button>
            @else
                <a href="{{ $detailUrl }}" class="btn btn-sm community-room-card__btn-primary">Visit</a>
                @if(request()->routeIs('account.my-communities'))
                    <a href="{{ url('/records') }}?community_id={{ $community->id }}" class="btn btn-sm btn-outline-secondary">Resources</a>
                    <a href="{{ url('/forums') }}?community_id={{ $community->id }}" class="btn btn-sm btn-outline-secondary">Forums</a>
                    <button type="button" class="btn btn-sm btn-outline-danger leave-btn" data-community-id="{{ $community->id }}">Leave</button>
                @else
                    <button type="button" class="btn btn-sm btn-outline-danger leave-btn" data-community-id="{{ $community->id }}">Leave</button>
                @endif
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-sm community-room-card__btn-join">Log in to join</a>
        @endif
    </div>
</div>
