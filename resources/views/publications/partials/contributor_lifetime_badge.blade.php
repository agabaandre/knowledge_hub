@php
    $lifetimeBadge = $lifetimeBadge ?? null;
    $badgeType = $lifetimeBadge->badgeType ?? null;
    $user = $author->user ?? null;
    $year = (int) ($badgeDrilldownYear ?? now()->subMonth()->year);
    $month = (int) ($badgeDrilldownMonth ?? now()->subMonth()->month);
    $starCount = (int) ($communityBadgeStarCount ?? 0);
    $periodLabel = \Carbon\Carbon::create($year, $month, 1)->format('F Y');
    $badgeSlug = $badgeType->slug ?? null;
    $badgeColor = $badgeType->badge_color ?? '#64748b';
    $communitiesUrl = $author->slug
        ? route('authors.badge-communities', $author->slug)
        : url('authors/publications/badge-communities').'?author='.$author->id;
    $badgeAcquiredAt = $lifetimeBadge->last_upgraded_at ?? $lifetimeBadge->created_at ?? null;
@endphp
@if($lifetimeBadge && $badgeType && $user)
<div class="contributor-lifetime-badge-wrap">
    <div class="contributor-participant-badges__title">Contributor badge</div>
    <button type="button"
        class="contributor-lifetime-badge-btn"
        id="contributorLifetimeBadgeBtn"
        data-url="{{ $communitiesUrl }}"
        data-year="{{ $year }}"
        data-month="{{ $month }}"
        aria-haspopup="dialog">
        <span class="contributor-lifetime-badge-btn__icon" style="background: {{ $badgeColor }}22; border-color: {{ $badgeColor }}55;">
            @if(!empty($badgeType->image_path))
                <img src="{{ asset($badgeType->image_path) }}" alt="">
            @else
                {{ participant_badge_emoji($badgeSlug) }}
            @endif
            @if($starCount > 0)
                <span class="contributor-lifetime-badge-btn__stars" title="{{ $starCount }} {{ Str::plural('community', $starCount) }} in {{ $periodLabel }}">
                    <i class="fa fa-star" aria-hidden="true"></i> {{ $starCount }}
                </span>
            @endif
        </span>
        <span class="contributor-lifetime-badge-btn__body">
            <span class="contributor-lifetime-badge-btn__name">{{ $badgeType->name }}</span>
            <span class="contributor-lifetime-badge-btn__meta">
                {{ number_format((int) $lifetimeBadge->lifetime_contributions) }} lifetime contributions
                @if($starCount > 0)
                    · {{ $starCount }} {{ Str::plural('community', $starCount) }} in {{ $periodLabel }}
                @endif
            </span>
            @if($badgeAcquiredAt)
                <span class="contributor-lifetime-badge-btn__acquired">
                    <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                    Acquired {{ $badgeAcquiredAt->format('M j, Y') }}
                    <span class="contributor-lifetime-badge-btn__acquired-relative">({{ $badgeAcquiredAt->diffForHumans() }})</span>
                </span>
            @endif
            <span class="contributor-lifetime-badge-btn__hint">Click for community breakdown</span>
        </span>
    </button>
</div>

<div class="modal fade" id="contributorCommunityBadgeModal" tabindex="-1" role="dialog" aria-labelledby="contributorCommunityBadgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contributorCommunityBadgeModalLabel">
                    {{ $badgeType->name }}
                    @if($badgeAcquiredAt)
                        <span class="d-block text-muted small font-weight-normal mt-1">Acquired {{ $badgeAcquiredAt->format('F j, Y') }}</span>
                    @endif
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3" id="contributorCommunityBadgePeriod">Loading…</p>
                <div id="contributorCommunityBadgeLoader" class="text-center text-muted py-3">Loading community activity…</div>
                <ul class="list-group list-group-flush d-none" id="contributorCommunityBadgeList"></ul>
                <p class="text-muted small mb-0 d-none" id="contributorCommunityBadgeEmpty">No community-scoped contributions recorded for this month.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .contributor-lifetime-badge-wrap { margin-top: 0.85rem; }
    .contributor-lifetime-badge-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        text-align: left;
        padding: 0.65rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
        cursor: pointer;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .contributor-lifetime-badge-btn:hover,
    .contributor-lifetime-badge-btn:focus {
        border-color: rgba(17, 154, 72, 0.35);
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        outline: none;
    }
    .contributor-lifetime-badge-btn__icon {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 12px;
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .contributor-lifetime-badge-btn__icon img {
        width: 36px;
        height: 36px;
        object-fit: contain;
    }
    .contributor-lifetime-badge-btn__stars {
        position: absolute;
        bottom: -4px;
        right: -6px;
        background: #0f172a;
        color: #fbbf24;
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 1;
        padding: 0.2rem 0.35rem;
        border-radius: 999px;
        border: 2px solid #fff;
        white-space: nowrap;
    }
    .contributor-lifetime-badge-btn__name {
        display: block;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }
    .contributor-lifetime-badge-btn__meta {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.15rem;
    }
    .contributor-lifetime-badge-btn__acquired {
        display: block;
        font-size: 0.75rem;
        color: #475569;
        margin-top: 0.3rem;
        font-weight: 500;
    }
    .contributor-lifetime-badge-btn__acquired-relative {
        color: #94a3b8;
        font-weight: 400;
    }
    .contributor-lifetime-badge-btn__hint {
        display: block;
        font-size: 0.72rem;
        color: {{ settings()->primary_color ?? '#119A48' }};
        margin-top: 0.25rem;
        font-weight: 600;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('contributorLifetimeBadgeBtn');
    if (!btn || typeof jQuery === 'undefined') return;

    btn.addEventListener('click', function () {
        var url = btn.getAttribute('data-url');
        var year = btn.getAttribute('data-year');
        var month = btn.getAttribute('data-month');
        var $modal = jQuery('#contributorCommunityBadgeModal');
        var $list = jQuery('#contributorCommunityBadgeList');
        var $loader = jQuery('#contributorCommunityBadgeLoader');
        var $empty = jQuery('#contributorCommunityBadgeEmpty');
        var $period = jQuery('#contributorCommunityBadgePeriod');

        $list.addClass('d-none').empty();
        $empty.addClass('d-none');
        $loader.removeClass('d-none');
        $period.text('Loading…');
        $modal.modal('show');

        jQuery.get(url, { year: year, month: month })
            .done(function (data) {
                $loader.addClass('d-none');
                $period.text('Community-scoped contributions in ' + (data.period_label || ''));

                if (!data.communities || !data.communities.length) {
                    $empty.removeClass('d-none');
                    return;
                }

                data.communities.forEach(function (row) {
                    var name = row.community_name || 'Community';
                    var link = row.community_url
                        ? '<a href="' + row.community_url + '">' + name + '</a>'
                        : name;
                    $list.append(
                        '<li class="list-group-item d-flex justify-content-between align-items-center px-0">' +
                        '<span>' + link + '</span>' +
                        '<span class="badge badge-primary badge-pill">' + row.contributions_count + '</span>' +
                        '</li>'
                    );
                });
                $list.removeClass('d-none');
            })
            .fail(function () {
                $loader.addClass('d-none');
                $period.text('Could not load community contributions.');
                $empty.removeClass('d-none').text('Unable to load community breakdown. Please try again later.');
            });
    });
});
</script>
@endif
