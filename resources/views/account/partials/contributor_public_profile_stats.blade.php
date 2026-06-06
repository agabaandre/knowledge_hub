@php
    $contributorPublicProfile = $contributorPublicProfile ?? null;
    $profileUrl = $contributorPublicProfile['public_profile_url'] ?? null;
    $author = $contributorPublicProfile['author'] ?? null;
    $stats = $contributorPublicProfile['contribution_stats'] ?? [];
    $badgeCount = (int) ($contributorPublicProfile['badge_count'] ?? 0);
    $primary = settings()->primary_color ?? '#119A48';
@endphp
@if($profileUrl && $author)
<div class="col-12 mb-3">
    <div class="card account-contributor-card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <div class="account-contributor-card__kicker">Your public contributor profile</div>
                    <h2 class="account-contributor-card__title mb-1">{{ $author->name }}</h2>
                    <p class="text-muted small mb-0">These figures match what visitors see on your public profile.</p>
                </div>
                <a href="{{ $profileUrl }}" class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer">
                    <i class="fa fa-external-link-alt mr-1"></i> View public profile
                </a>
            </div>
            <div class="account-contributor-stats">
                <div class="account-contributor-stat">
                    <div class="account-contributor-stat__value">{{ number_format((int) ($stats['total_contributions'] ?? 0)) }}</div>
                    <div class="account-contributor-stat__label">Total contributions</div>
                </div>
                <div class="account-contributor-stat">
                    <div class="account-contributor-stat__value">{{ number_format((int) ($stats['resource_contributions'] ?? 0)) }}</div>
                    <div class="account-contributor-stat__label">Published resources</div>
                </div>
                <div class="account-contributor-stat">
                    <div class="account-contributor-stat__value">{{ number_format((int) ($stats['forum_posts'] ?? 0)) }}</div>
                    <div class="account-contributor-stat__label">Forum threads started</div>
                </div>
                <div class="account-contributor-stat">
                    <div class="account-contributor-stat__value">{{ number_format((int) ($stats['forum_comments'] ?? 0)) }}</div>
                    <div class="account-contributor-stat__label">Forum comments</div>
                </div>
                @if($badgeCount > 0)
                <div class="account-contributor-stat">
                    <div class="account-contributor-stat__value">{{ optional($contributorPublicProfile['lifetime_badge'] ?? null)->badgeType->name ?? '1' }}</div>
                    <div class="account-contributor-stat__label">Lifetime badge tier</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<style>
    .account-contributor-card {
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(17, 154, 72, 0.06) 0%, #ffffff 55%, #f8fafc 100%);
        border: 1px solid #e2e8f0 !important;
    }
    .account-contributor-card__kicker {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: {{ $primary }};
        margin-bottom: 0.35rem;
    }
    .account-contributor-card__title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
    }
    .account-contributor-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.85rem;
    }
    .account-contributor-stat {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.9rem 0.85rem;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
    }
    .account-contributor-stat__value {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
        margin-bottom: 0.25rem;
    }
    .account-contributor-stat__label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.3;
    }
</style>
@endif
