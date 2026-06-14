@php
    $faces = collect($forum->listing_contributor_faces ?? []);
    $moreContributors = (int) ($forum->listing_more_contributors_not_shown ?? 0);
    if ($faces->isEmpty() && $moreContributors <= 0) {
        return;
    }
    $roleLabel = function (string $role) {
        if ($role === 'author') {
            return 'Thread author';
        }
        if ($role === 'contributor') {
            return 'Contributor';
        }

        return 'Participant';
    };
@endphp
<div class="community-room-card__avatar-carousel forum-card__contributors" onclick="event.stopPropagation();" role="region" aria-label="Discussion contributors">
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
            @if($moreContributors > 0)
                <span class="community-room-card__more-members">+{{ $moreContributors }} more</span>
            @endif
        </div>
    </div>
    <button type="button" class="community-room-card__avatar-nav community-room-card__avatar-nav--next" aria-label="Scroll contributors right">
        <i class="fa fa-chevron-right" aria-hidden="true"></i>
    </button>
</div>
