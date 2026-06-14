@php
    $pinned = $pinned ?? false;
    $detailUrl = community_detail_url($community);
    $descPlain = strip_tags($community->description ?? '');
    $cardsPerRow = communities_listing_cards_per_row();
    $descCharLimit = communities_listing_description_char_limit($cardsPerRow);
    $descLineClamp = communities_listing_description_line_clamp($cardsPerRow);
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
    $coverageBits = [];
    if (!$community->region_id && !$community->country_id) {
        $coverageBits[] = 'Whole of Africa';
    } elseif ($community->region_id && !$community->country_id) {
        $coverageBits[] = $community->region->region_name ?? 'Region';
    } elseif ($community->country_id) {
        $coverageBits[] = $community->country->name ?? 'Country';
    }
@endphp
<article class="community-room-card {{ $canEnterCommunity ? 'community-room-card--clickable' : '' }} {{ $pinned ? 'community-room-card--pinned' : '' }}"
    @if($canEnterCommunity)
        onclick="window.location.href='{{ $detailUrl }}'"
        role="link"
        tabindex="0"
        onkeydown="if(event.key==='Enter'){window.location.href='{{ $detailUrl }}'}"
    @endif
>
    @if($pinned)
        <span class="community-room-card__recommended" title="Recommended for you">
            <i class="fa fa-thumb-tack" aria-hidden="true"></i> Recommended
        </span>
    @endif

    <header class="community-room-card__head">
        <div class="community-room-card__title-row">
            <span class="community-room-card__brand" aria-hidden="true">
                <i class="fa fa-users"></i>
            </span>
            <h2 class="community-room-card__title">
                <a href="{{ $detailUrl }}" class="community-room-card__title-link notranslate" translate="no" onclick="event.stopPropagation();">{{ $community->community_name }}</a>
            </h2>
        </div>
        <div class="community-room-card__badges">
            @if(isset($community->is_public) && $community->is_public)
                <span class="community-room-card__badge community-room-card__badge--access">
                    <i class="fa fa-unlock-alt" aria-hidden="true"></i> Open to all
                </span>
            @else
                <span class="community-room-card__badge community-room-card__badge--private">
                    <i class="fa fa-lock" aria-hidden="true"></i> Members only
                </span>
            @endif
            @if($lastAt)
                <span class="community-room-card__badge community-room-card__badge--activity">
                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{ $lastAt->diffForHumans() }}
                </span>
            @elseif($community->created_at)
                <span class="community-room-card__badge community-room-card__badge--activity">
                    <i class="fa fa-calendar-o" aria-hidden="true"></i> {{ $community->created_at->diffForHumans() }}
                </span>
            @endif
            @if(count($coverageBits))
                <span class="community-room-card__badge community-room-card__badge--coverage notranslate" translate="no">
                    <i class="fa fa-globe" aria-hidden="true"></i> {{ $coverageBits[0] }}
                </span>
            @endif
        </div>
    </header>

    @if($descPlain !== '')
    <p class="community-room-card__desc community-room-card__desc--cols-{{ $cardsPerRow }}"
       style="-webkit-line-clamp: {{ $descLineClamp }}; max-height: calc(1.45em * {{ $descLineClamp }});">
        {{ Str::limit($descPlain, $descCharLimit) }}
    </p>
    @endif

    @if($faces->isNotEmpty() || $moreMembers > 0)
    <div class="community-room-card__members" onclick="event.stopPropagation();">
        <span class="community-room-card__members-label">Members</span>
        <div class="community-room-card__avatar-carousel" role="region" aria-label="Contributors and members">
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
                        <span class="community-room-card__more-members">+{{ $moreMembers }}</span>
                    @endif
                </div>
            </div>
            <button type="button" class="community-room-card__avatar-nav community-room-card__avatar-nav--next" aria-label="Scroll contributors right">
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    @endif

    <div class="community-room-card__metrics">
        <span class="community-room-card__metric" title="Forum discussions">
            <i class="fa fa-comments" aria-hidden="true"></i>
            <strong>{{ number_format((int) ($community->forums_count ?? 0)) }}</strong>
            <span>Forums</span>
        </span>
        <span class="community-room-card__metric" title="Publications">
            <i class="fa fa-book" aria-hidden="true"></i>
            <strong>{{ number_format((int) ($community->publications_count ?? 0)) }}</strong>
            <span>Resources</span>
        </span>
        <span class="community-room-card__metric" title="Members">
            <i class="fa fa-users" aria-hidden="true"></i>
            <strong>{{ number_format((int) ($community->members_count ?? 0)) }}</strong>
            <span>Members</span>
        </span>
    </div>

    <footer class="community-room-card__footer">
        <a href="{{ $detailUrl }}" class="community-room-card__more-link" onclick="event.stopPropagation();">
            View details <i class="fa fa-arrow-right" aria-hidden="true"></i>
        </a>
        <div class="community-room-card__actions" onclick="event.stopPropagation();">
            @if (Auth::check())
                @if (!$community->user_joined && !$community->user_pending_approval)
                    <button type="button" class="btn btn-sm community-room-card__btn-join join-btn" data-community-id="{{ $community->id }}" data-detail-url="{{ $detailUrl }}">Join</button>
                @elseif ($community->user_pending_approval)
                    <button type="button" class="btn btn-sm btn-warning community-room-card__btn-pending" disabled>Pending</button>
                @else
                    <a href="{{ $detailUrl }}" class="btn btn-sm community-room-card__btn-primary">Visit</a>
                    @if(request()->routeIs('account.my-communities'))
                        <a href="{{ url('/records') }}?community_id={{ $community->id }}" class="btn btn-sm community-room-card__btn-secondary">Resources</a>
                        <a href="{{ url('/forums') }}?community_id={{ $community->id }}" class="btn btn-sm community-room-card__btn-secondary">Forums</a>
                        <button type="button" class="btn btn-sm community-room-card__btn-leave leave-btn" data-community-id="{{ $community->id }}">Leave</button>
                    @else
                        <button type="button" class="btn btn-sm community-room-card__btn-leave leave-btn" data-community-id="{{ $community->id }}">Leave</button>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-sm community-room-card__btn-join">Log in to join</a>
            @endif
        </div>
    </footer>
</article>
