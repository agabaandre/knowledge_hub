@extends('layouts.plain')

@section('title', 'My forum posts')

@section('styles')
@include('common.table')
@include('admin.publications.partials.filter_styles')
<style>
    .my-pub-page {
        --mp-green: {{ settings()->au_corporate_green ?? '#1A5632' }};
        --mp-gold: {{ settings()->au_gold ?? '#B4A269' }};
        --mp-red: {{ settings()->au_red ?? '#9F2241' }};
        --mp-plum: {{ settings()->au_plum ?? '#522B39' }};
        --mp-grey: {{ settings()->au_grey_text ?? '#58595B' }};
    }
    .my-pub-shell {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }
    .my-pub-shell > .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .my-pub-shell .card-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .my-pub-shell__subtitle {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0.25rem 0 0;
    }
    .my-pub-quick-links {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .my-pub-quick-links a {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.45rem 0.85rem;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #334155;
        font-size: 0.8125rem;
        font-weight: 600;
        text-decoration: none;
        transition: border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }
    .my-pub-quick-links a:hover,
    .my-pub-quick-links a.is-active {
        border-color: var(--mp-green);
        color: var(--mp-green);
        box-shadow: 0 2px 8px rgba(26, 86, 50, 0.08);
        text-decoration: none;
    }
    .my-pub-section {
        margin-bottom: 1.25rem;
    }
    .my-pub-section__title {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #64748b;
        margin: 0 0 0.65rem;
    }
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(132px, 1fr));
        gap: 0.65rem;
    }
    .metric-card {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.7rem 0.85rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        min-height: 0;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .metric-card__icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .metric-card__icon--green { background: #ecfdf3; color: var(--mp-green); }
    .metric-card__icon--gold { background: #faf6eb; color: var(--mp-gold); }
    .metric-card__icon--red { background: #fdf2f6; color: var(--mp-red); }
    .metric-card__icon--grey { background: #f1f5f9; color: var(--mp-grey); }
    .metric-card__icon--plum { background: #f6f0f3; color: var(--mp-plum); }
    .metric-card__value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.1;
    }
    .metric-card__label {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: 500;
        line-height: 1.2;
        margin-top: 0.1rem;
    }
    .community-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1rem 1.1rem 1.1rem;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    }
    .community-panel__head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
        flex-wrap: wrap;
    }
    .community-panel__head h6 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }
    .community-panel__head p {
        margin: 0.2rem 0 0;
        font-size: 0.8rem;
        color: #64748b;
    }
    .community-panel__count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: #ecfdf3;
        color: var(--mp-green);
        font-size: 0.75rem;
        font-weight: 700;
    }
    .community-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.65rem;
    }
    .community-card {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.75rem 0.9rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        color: #0f172a;
        text-decoration: none;
        transition: border-color 0.2s ease, background 0.2s ease, transform 0.2s ease;
    }
    .community-card:hover {
        border-color: var(--mp-green);
        background: #fff;
        transform: translateY(-1px);
        text-decoration: none;
        color: var(--mp-green);
    }
    .community-card__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--mp-green);
        flex-shrink: 0;
    }
    .community-card__name {
        font-size: 0.84rem;
        font-weight: 600;
        line-height: 1.35;
        word-break: break-word;
    }
    .community-card__meta {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 0.15rem;
    }
    .my-disc-col-index { width: 3rem; min-width: 3rem; }
    .my-disc-col-title { min-width: 180px; }
    .my-disc-col-status { min-width: 110px; }
    .my-disc-col-views { min-width: 88px; text-align: center; }
    .my-disc-col-created { min-width: 130px; white-space: nowrap; }
    .my-disc-col-actions { min-width: 200px; white-space: nowrap; }
    .my-disc-title {
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
    }
    .my-disc-moderator-note {
        font-size: 0.78rem;
        color: #b91c1c;
        margin-top: 0.35rem;
        line-height: 1.45;
    }
    .pub-filters-card--collapsible:not(.is-expanded) .pub-filters-card__header {
        border-bottom: none;
    }
    .pub-filters-panel-toggle .fa-chevron-down {
        transition: transform 0.2s ease;
    }
    .pub-filters-panel-toggle[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }
    @media (max-width: 767.98px) {
        .my-pub-page.row {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .my-pub-shell > .card-body {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row px-3 my-pub-page">
    <div class="card col-lg-12 my-pub-shell">
        <div class="card-header text-left d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <h3 class="card-title">My forum posts</h3>
                <p class="my-pub-shell__subtitle">Track your discussions, moderation status, and community engagement.</p>
            </div>
            <a href="{{ route('forums.create') }}" class="btn btn-success mt-2 mt-md-0">
                <i class="fa fa-plus mr-1"></i> Start new discussion
            </a>
        </div>

        <div class="card-body pb-2">
            <div class="my-pub-quick-links">
                <a href="{{ route('account.publications') }}"><i class="fa fa-file-alt"></i> My publications</a>
                <a href="{{ route('account.my-discussions') }}" class="is-active"><i class="fa fa-comment-dots"></i> My forum posts</a>
                <a href="{{ route('account.my-forums') }}"><i class="fa fa-comments"></i> My Forums</a>
                <a href="{{ route('account.my-communities') }}"><i class="fa fa-users"></i> My Communities</a>
            </div>

            <div class="my-pub-section">
                <h4 class="my-pub-section__title">Overview</h4>
                <div class="metric-grid">
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--green"><i class="fa fa-comments"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['total'] ?? 0) }}</div>
                            <div class="metric-card__label">Forum posts</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--green"><i class="fa fa-check-circle"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['published'] ?? 0) }}</div>
                            <div class="metric-card__label">Published</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--gold"><i class="fa fa-clock"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['pending'] ?? 0) }}</div>
                            <div class="metric-card__label">Pending</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--red"><i class="fa fa-times-circle"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['rejected'] ?? 0) }}</div>
                            <div class="metric-card__label">Rejected</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--red"><i class="fa fa-eye"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['total_views'] ?? 0) }}</div>
                            <div class="metric-card__label">Total views</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--plum"><i class="fa fa-reply"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['forum_comments'] ?? 0) }}</div>
                            <div class="metric-card__label">Comments</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-card__icon metric-card__icon--green"><i class="fa fa-chart-line"></i></div>
                        <div>
                            <div class="metric-card__value">{{ number_format($stats['forum_engagements'] ?? 0) }}</div>
                            <div class="metric-card__label">Engagements</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(!empty($stats['communities']) && count($stats['communities']) > 0)
            <div class="my-pub-section">
                <h4 class="my-pub-section__title">Communities of Practice</h4>
                <div class="community-panel">
                    <div class="community-panel__head">
                        <div>
                            <h6>Your memberships</h6>
                            <p>Communities where you are an approved member.</p>
                        </div>
                        <span class="community-panel__count">{{ count($stats['communities']) }}</span>
                    </div>
                    <div class="community-grid">
                        @foreach($stats['communities'] as $community)
                            @php
                                $communityName = is_array($community) ? ($community['name'] ?? '') : (string) $community;
                                $communityUrl = is_array($community) ? ($community['url'] ?? route('account.my-communities')) : route('account.my-communities');
                            @endphp
                            <a href="{{ $communityUrl }}" class="community-card">
                                <span class="community-card__icon"><i class="fa fa-users"></i></span>
                                <span>
                                    <span class="community-card__name">{{ $communityName }}</span>
                                    <span class="community-card__meta">View community</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="card-body text-left pt-3 border-top">
            @php
                $hasActiveFilters = request()->filled('term') || request()->filled('status');
            @endphp
            <div class="pub-filters-card pub-filters-card--collapsible {{ $hasActiveFilters ? 'is-expanded' : '' }} mb-3">
                <div class="pub-filters-card__header">
                    <div class="pub-filters-card__heading">
                        <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                        <div>
                            <h3 class="pub-filters-card__title">Filter forum posts</h3>
                            <p class="pub-filters-card__subtitle">Filters apply automatically as you type or change selections</p>
                        </div>
                    </div>
                    <button
                        class="pub-filters-advanced-toggle pub-filters-panel-toggle"
                        type="button"
                        data-toggle="collapse"
                        data-target="#myDiscussionsFiltersPanel"
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}"
                        aria-controls="myDiscussionsFiltersPanel"
                        id="myDiscussionsFiltersToggle"
                    >
                        <i class="fa fa-chevron-down mr-1"></i>
                        <span class="pub-filters-panel-toggle__label">{{ $hasActiveFilters ? 'Hide Filters' : 'Show Filters' }}</span>
                    </button>
                </div>
                <div id="myDiscussionsFiltersPanel" class="collapse {{ $hasActiveFilters ? 'show' : '' }}">
                    <div class="pub-filters-card__body">
                        <form id="myDiscussionsFiltersForm" method="GET" action="{{ route('account.my-discussions') }}" class="mb-0">
                            <div class="pub-filters-grid">
                                <div class="pub-filter-field">
                                    <label class="pub-filter-label" for="filterMyDiscTerm">Title or content</label>
                                    <input type="text" name="term" id="filterMyDiscTerm" class="form-control pub-filter-input" value="{{ request('term') }}" placeholder="Search title or content…">
                                </div>
                                <div class="pub-filter-field">
                                    <label class="pub-filter-label" for="filterMyDiscStatus">Status</label>
                                    <select name="status" id="filterMyDiscStatus" class="form-control pub-filter-input">
                                        <option value="">All statuses</option>
                                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending approval</option>
                                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pub-filters-actions">
                                <a href="{{ route('account.my-discussions') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearMyDiscussionsFilters">
                                    <i class="fa fa-rotate-left"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <p class="kh-table-mobile-hint"><i class="fa fa-mobile-alt mr-1"></i> Discussion rows are shown as cards on small screens.</p>
            <div class="publication-table-wrap kh-table-mobile-scroll">
                <table class="table table-striped table-bordered align-middle w-100 kh-table-mobile-cards kh-table-mobile-cards--medium" id="my-discussions-table">
                    <thead>
                        <tr>
                            <th class="my-disc-col-index">#</th>
                            <th class="my-disc-col-title">Title</th>
                            <th class="my-disc-col-status">Status</th>
                            <th class="my-disc-col-views">Views</th>
                            <th class="my-disc-col-created">Submitted</th>
                            <th class="my-disc-col-actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($threads as $thread)
                            @php
                                $isRejected = (int) ($thread->is_rejected ?? 0) === 1;
                                $isLive = (int) ($thread->is_approved ?? 0) === 1 && (int) ($thread->status ?? 0) === 1;
                                $rowNumber = ($threads->currentPage() - 1) * $threads->perPage() + $loop->iteration;
                            @endphp
                            <tr>
                                <td class="my-disc-col-index kh-mcard-hide text-center" data-label="#">{{ $rowNumber }}</td>
                                <td class="my-disc-col-title kh-mcard-primary" data-label="Title">
                                    <div class="my-disc-title">{{ $thread->forum_title }}</div>
                                    @if($isRejected && !empty($thread->rejected_reason))
                                        <div class="my-disc-moderator-note">
                                            <strong>Moderator note:</strong> {{ \Illuminate\Support\Str::limit(strip_tags($thread->rejected_reason), 200) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="my-disc-col-status" data-label="Status">
                                    @if($isLive)
                                        <span class="badge bg-success">Published</span>
                                    @elseif($isRejected)
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending approval</span>
                                    @endif
                                </td>
                                <td class="my-disc-col-views text-center" data-label="Views">{{ format_view_count((int) ($thread->views ?? 0)) }}</td>
                                <td class="my-disc-col-created" data-label="Submitted">
                                    {{ $thread->created_at ? \Carbon\Carbon::parse($thread->created_at)->format('M j, Y g:i A') : '—' }}
                                </td>
                                <td class="my-disc-col-actions kh-mcard-actions text-center" data-label="Actions">
                                    <a href="{{ forum_thread_url($thread) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('account.my-discussions.edit', $thread) }}" class="btn btn-sm btn-primary">
                                        {{ $isRejected ? 'Edit & resubmit' : 'Edit' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    @if($hasActiveFilters)
                                        No forum posts match your filters.
                                    @else
                                        You have not started any discussions yet.
                                        <a href="{{ route('forums.create') }}">Create your first post</a>.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($threads, 'hasPages') && $threads->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $threads->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    var filterTimer = null;

    function submitMyDiscussionsFilters() {
        $('#myDiscussionsFiltersForm').trigger('submit');
    }

    $('#filterMyDiscTerm').on('input', function () {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(submitMyDiscussionsFilters, 350);
    });

    $('#filterMyDiscStatus').on('change', submitMyDiscussionsFilters);

    $('#myDiscussionsFiltersForm').on('submit', function (e) {
        e.preventDefault();
        var params = new URLSearchParams(new FormData(this));
        var query = params.toString();
        window.location.href = query
            ? '{{ route('account.my-discussions') }}?' + query
            : '{{ route('account.my-discussions') }}';
    });

    $('#clearMyDiscussionsFilters').on('click', function (e) {
        e.preventDefault();
        window.location.href = '{{ route('account.my-discussions') }}';
    });

    $('#myDiscussionsFiltersPanel')
        .on('show.bs.collapse', function () {
            $('#myDiscussionsFiltersToggle').attr('aria-expanded', 'true');
            $('#myDiscussionsFiltersToggle .pub-filters-panel-toggle__label').text('Hide Filters');
            $('.pub-filters-card--collapsible').addClass('is-expanded');
        })
        .on('hide.bs.collapse', function () {
            $('#myDiscussionsFiltersToggle').attr('aria-expanded', 'false');
            $('#myDiscussionsFiltersToggle .pub-filters-panel-toggle__label').text('Show Filters');
            $('.pub-filters-card--collapsible').removeClass('is-expanded');
        });
});
</script>
@endsection
