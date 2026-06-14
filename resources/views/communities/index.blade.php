@extends('layouts.app')

@php
    // SEO Meta Tags (controller may override for e.g. my-communities)
    $pageTitle = $pageTitle ?? ('Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub'));
    $pageDescription = $pageDescription ?? 'Join professional communities of practice focused on public health topics across Africa. Connect with experts, share knowledge, and collaborate on health initiatives.';
    $pageKeywords = $pageKeywords ?? ('communities of practice, public health communities, Africa CDC communities, health professionals, networking, collaboration, ' . (settings()->seo_keywords ?? ''));
    $pageImage = $pageImage ?? (settings()->logo ?? asset('assets/images/logo.png'));
    $canonicalUrl = $canonicalUrl ?? url('communities');
    $ogType = $ogType ?? 'website';
@endphp


@section('structured_data')
@if(!empty($communitiesCollectionPageSchema))
<script type="application/ld+json">{!! json_encode($communitiesCollectionPageSchema, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endif
@endsection

@section('styles')
    <style>
        .theme-text { color: {{ settings()->primary_color ?? '#119A48' }}; }

        .community-room-card {
            --crc-radius: 4px;
            --crc-green: var(--theme-color-primary, #119A48);
            --crc-border: #e2e8f0;
            --crc-muted: #64748b;
            --crc-text: #0f172a;
            --crc-surface: #ffffff;
            --crc-soft: #f8fafc;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            text-align: left;
            background: var(--crc-surface);
            border: 1px solid var(--crc-border);
            border-radius: var(--crc-radius);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04), 0 4px 16px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }
        .community-room-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--crc-green) 0%, rgba(17, 154, 72, 0.35) 100%);
            opacity: 0.85;
        }
        .community-room-card:hover {
            border-color: rgba(17, 154, 72, 0.35);
            box-shadow: 0 8px 28px rgba(15, 23, 42, 0.08);
            transform: translateY(-2px);
        }
        .community-room-card--clickable { cursor: pointer; }
        .community-room-card--pinned::before {
            background: linear-gradient(90deg, var(--crc-green) 0%, #86efac 100%);
            opacity: 1;
        }
        .community-room-card__recommended {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            align-self: flex-start;
            margin: 0.85rem 1rem 0;
            padding: 0.2rem 0.55rem;
            border-radius: var(--crc-radius);
            background: #ecfdf3;
            color: var(--crc-green);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }
        .community-room-card__head {
            padding: 1rem 1rem 0.65rem;
        }
        .community-room-card__title-row {
            display: flex;
            align-items: flex-start;
            gap: 0.65rem;
            margin-bottom: 0.65rem;
        }
        .community-room-card__brand {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: var(--crc-radius);
            background: linear-gradient(135deg, #ecfdf3 0%, #f0fdf4 100%);
            border: 1px solid #d1fae5;
            color: var(--crc-green);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.95rem;
        }
        .community-room-card__title {
            flex: 1;
            margin: 0;
            min-width: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.35;
            color: var(--crc-text);
        }
        .community-room-card__title-link {
            color: inherit;
            text-decoration: none;
        }
        .community-room-card__title-link:hover {
            color: var(--crc-green);
            text-decoration: none;
        }
        .community-room-card__badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }
        .community-room-card__badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.55rem;
            border-radius: var(--crc-radius);
            font-size: 0.68rem;
            font-weight: 600;
            line-height: 1.3;
            border: 1px solid transparent;
        }
        .community-room-card__badge i { font-size: 0.62rem; opacity: 0.9; }
        .community-room-card__badge--access {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe;
        }
        .community-room-card__badge--private {
            background: #f8fafc;
            color: #475569;
            border-color: #e2e8f0;
        }
        .community-room-card__badge--activity {
            background: #f0fdf4;
            color: #166534;
            border-color: #dcfce7;
        }
        .community-room-card__badge--coverage {
            background: #faf5ff;
            color: #6b21a8;
            border-color: #f3e8ff;
        }
        .community-room-card__desc {
            margin: 0 1rem 0.75rem;
            color: #475569;
            font-size: 0.84rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .community-room-card__members {
            margin: 0 1rem 0.75rem;
            padding: 0.55rem 0.65rem;
            border: 1px solid #eef2f6;
            border-radius: var(--crc-radius);
            background: var(--crc-soft);
        }
        .community-room-card__members-label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--crc-muted);
            margin-bottom: 0.45rem;
        }
        .community-room-card__avatar-carousel {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            min-height: 2rem;
        }
        .community-room-card__avatar-nav {
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            padding: 0;
            border: 1px solid var(--crc-border);
            border-radius: var(--crc-radius);
            background: #fff;
            color: var(--crc-muted);
            font-size: 0.62rem;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s, border-color 0.15s, background 0.15s;
        }
        .community-room-card__avatar-nav:hover {
            color: var(--crc-green);
            border-color: var(--crc-green);
            background: #f0fdf4;
        }
        .community-room-card__avatar-nav:focus {
            outline: 2px solid var(--crc-green);
            outline-offset: 1px;
        }
        .community-room-card__avatar-nav[disabled],
        .community-room-card__avatar-nav.is-disabled {
            opacity: 0.35;
            pointer-events: none;
            cursor: default;
        }
        .community-room-card__avatar-track {
            flex: 1;
            min-width: 0;
            overflow-x: auto;
            overflow-y: hidden;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            overscroll-behavior-x: contain;
        }
        .community-room-card__avatar-track::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }
        .community-room-card__avatar-slides {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0.45rem;
            padding: 0.1rem 0;
            width: max-content;
            min-height: 1.75rem;
        }
        a.community-room-card__avatar-wrap {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }
        a.community-room-card__avatar-wrap:hover,
        a.community-room-card__avatar-wrap:focus-visible {
            box-shadow: 0 0 0 2px var(--crc-green);
            z-index: 3;
        }
        .community-room-card__avatar-wrap {
            position: relative;
            box-sizing: border-box;
            width: 1.82rem;
            height: 1.82rem;
            min-width: 1.82rem;
            min-height: 1.82rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #fff;
            border-radius: var(--crc-radius);
            overflow: hidden;
            flex-shrink: 0;
            background: #e2e8f0;
            isolation: isolate;
            scroll-snap-align: start;
            box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08);
        }
        .community-room-card__avatar-wrap--online::after {
            content: '';
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: 0.45rem;
            height: 0.45rem;
            background: #16a34a;
            border: 1.5px solid #fff;
            border-radius: 50%;
            z-index: 4;
            pointer-events: none;
        }
        .community-room-card__avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: var(--crc-radius);
        }
        .community-room-card__avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            font-size: 0.58rem;
            font-weight: 700;
            color: #334155;
            background: #e2e8f0;
            border-radius: var(--crc-radius);
            line-height: 1;
        }
        .community-room-card__more-members {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.82rem;
            height: 1.82rem;
            padding: 0 0.35rem;
            border-radius: var(--crc-radius);
            background: #fff;
            border: 1px solid var(--crc-border);
            font-size: 0.65rem;
            font-weight: 700;
            color: var(--crc-muted);
            white-space: nowrap;
            flex-shrink: 0;
            scroll-snap-align: start;
        }
        .community-room-card__metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.45rem;
            margin: 0 1rem 0.85rem;
        }
        .community-room-card__metric {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.1rem;
            padding: 0.45rem 0.25rem;
            border: 1px solid #eef2f6;
            border-radius: var(--crc-radius);
            background: var(--crc-soft);
            color: var(--crc-muted);
            font-size: 0.62rem;
            font-weight: 600;
            text-align: center;
            line-height: 1.2;
        }
        .community-room-card__metric i {
            color: var(--crc-green);
            font-size: 0.72rem;
            margin-bottom: 0.1rem;
        }
        .community-room-card__metric strong {
            color: var(--crc-text);
            font-size: 0.82rem;
            font-weight: 700;
        }
        .community-room-card__footer {
            margin-top: auto;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            padding: 0.85rem 1rem 1rem;
            border-top: 1px solid #eef2f6;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .community-room-card__more-link {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--crc-green);
            text-decoration: none;
        }
        .community-room-card__more-link:hover {
            color: #0f766e;
            text-decoration: none;
        }
        .community-room-card__more-link i { font-size: 0.68rem; }
        .community-room-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }
        .community-room-card__actions .btn {
            border-radius: var(--crc-radius) !important;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3rem 0.65rem;
            line-height: 1.25;
        }
        .community-room-card__btn-join {
            border: 1px solid var(--crc-green) !important;
            color: var(--crc-green) !important;
            background: #fff !important;
        }
        .community-room-card__btn-join:hover {
            background: var(--crc-green) !important;
            color: #fff !important;
        }
        .community-room-card__btn-primary {
            background: var(--crc-green) !important;
            border-color: var(--crc-green) !important;
            color: #fff !important;
        }
        .community-room-card__btn-secondary {
            border: 1px solid var(--crc-border) !important;
            background: #fff !important;
            color: #475569 !important;
        }
        .community-room-card__btn-secondary:hover {
            border-color: var(--crc-green) !important;
            color: var(--crc-green) !important;
            background: #f0fdf4 !important;
        }
        .community-room-card__btn-leave {
            border: 1px solid #fecaca !important;
            background: #fff !important;
            color: #dc2626 !important;
        }
        .community-room-card__btn-leave:hover {
            background: #fef2f2 !important;
            color: #b91c1c !important;
        }
        .community-room-card__btn-pending {
            border-radius: var(--crc-radius) !important;
        }

        .communities-card-grid {
            display: grid;
            gap: 1rem;
            align-items: stretch;
            width: 100%;
        }
        .communities-card-grid__item {
            display: flex;
            min-width: 0;
        }
        .communities-card-grid__item > .community-room-card {
            width: 100%;
            height: 100%;
        }
        .communities-card-grid--per-row-1 {
            grid-template-columns: minmax(0, 1fr);
        }
        .communities-card-grid--per-row-2 {
            grid-template-columns: minmax(0, 1fr);
        }
        @media (min-width: 768px) {
            .communities-card-grid--per-row-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .communities-card-grid--per-row-3 {
            grid-template-columns: minmax(0, 1fr);
        }
        @media (min-width: 768px) {
            .communities-card-grid--per-row-3 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (min-width: 992px) {
            .communities-card-grid--per-row-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .community-section-heading {
            font-size: 1.125rem;
            font-weight: 700;
            color: #242729;
            margin-bottom: 0.25rem;
        }

        .page-title {
            margin-bottom: 1.25rem;
            padding: 1rem 0 0;
            text-align: center;
        }
        .page-title h1 {
            font-size: 1.65rem;
            font-weight: 700;
            color: #242729;
            margin-bottom: 0.35rem;
        }

        /* Ensure filter select fields always show visible borders */
        #filterForm .form-control {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            background-color: #fff;
        }
        #filterForm .select2-container--default .select2-selection--single,
        #filterForm .select2-container--bootstrap4 .select2-selection {
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem !important;
            min-height: calc(1.5em + 0.75rem + 2px);
            background-color: #fff !important;
        }
        #filterForm .select2-container .select2-selection__rendered {
            line-height: calc(1.5em + 0.75rem) !important;
        }
        #filterForm .select2-container .select2-selection__arrow {
            height: calc(1.5em + 0.75rem + 2px) !important;
        }

        .communities-nav-react { margin-bottom: 1.5rem; }
        .communities-nav-jumps {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            margin-bottom: 0.75rem;
        }
        .communities-nav-jump-btn {
            border: 1px solid #dbe3ec;
            background: #fff;
            color: #334155;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            cursor: pointer;
        }
        .communities-nav-jump-btn:hover {
            border-color: var(--theme-color-primary, #119A48);
            color: var(--theme-color-primary, #119A48);
            background: #f0fdf4;
        }
        .communities-filters--react {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
        }
        .communities-search-bar { position: relative; margin-bottom: 0.75rem; }
        .communities-search-bar input {
            width: 100%;
            padding: 0.65rem 1rem 0.65rem 2.5rem;
            border: 1px solid #e2e8f0;
            font-size: 0.95rem;
        }
        .communities-search-icon {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .communities-filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .communities-filter-btn {
            padding: 0.45rem 0.9rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
        }
        .communities-filter-btn.active,
        .communities-filter-btn:hover {
            background: var(--theme-color-primary, #119A48);
            border-color: var(--theme-color-primary, #119A48);
            color: #fff;
        }
        .communities-nav-status {
            margin: 0.85rem 0 0;
            font-size: 0.8125rem;
            color: #64748b;
        }
        .communities-infinite-sentinel { height: 1px; width: 100%; }
        .communities-infinite-loader { color: #64748b; font-size: 0.875rem; padding: 0.5rem 0; }
        #communities-filters,
        #communities-recommended,
        #communities-all-heading,
        #communities-membership-summary {
            scroll-margin-top: 6rem;
        }

        .communities-membership-summary {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem 1.25rem;
            margin-bottom: 1.25rem;
            padding: 0.9rem 1.1rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            background: linear-gradient(135deg, #f8fafc 0%, #f0fdf4 100%);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }
        .communities-membership-summary__icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 4px;
            background: #fff;
            border: 1px solid #dcfce7;
            color: var(--theme-color-primary, #119A48);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .communities-membership-summary__body {
            flex: 1;
            min-width: 12rem;
        }
        .communities-membership-summary__title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
        }
        .communities-membership-summary__text {
            margin: 0.2rem 0 0;
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.45;
        }
        .communities-membership-summary__stats {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .communities-membership-summary__stat {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            background: #fff;
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
        }
        .communities-membership-summary__stat strong {
            color: var(--theme-color-primary, #119A48);
            font-size: 0.92rem;
        }
        .communities-membership-summary__stat--pending strong {
            color: #d97706;
        }
        .communities-membership-summary__link {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--theme-color-primary, #119A48);
            text-decoration: none;
            white-space: nowrap;
        }
        .communities-membership-summary__link:hover {
            text-decoration: underline;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-title pt-3 pb-3">
            <h1>Communities of Practice</h1>
            <p style="margin: 0.5rem 0 0 0; font-size: 1rem; color: #718096;">Join a community of practice to connect with peers, share knowledge, and participate in discussions.</p>
        </div>

        @if(Auth::check() && !empty($userMembershipStats))
            <div class="communities-membership-summary" id="communities-membership-summary">
                <span class="communities-membership-summary__icon" aria-hidden="true">
                    <i class="fa fa-users"></i>
                </span>
                <div class="communities-membership-summary__body">
                    <p class="communities-membership-summary__title">Your community memberships</p>
                    <p class="communities-membership-summary__text">
                        @if(request()->routeIs('account.my-communities'))
                            You are a member of {{ number_format((int) ($userMembershipStats['joined'] ?? 0)) }} public {{ (int) ($userMembershipStats['joined'] ?? 0) === 1 ? 'community' : 'communities' }}.
                        @else
                            Communities you belong to appear first in the list below.
                        @endif
                    </p>
                </div>
                <div class="communities-membership-summary__stats">
                    <span class="communities-membership-summary__stat">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                        <strong>{{ number_format((int) ($userMembershipStats['joined'] ?? 0)) }}</strong>
                        joined
                    </span>
                    @if((int) ($userMembershipStats['pending'] ?? 0) > 0)
                        <span class="communities-membership-summary__stat communities-membership-summary__stat--pending">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            <strong>{{ number_format((int) $userMembershipStats['pending']) }}</strong>
                            pending
                        </span>
                    @endif
                </div>
                @if(!request()->routeIs('account.my-communities') && (int) ($userMembershipStats['joined'] ?? 0) > 0)
                    <a href="{{ route('account.my-communities') }}" class="communities-membership-summary__link">
                        View my communities <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        @endif

        @php
            $communitiesInfiniteScroll = (bool) ($communitiesInfiniteScroll ?? false);
            $loadedCommunityCount = ($communities instanceof \Illuminate\Pagination\AbstractPaginator)
                ? (($communities->currentPage() - 1) * $communities->perPage()) + $communities->count()
                : count($communities ?? []);
            $navJumps = [];
            if (!request()->routeIs('account.my-communities')) {
                $navJumps[] = ['id' => 'communities-filters', 'label' => 'Filters'];
                if (Auth::check() && isset($recommendedCommunities) && $recommendedCommunities->isNotEmpty()) {
                    $navJumps[] = ['id' => 'communities-recommended', 'label' => 'Recommended'];
                }
            }
            $navJumps[] = ['id' => 'communities-all-heading', 'label' => 'All communities'];
        @endphp

        <div id="communities-nav-root"></div>
        <script type="application/json" id="communities-nav-config">{!! json_encode([
            'totalCommunities' => $communities instanceof \Illuminate\Pagination\AbstractPaginator
                ? $communities->total()
                : count($communities ?? []),
            'searchPlaceholder' => 'Search communities by name or description...',
            'searchDebounceMs' => 220,
            'jumps' => $navJumps,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

        <!-- Filter Section (main directory only) -->
        @if(!request()->routeIs('account.my-communities'))
        <div class="row mb-4" id="communities-filters">
            <div class="col-12">
                <div class="card" style="border-radius: 0.25rem; border: 1px solid #e2e8f0;">
                    <div class="card-body">
                        <form method="GET" action="{{ route('community.index') }}" id="filterForm">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="coverage" class="form-label" style="font-weight: 600; color: #2d3748;">Coverage</label>
                                    <select class="form-control select2" id="coverage" name="coverage" style="width: 100%;">
                                        <option value="">All Coverage Types</option>
                                        <option value="whole_of_africa" {{ request('coverage') == 'whole_of_africa' ? 'selected' : '' }}>Whole of Africa</option>
                                        <option value="region" {{ request('coverage') == 'region' ? 'selected' : '' }}>Region</option>
                                        <option value="country" {{ request('coverage') == 'country' ? 'selected' : '' }}>Country</option>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3" id="region_filter_container" style="display: {{ request('coverage') == 'region' ? 'block' : 'none' }};">
                                    <label for="region_id" class="form-label" style="font-weight: 600; color: #2d3748;">Region</label>
                                    <select class="form-control select2" id="region_id" name="region_id" style="width: 100%;">
                                        <option value="">All Regions</option>
                                        @foreach($regions ?? [] as $region)
                                            <option value="{{ $region->id }}" {{ request('region_id') == $region->id ? 'selected' : '' }}>
                                                {{ $region->region_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3" id="country_filter_container" style="display: {{ request('coverage') == 'country' || request('country_id') ? 'block' : 'none' }};">
                                    <label for="country_id" class="form-label" style="font-weight: 600; color: #2d3748;">Country</label>
                                    <select class="form-control select2" id="country_id" name="country_id" style="width: 100%;">
                                        <option value="">All Countries</option>
                                        @foreach($countries ?? [] as $country)
                                            <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="organisation" class="form-label" style="font-weight: 600; color: #2d3748;">Organisation</label>
                                    <select class="form-control select2" id="organisation" name="organisation" style="width: 100%;">
                                        <option value="">All Organisations</option>
                                        @foreach($organisations ?? [] as $org)
                                            <option value="{{ $org }}" {{ request('organisation') == $org ? 'selected' : '' }}>
                                                {{ $org }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="department" class="form-label" style="font-weight: 600; color: #2d3748;">Department</label>
                                    <select class="form-control select2" id="department" name="department" style="width: 100%;">
                                        <option value="">All Departments</option>
                                        @foreach($departments ?? [] as $dept)
                                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                                {{ $dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn theme-primary" style="width: 100%; border: none; padding: 0.775rem .95rem;">
                                        <i class="fa fa-filter mr-1"></i> Filter
                                    </button>
                                    @if(request()->hasAny(['coverage', 'region_id', 'country_id', 'organisation', 'department']))
                                        <a href="{{ route('community.index') }}" class="btn theme-secondary ml-2" style="border: none; padding: 0.775rem .95rem; white-space: nowrap;">
                                            <i class="fa fa-times mr-1"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(Auth::check() && isset($recommendedCommunities) && $recommendedCommunities->isNotEmpty() && !request()->routeIs('account.my-communities'))
            <div class="mb-4 pb-2 border-bottom" id="communities-recommended">
                <h2 class="community-section-heading">Recommended for you</h2>
                <p class="text-muted small mb-3 mb-md-4">Based on your profile health themes and tags from publications you have saved.</p>
                <div class="{{ communities_listing_grid_wrapper_class() }}">
                    @foreach($recommendedCommunities as $community)
                        <div class="communities-card-grid__item">
                            @include('communities.partials.room_card', ['community' => $community, 'pinned' => true])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(request()->routeIs('account.my-communities'))
            <h2 class="community-section-heading mb-3" id="communities-all-heading">Your communities</h2>
        @else
            <h2 class="community-section-heading mb-3" id="communities-all-heading">All communities</h2>
        @endif

        <div id="communities-list-wrap"
             @if($communitiesInfiniteScroll && $communities instanceof \Illuminate\Pagination\AbstractPaginator)
             data-infinite-scroll="1"
             data-current-page="{{ $communities->currentPage() }}"
             data-last-page="{{ $communities->lastPage() }}"
             data-total="{{ $communities->total() }}"
             data-loaded="{{ $loadedCommunityCount }}"
             @endif>
        <div class="{{ communities_listing_grid_wrapper_class() }}" id="communities-list">
            @if($communities->count() > 0)
                @include('communities.partials.community_list_items', ['communities' => $communities])
            @else
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle mr-2"></i>No communities found.
                    </div>
                </div>
            @endif
        </div>

        @if($communitiesInfiniteScroll && $communities instanceof \Illuminate\Pagination\AbstractPaginator && $communities->total() > 0)
        <div class="communities-infinite-footer py-3 text-center" id="communities-infinite-footer">
            <p class="text-muted small mb-2" id="communities-infinite-status">
                Showing {{ number_format($loadedCommunityCount) }} of {{ number_format($communities->total()) }} communities
            </p>
            @if($communities->hasMorePages())
                <div id="communities-infinite-sentinel" class="communities-infinite-sentinel" aria-hidden="true"></div>
                <div id="communities-infinite-loader" class="communities-infinite-loader d-none" aria-live="polite">
                    <i class="fa fa-spinner fa-spin me-1"></i>Loading more communities…
                </div>
            @else
                <p class="text-muted small mb-0" id="communities-infinite-complete">All communities loaded</p>
            @endif
        </div>
        @elseif($communities instanceof \Illuminate\Pagination\AbstractPaginator && $communities->hasPages())
        <div class="row">
            <div class="col-md-12">
                {{ $communities->links() }}
            </div>
        </div>
        @endif
        </div>
    </div>

    <!-- Join Modal -->
    <div class="modal fade" id="joinModal" tabindex="-1" role="dialog" aria-labelledby="joinModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="joinModalLabel">Join Community</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to join this community?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmJoin">Join</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Modal -->
    <div class="modal fade" id="leaveModal" tabindex="-1" role="dialog" aria-labelledby="leaveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveModalLabel">Leave Community</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to leave this community?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLeave">Leave</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
    <script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
    <script src="{{ asset('js/communities-index-filters.js') }}?v={{ @filemtime(public_path('js/communities-index-filters.js')) }}"></script>
    <script src="{{ asset('js/communities-index-nav.js') }}?v={{ @filemtime(public_path('js/communities-index-nav.js')) }}"></script>
    <script>
        window.communitiesInfiniteScrollConfig = {
            enabled: @json((bool) ($communitiesInfiniteScroll ?? false)),
            pageUrl: @json(route('community.page'))
        };
        window.COMMUNITIES_INFINITE_STATUS_COMPLETE = 'All communities loaded';
        window.COMMUNITIES_INFINITE_STATUS_ERROR = 'Could not load more communities. Tap to retry.';
    </script>
    <script src="{{ asset('js/communities-index-infinite.js') }}?v={{ @filemtime(public_path('js/communities-index-infinite.js')) }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
    <script>
        $(document).ready(function() {
            if ($('#filterForm').length) {
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('#coverage').on('change', function() {
                var coverage = $(this).val();
                if (coverage === 'region') {
                    $('#region_filter_container').show();
                    $('#country_filter_container').hide();
                    $('#country_id').val('').trigger('change');
                } else if (coverage === 'country') {
                    $('#region_filter_container').hide();
                    $('#country_filter_container').show();
                    $('#region_id').val('').trigger('change');
                } else {
                    $('#region_filter_container').hide();
                    $('#country_filter_container').hide();
                    $('#region_id').val('').trigger('change');
                    $('#country_id').val('').trigger('change');
                }
            });

            $('#coverage').trigger('change');
            }

            /* Participant strip: constant low-speed smooth scroll (rAF), pause on hover */
            var communityAvatarReduceMotion = window.matchMedia
                && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var communityAvatarScrollControllers = window.__khCommunityAvatarScrollControllers
                || (window.__khCommunityAvatarScrollControllers = []);

            window.initCommunityListingEnhancements = function (scope) {
                var $scope = scope ? $(scope) : $(document);
                $scope.find('.community-room-card__avatar-carousel:not([data-carousel-bound])').each(function () {
                var $carousel = $(this);
                $carousel.attr('data-carousel-bound', '1');
                var $track = $carousel.find('.community-room-card__avatar-track');
                var $slides = $track.find('.community-room-card__avatar-slides');
                var $prev = $carousel.find('.community-room-card__avatar-nav--prev');
                var $next = $carousel.find('.community-room-card__avatar-nav--next');
                if (!$track.length || !$slides.length) {
                    return;
                }

                var trackEl = $track[0];
                var slidesEl = $slides[0];
                var isPlaying = true;
                var rafId = null;
                var scrollFrac = 0;
                var pixelsPerFrame = 0.22;
                var resumeAfterNavTimer = null;

                function smoothBehavior() {
                    return communityAvatarReduceMotion ? 'auto' : 'smooth';
                }

                function gapPx() {
                    try {
                        var g = window.getComputedStyle(slidesEl).columnGap || window.getComputedStyle(slidesEl).gap;
                        var n = parseFloat(g);
                        return isNaN(n) ? 10 : n;
                    } catch (e) {
                        return 10;
                    }
                }

                function itemStep() {
                    var first = $slides.children().first()[0];
                    if (!first) {
                        return 48;
                    }
                    return first.getBoundingClientRect().width + gapPx();
                }

                function maxScroll() {
                    return trackEl.scrollWidth - trackEl.clientWidth;
                }

                function updateNav() {
                    var ms = maxScroll();
                    var left = trackEl.scrollLeft;
                    var tol = 2;
                    if (ms <= tol) {
                        $prev.prop('disabled', true);
                        $next.prop('disabled', true);
                        return;
                    }
                    $prev.prop('disabled', left <= tol);
                    $next.prop('disabled', left >= ms - tol);
                }

                function stopContinuousScroll() {
                    if (rafId !== null) {
                        cancelAnimationFrame(rafId);
                        rafId = null;
                    }
                }

                function continuousTick() {
                    rafId = null;
                    if (communityAvatarReduceMotion || document.hidden || !isPlaying) {
                        return;
                    }
                    var ms = maxScroll();
                    if (ms > 2) {
                        scrollFrac += pixelsPerFrame;
                        var step = Math.floor(scrollFrac);
                        if (step >= 1) {
                            trackEl.scrollLeft += step;
                            scrollFrac -= step;
                        }
                        if (trackEl.scrollLeft >= ms - 0.75) {
                            trackEl.scrollLeft = 0;
                            scrollFrac = 0;
                        }
                    }
                    if (isPlaying && !document.hidden && !communityAvatarReduceMotion) {
                        rafId = requestAnimationFrame(continuousTick);
                    }
                }

                function startContinuousScroll() {
                    if (communityAvatarReduceMotion) {
                        return;
                    }
                    if (rafId !== null) {
                        return;
                    }
                    rafId = requestAnimationFrame(continuousTick);
                }

                communityAvatarScrollControllers.push({
                    stop: stopContinuousScroll,
                    resumeIfPlaying: function () {
                        if (isPlaying && !document.hidden && !communityAvatarReduceMotion) {
                            startContinuousScroll();
                        }
                    }
                });

                $carousel.on('mouseenter', function () {
                    isPlaying = false;
                    stopContinuousScroll();
                });
                $carousel.on('mouseleave', function () {
                    isPlaying = true;
                    startContinuousScroll();
                });

                function afterManualNav() {
                    clearTimeout(resumeAfterNavTimer);
                    stopContinuousScroll();
                    resumeAfterNavTimer = setTimeout(function () {
                        if (isPlaying && !document.hidden && !communityAvatarReduceMotion) {
                            startContinuousScroll();
                        }
                    }, 550);
                }

                $prev.on('click', function (e) {
                    e.stopPropagation();
                    var delta = itemStep();
                    var ms = maxScroll();
                    var cur = trackEl.scrollLeft;
                    var beh = smoothBehavior();
                    afterManualNav();
                    if (cur <= 5) {
                        trackEl.scrollTo({ left: ms, behavior: beh });
                    } else {
                        trackEl.scrollBy({ left: -delta, behavior: beh });
                    }
                });
                $next.on('click', function (e) {
                    e.stopPropagation();
                    var delta = itemStep();
                    var ms = maxScroll();
                    var cur = trackEl.scrollLeft;
                    var beh = smoothBehavior();
                    afterManualNav();
                    if (cur >= ms - 5) {
                        trackEl.scrollTo({ left: 0, behavior: beh });
                    } else {
                        trackEl.scrollBy({ left: delta, behavior: beh });
                    }
                });

                $track.on('scroll', updateNav);
                $(window).on('resize', updateNav);
                if (window.ResizeObserver) {
                    new ResizeObserver(updateNav).observe(trackEl);
                }
                updateNav();
                startContinuousScroll();
            });
            };

            window.initCommunityListingEnhancements(document.getElementById('communities-list'));

            if (communityAvatarScrollControllers.length && !window.__khCommunitiesAvatarVis) {
                window.__khCommunitiesAvatarVis = true;
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        communityAvatarScrollControllers.forEach(function (c) {
                            c.stop();
                        });
                    } else {
                        communityAvatarScrollControllers.forEach(function (c) {
                            c.resumeIfPlaying();
                        });
                    }
                });
            }
        });

        let communityId;

        $(document).on('click', '.join-btn', function() {
            communityId = $(this).data('community-id');
            @if (Auth::check())
                $('#joinModal').modal('show');
            @else
                window.location.href = '/login';
            @endif
        });

        $('#confirmJoin').on('click', function() {
            var detailUrl = $('.join-btn[data-community-id="' + communityId + '"]').data('detail-url');
            $.ajax({
                url: '{{ route('community.join') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function(response) {
                    $('#joinModal').modal('hide');
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (detailUrl) {
                        window.location.href = detailUrl;
                    } else {
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $(document).on('click', '.leave-btn', function() {
            communityId = $(this).data('community-id');
            $('#leaveModal').modal('show');
        });

        $('#confirmLeave').on('click', function() {
            $.ajax({
                url: '{{ route('community.leave') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function(response) {
                    $('#leaveModal').modal('hide');
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });
    </script>
@endsection
