@extends('layouts.app')

@php
    // SEO Meta Tags for Communities Listing Page
    $pageTitle = 'Communities of Practice - ' . (settings()->site_name ?? 'Africa CDC Knowledge Hub');
    $pageDescription = 'Join professional communities of practice focused on public health topics across Africa. Connect with experts, share knowledge, and collaborate on health initiatives.';
    $pageKeywords = 'communities of practice, public health communities, Africa CDC communities, health professionals, networking, collaboration, ' . (settings()->seo_keywords ?? '');
    $pageImage = settings()->logo ?? asset('assets/images/logo.png');
    $canonicalUrl = url('communities');
    $ogType = 'website';
@endphp


@section('structured_data')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "{{ $pageTitle }}",
    "description": "{{ strip_tags($pageDescription) }}",
    "url": "{{ $canonicalUrl }}",
    "mainEntity": {
        "@type": "ItemList",
        "itemListElement": [
            @if(isset($communities) && $communities->count() > 0)
                @foreach($communities->take(10) as $index => $community)
                {
                    "@type": "ListItem",
                    "position": {{ $index + 1 }},
                    "item": {
                        "@type": "Organization",
                        "name": "{{ addslashes($community->community_name) }}",
                        "url": "{{ url('communities/detail/' . $community->id) }}",
                        "description": "{{ addslashes(Str::limit(strip_tags($community->description ?? ''), 200)) }}"
                    }
                }@if(!$loop->last),@endif
                @endforeach
            @endif
        ]
    },
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Home",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Communities",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
}
</script>
@endsection

@section('styles')
    <style>
        .theme-text { color: {{ settings()->primary_color ?? '#119A48' }}; }

        /* Compact “chat room” style cards; vertical rhythm ~1.32× (base ×1.2, +10%) */
        .community-room-card {
            border: 1px solid #d6d9dc;
            border-radius: 6px;
            background: #fff;
            padding: calc(8px * 1.32) calc(10px * 1.32);
            display: flex;
            flex-direction: column;
            text-align: left;
            transition: border-color .15s ease, box-shadow .15s ease;
            font-size: calc(0.8125rem * 1.32);
            line-height: 1.4;
            max-width: 100%;
        }
        .community-room-card:hover {
            border-color: #b0b8c1;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .community-room-card--clickable { cursor: pointer; }
        .community-room-card__head { margin-bottom: calc(4px * 1.32); }
        .community-room-card__title-row {
            display: flex;
            align-items: flex-start;
            gap: calc(6px * 1.32);
            margin-bottom: calc(4px * 1.32);
        }
        .community-room-card__pin {
            color: var(--theme-color-primary, #119A48);
            font-size: 0.75rem;
            margin-top: 3px;
            flex-shrink: 0;
        }
        .community-room-card__title {
            flex: 1;
            margin: 0;
            font-size: calc(0.9rem * 1.32);
            font-weight: 700;
            line-height: 1.2;
        }
        .community-room-card__title-link {
            color: var(--theme-color-primary, #119A48);
            text-decoration: none;
        }
        .community-room-card__title-link:hover { text-decoration: underline; opacity: 0.88; }
        .community-room-card__star {
            color: #9aa6b2;
            font-size: 0.85rem;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .community-room-card__badges {
            display: flex;
            flex-wrap: wrap;
            gap: calc(6px * 1.32);
            align-items: center;
        }
        .community-room-card__badge {
            display: inline-block;
            padding: calc(2px * 1.32) calc(8px * 1.32);
            border-radius: 999px;
            font-size: calc(0.6875rem * 1.32);
            font-weight: 600;
            line-height: 1.35;
        }
        .community-room-card__badge--access {
            background: #e1ecf4;
            color: #39739d;
        }
        .community-room-card__badge--private {
            background: #e8e8e8;
            color: #555;
        }
        .community-room-card__badge--activity {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .community-room-card__desc {
            margin: 0 0 calc(4px * 1.32);
            color: #3b4045;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 0;
            max-height: calc(2.8em * 1.32 * 1.5);
            font-size: calc(0.75rem * 1.32);
        }
        .community-room-card__coverage {
            margin: 0 0 calc(4px * 1.32);
            font-size: calc(0.6875rem * 1.32);
            color: #6a737c;
        }
        .community-room-card__coverage i { color: var(--theme-color-primary, #119A48); margin-right: 4px; }
        .community-room-card__avatars {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
            gap: 0;
            margin-bottom: calc(6px * 1.32);
            min-height: calc(26px * 1.32);
            overflow: hidden;
        }
        .community-room-card__avatar-wrap {
            position: relative;
            margin-left: calc(-5px * 1.32);
            border: 2px solid #fff;
            border-radius: 3px;
            overflow: visible;
            line-height: 0;
            flex-shrink: 0;
        }
        .community-room-card__avatar-wrap:first-child { margin-left: 0; }
        .community-room-card__avatar-wrap--online::after {
            content: '';
            position: absolute;
            bottom: -1px;
            right: -1px;
            width: calc(7px * 1.32);
            height: calc(7px * 1.32);
            background: #2e7d32;
            border: 1.5px solid #fff;
            border-radius: 50%;
            z-index: 1;
        }
        .community-room-card__avatar {
            width: calc(24px * 1.32);
            height: calc(24px * 1.32);
            object-fit: cover;
            display: block;
            border-radius: 2px;
            vertical-align: top;
        }
        .community-room-card__avatar-initials {
            display: flex;
            align-items: center;
            justify-content: center;
            width: calc(24px * 1.32);
            height: calc(24px * 1.32);
            font-size: calc(9px * 1.32);
            font-weight: 700;
            color: #3c4146;
            background: #e4e6e8;
            border-radius: 2px;
            line-height: 1;
        }
        .community-room-card__more-members {
            margin-left: calc(6px * 1.32);
            font-size: calc(0.6875rem * 1.32);
            color: #6a737c;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .community-room-card__footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: calc(4px * 1.32);
            padding-top: calc(4px * 1.32);
            margin-top: 0;
            border-top: 1px solid #edeff1;
        }
        .community-room-card__more-link {
            font-size: calc(0.8125rem * 1.32);
            font-weight: 500;
            color: var(--theme-color-primary, #119A48);
        }
        .community-room-card__more-link:hover { opacity: 0.88; }
        .community-room-card__stats {
            font-size: calc(0.75rem * 1.32);
            color: var(--theme-color-primary, #119A48);
            font-weight: 500;
            white-space: nowrap;
        }
        .community-room-card__stats i { margin-right: 4px; }
        .community-room-card__stats-sep { margin: 0 4px; color: #9aa6b2; font-weight: 400; }
        .community-room-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: calc(4px * 1.32);
            margin-top: calc(4px * 1.32);
            padding-top: calc(4px * 1.32);
            border-top: 1px solid #f1f2f3;
        }
        .community-room-card__btn-join {
            border: 1px solid var(--theme-color-primary, #119A48);
            color: var(--theme-color-primary, #119A48);
            background: #fff;
            font-size: calc(0.6875rem * 1.32);
            padding: calc(0.15rem * 1.32) calc(0.45rem * 1.32);
            line-height: 1.2;
        }
        .community-room-card__btn-join:hover {
            background: var(--theme-color-primary, #119A48);
            color: #fff;
        }
        .community-room-card__btn-primary {
            background: var(--theme-color-primary, #119A48);
            border-color: var(--theme-color-primary, #119A48);
            color: #fff;
            font-size: calc(0.6875rem * 1.32);
            padding: calc(0.15rem * 1.32) calc(0.45rem * 1.32);
            line-height: 1.2;
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
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="page-title pt-3 pb-3">
            <h1>Communities of Practice</h1>
            <p style="margin: 0.5rem 0 0 0; font-size: 1rem; color: #718096;">Join a community of practice to connect with peers, share knowledge, and participate in discussions.</p>
        </div>

        <!-- Filter Section (main directory only) -->
        @if(!request()->routeIs('account.my-communities'))
        <div class="row mb-4">
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
            <div class="mb-4 pb-2 border-bottom">
                <h2 class="community-section-heading">Recommended for you</h2>
                <p class="text-muted small mb-3 mb-md-4">Based on your profile health themes and tags from publications you have saved.</p>
                <div class="row align-items-start">
                    @foreach($recommendedCommunities as $community)
                        <div class="col-md-6 col-lg-4 mb-3">
                            @include('communities.partials.room_card', ['community' => $community, 'pinned' => true])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(request()->routeIs('account.my-communities'))
            <h2 class="community-section-heading mb-3">Your communities</h2>
        @else
            <h2 class="community-section-heading mb-3">All communities</h2>
        @endif

        <div class="row align-items-start">
            @forelse ($communities as $community)
                <div class="col-md-6 col-lg-4 mb-3">
                    @include('communities.partials.room_card', ['community' => $community, 'pinned' => false])
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fa fa-info-circle mr-2"></i>No communities found.
                    </div>
                </div>
            @endforelse
        </div>
        @if($communities->hasPages())
        <div class="row">
            <div class="col-md-12">
                {{ $communities->links() }}
            </div>
        </div>
        @endif
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
        });

        let communityId;

        $('.join-btn').on('click', function() {
            communityId = $(this).data('community-id');
            @if (Auth::check())
                $('#joinModal').modal('show');
            @else
                window.location.href = '/login';
            @endif
        });

        $('#confirmJoin').on('click', function() {
            $.ajax({
                url: '{{ route('community.join') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    community_id: communityId
                },
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        $('#joinModal').modal('hide');
                        alert(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('An error occurred. Please try again.');
                }
            });
        });

        $('.leave-btn').on('click', function() {
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
