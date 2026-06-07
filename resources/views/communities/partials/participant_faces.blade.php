@php
    $faces = $faces ?? collect();
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
@endphp
@if($faces->isNotEmpty())
<div class="community-detail-participants" role="list" aria-label="Featured participants">
    <div class="community-detail-participants__label"><i class="fa fa-users mr-1"></i>Featured participants</div>
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
                ? 'href="' . e($profileUrl) . '" title="' . e($hoverTip) . '" aria-label="' . e('View profile: ' . $u->name) . '" role="listitem"'
                : 'title="' . e($hoverTip) . '" role="listitem"';
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
</div>
@endif
