@php
    $primary = settings()->primary_color ?? '#119A48';
    $user = $author->user ?? null;
    $isOrganisation = strtolower((string) ($author->is_organsiation ?? '')) === 'yes';
    $avatarUrl = null;

    if (! empty($author->logo) && $author->logo !== 'author.png') {
        $avatarUrl = filter_var($author->logo, FILTER_VALIDATE_URL)
            ? $author->logo
            : asset(ltrim($author->logo, '/'));
    } elseif ($user && ! empty($user->photo)) {
        $avatarUrl = $user->photo;
    } elseif (! empty($author->icon) && str_contains((string) $author->icon, '/')) {
        $avatarUrl = filter_var($author->icon, FILTER_VALIDATE_URL)
            ? $author->icon
            : asset(ltrim($author->icon, '/'));
    }

    $jobTitle = $user->job_title ?? null;
    $organization = $contributorOrganization ?? contributor_profile_organization($author, $user);
    $countryName = $user->country->name ?? null;
    $stats = $contributionStats ?? [];
    $profileKicker = ($isOrganisation && ! $user) ? 'Contributing Organisation' : 'Contributor';
    $lifetimeBadge = $lifetimeBadge ?? ($user->lifetimeBadge ?? null);
@endphp

<style>
    .contributor-hero {
        background: linear-gradient(135deg, rgba(17, 154, 72, 0.08) 0%, #ffffff 55%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.75rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
    }
    .contributor-hero-inner {
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
    }
    .contributor-avatar {
        width: 112px;
        height: 112px;
        border-radius: 16px;
        flex-shrink: 0;
        overflow: hidden;
        border: 3px solid #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .contributor-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .contributor-avatar-icon {
        font-size: 2.5rem;
        color: {{ $primary }};
    }
    .contributor-hero-body {
        flex: 1;
        min-width: 0;
    }
    .contributor-kicker {
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: {{ $primary }};
        margin-bottom: 0.35rem;
    }
    .contributor-name {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin: 0 0 0.5rem;
        word-wrap: break-word;
    }
    .contributor-name a {
        color: inherit;
        text-decoration: none;
    }
    .contributor-name a:hover {
        color: {{ $primary }};
    }
    .contributor-title {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .contributor-meta-line {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem 1rem;
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 0.75rem;
    }
    .contributor-meta-line i {
        color: {{ $primary }};
        margin-right: 0.35rem;
    }
    .contributor-org-block {
        margin-bottom: 0.75rem;
    }
    .contributor-org-label {
        display: block;
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 0.2rem;
    }
    .contributor-org-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.4;
    }
    .contributor-participant-badges {
        margin-top: 0.85rem;
    }
    .contributor-participant-badges__title {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 0.55rem;
    }
    .contributor-participant-badges__grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }
    .participant-badge-card {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: 0;
        max-width: 100%;
        padding: 0.45rem 0.65rem;
        border-radius: 10px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        background: #fff;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.05);
    }
    .participant-badge-card__icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .participant-badge-card__icon img {
        width: 24px;
        height: 24px;
        object-fit: contain;
    }
    .participant-badge-card__body {
        min-width: 0;
    }
    .participant-badge-card__name {
        font-size: 0.78rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
    }
    .participant-badge-card__meta {
        font-size: 0.68rem;
        color: #64748b;
        line-height: 1.3;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }
    .contributor-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.75rem;
    }
    .contributor-stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.1rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 92px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .contributor-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        border-color: rgba(17, 154, 72, 0.25);
    }
    .contributor-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
    }
    .contributor-stat-card--total .contributor-stat-icon {
        background: rgba(17, 154, 72, 0.12);
        color: {{ $primary }};
    }
    .contributor-stat-card--resources .contributor-stat-icon {
        background: rgba(37, 99, 235, 0.12);
        color: #2563eb;
    }
    .contributor-stat-card--threads .contributor-stat-icon {
        background: rgba(124, 58, 237, 0.12);
        color: #7c3aed;
    }
    .contributor-stat-card--comments .contributor-stat-icon {
        background: rgba(234, 88, 12, 0.12);
        color: #ea580c;
    }
    .contributor-stat-value {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 0.2rem;
    }
    .contributor-stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.3;
    }
    .contributor-section-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(17, 154, 72, 0.15);
    }
    @media (max-width: 991.98px) {
        .contributor-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 575.98px) {
        .contributor-hero-inner {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .contributor-meta-line,
        .contributor-lifetime-badge-wrap {
            text-align: center;
        }
        .contributor-name {
            font-size: 1.4rem;
        }
        .contributor-stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contributor-hero">
    <div class="contributor-hero-inner">
        <div class="contributor-avatar" aria-hidden="true">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ $author->name }}" onerror="this.style.display='none'; this.parentElement.querySelector('.contributor-avatar-fallback')?.classList.remove('d-none');">
                <i class="contributor-avatar-icon contributor-avatar-fallback d-none {{ $isOrganisation ? 'fa fa-building' : 'fa fa-user' }}"></i>
            @else
                <i class="contributor-avatar-icon {{ $isOrganisation ? 'fa fa-building' : 'fa fa-user' }}"></i>
            @endif
        </div>
        <div class="contributor-hero-body">
            <div class="contributor-kicker">{{ $profileKicker }}</div>
            <h1 class="contributor-name">
                @if(!empty($author->orcid))
                    <a href="https://orcid.org/{{ $author->orcid }}" target="_blank" rel="noopener noreferrer" title="View ORCID profile">
                        {{ $author->name }}
                        <i class="fa fa-external-link-alt" style="font-size: 0.55em; margin-left: 0.35rem;"></i>
                    </a>
                @else
                    {{ $author->name }}
                @endif
            </h1>
            @if($jobTitle)
                <div class="contributor-title">{{ $jobTitle }}</div>
            @endif
            @if($organization)
                <div class="contributor-org-block">
                    <span class="contributor-org-label">Organization / Institution</span>
                    <div class="contributor-org-value"><i class="fa fa-building mr-1" style="color: {{ $primary }};"></i>{{ $organization }}</div>
                </div>
            @endif
            <div class="contributor-meta-line">
                @if($countryName)
                    <span><i class="fa fa-map-marker-alt"></i>{{ $countryName }}</span>
                @endif
                @if(!empty($author->email))
                    <span><i class="fa fa-envelope"></i>{{ $author->email }}</span>
                @endif
            </div>
            @include('publications.partials.contributor_lifetime_badge', [
                'author' => $author,
                'lifetimeBadge' => $lifetimeBadge,
                'badgeDrilldownYear' => $badgeDrilldownYear ?? null,
                'badgeDrilldownMonth' => $badgeDrilldownMonth ?? null,
                'communityBadgeStarCount' => $communityBadgeStarCount ?? 0,
            ])
        </div>
    </div>
</div>

<div class="contributor-stats-grid" aria-label="Contributor impact statistics">
    <div class="contributor-stat-card contributor-stat-card--total">
        <div class="contributor-stat-icon"><i class="fa fa-chart-line"></i></div>
        <div>
            <div class="contributor-stat-value">{{ number_format((int) ($stats['total_contributions'] ?? 0)) }}</div>
            <div class="contributor-stat-label">Total contributions</div>
        </div>
    </div>
    <div class="contributor-stat-card contributor-stat-card--resources">
        <div class="contributor-stat-icon"><i class="fa fa-book"></i></div>
        <div>
            <div class="contributor-stat-value">{{ number_format((int) ($stats['resource_contributions'] ?? 0)) }}</div>
            <div class="contributor-stat-label">Published resources</div>
        </div>
    </div>
    <div class="contributor-stat-card contributor-stat-card--threads">
        <div class="contributor-stat-icon"><i class="fa fa-comments"></i></div>
        <div>
            <div class="contributor-stat-value">{{ number_format((int) ($stats['forum_posts'] ?? 0)) }}</div>
            <div class="contributor-stat-label">Forum threads started</div>
        </div>
    </div>
    <div class="contributor-stat-card contributor-stat-card--comments">
        <div class="contributor-stat-icon"><i class="fa fa-reply"></i></div>
        <div>
            <div class="contributor-stat-value">{{ number_format((int) ($stats['forum_comments'] ?? 0)) }}</div>
            <div class="contributor-stat-label">Forum comments</div>
        </div>
    </div>
</div>
