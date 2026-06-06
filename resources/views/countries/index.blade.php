@extends('layouts.plain')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/map.css')}}">
<style>
    .countries-page-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
        min-height: calc(100vh - 100px);
        padding: 2rem 0;
    }

    .countries-page-wrapper .container-fluid {
        max-width: 1400px;
        padding-left: 2rem;
        padding-right: 2rem;
    }

    .page-header {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--theme-color-primary, #119A48);
        margin: 0 0 0.5rem 0;
        letter-spacing: -0.5px;
    }

    .page-header p {
        color: #64748b;
        margin: 0;
        font-size: 1.05rem;
    }

    .stats-container {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .stat-item {
        flex: 1;
        min-width: 150px;
        background: var(--theme-color-primary, #119A48);
        color: white;
        padding: 1rem;
        border-radius: 12px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .stat-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 85%, black) 100%);
        z-index: 0;
    }

    .stat-item:nth-child(2)::before {
        background: linear-gradient(135deg, color-mix(in srgb, var(--theme-color-primary, #119A48) 70%, white) 0%, var(--theme-color-primary, #119A48) 100%);
    }

    .stat-item:nth-child(3)::before {
        background: linear-gradient(135deg, color-mix(in srgb, var(--theme-color-primary, #119A48) 50%, white) 0%, color-mix(in srgb, var(--theme-color-primary, #119A48) 70%, white) 100%);
    }

    .stat-item > * {
        position: relative;
        z-index: 1;
    }

    .stat-item h3 {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: white;
    }

    .stat-item p {
        margin: 0.25rem 0 0 0;
        font-size: 0.875rem;
        opacity: 0.95;
        color: white;
    }

    .map-container-wrapper {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 2rem;
        height: 100%;
    }

    .map-container-wrapper h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .map-section-container {
        position: relative;
    }

    .map-section-container svg {
        width: 100%;
        height: auto;
        max-height: 600px;
    }

    .regions-sidebar {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .regions-sidebar h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .region-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .region-card:hover {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.15);
        transform: translateY(-2px);
    }

    .region-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%);
        padding: 1rem 1.25rem;
        cursor: pointer;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.3s ease;
    }

    .region-card.active .region-card-header {
        background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #0d7a3a 100%);
        color: white;
    }

    .region-card-header:hover {
        background: #ffffff;
        color: #1e293b;
    }

    .region-card-header h5 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .region-card-header .region-icon {
        font-size: 1.25rem;
    }

    .region-resources {
        background: rgba(17, 154, 72, 0.1);
        color: var(--theme-color-primary, #119A48);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-left: auto;
    }

    .region-card.active .region-resources {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .region-card-header .chevron {
        transition: transform 0.3s ease;
        color: #64748b;
    }

    .region-card.active .chevron {
        transform: rotate(180deg);
        color: white;
    }

    .region-card-header:hover .chevron {
        color: #64748b;
    }

    .region-countries {
        padding: 1rem;
        display: none;
    }

    .region-card.active .region-countries {
        display: block;
    }

    .country-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1rem;
        padding: 0.5rem 0;
    }

    .country-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .country-card:hover {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 4px 12px rgba(17, 154, 72, 0.2);
        transform: translateY(-3px);
        text-decoration: none;
        color: inherit;
    }

    .country-card.country-selected {
        border-color: var(--theme-color-primary, #119A48) !important;
        box-shadow: 0 6px 16px rgba(17, 154, 72, 0.3) !important;
        background: rgba(17, 154, 72, 0.05);
        transform: translateY(-2px);
    }

    .country-flag {
        width: 80px;
        height: 50px;
        margin: 0 auto 0.75rem;
        border-radius: 8px;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border: 2px solid #e2e8f0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .country-card:hover .country-flag {
        border-color: var(--theme-color-primary, #119A48);
        box-shadow: 0 4px 8px rgba(17, 154, 72, 0.3);
    }

    .country-name {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .country-resources {
        font-size: 0.8rem;
        color: #64748b;
        background: #f1f5f9;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        display: inline-block;
    }

    .country-card:hover .country-resources {
        background: rgba(17, 154, 72, 0.1);
        color: var(--theme-color-primary, #119A48);
    }

    /* SVG Map Styling - Override map.css */
    .map-section-container {
        width: 100%;
        min-height: 600px;
        position: relative;
        overflow: visible;
    }

    .map-wrapper {
        width: 100% !important;
        min-height: 600px !important;
        position: relative;
        display: block !important;
        overflow: visible;
    }

    .map-wrapper svg {
        width: 100% !important;
        height: auto !important;
        min-height: 600px !important;
        max-height: 700px !important;
        display: block !important;
        padding: 1rem !important;
        padding-bottom: 2rem !important;
        float: none !important;
        visibility: visible !important;
        opacity: 1 !important;
        overflow: visible !important;
    }

    .map-wrapper #admin0 {
        position: relative !important;
        width: 100% !important;
        height: 100% !important;
    }

    .map-wrapper .st0.our-member {
        fill: var(--theme-color-primary, #119A48) !important;
        stroke: rgba(255, 255, 255, 0.9) !important;
        stroke-width: 2px !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        opacity: 0.75;
        visibility: visible !important;
        display: block !important;
    }

    .map-wrapper .st0.our-member:hover {
        fill: var(--theme-color-primary, #119A48) !important;
        stroke: #ffffff !important;
        stroke-width: 2.5px !important;
        filter: brightness(1.2) !important;
        opacity: 1 !important;
        z-index: 10;
    }

    .map-wrapper .st0.our-member.active {
        fill: var(--theme-color-primary, #119A48) !important;
        stroke: #ffffff !important;
        stroke-width: 2.5px !important;
        opacity: 1 !important;
        filter: brightness(1.15) !important;
    }

    /* Secondary color for focused countries */
    .map-wrapper .st0.our-member-focused {
        fill: var(--theme-color-secondary, #0d7a3a) !important;
        stroke: #ffffff !important;
        stroke-width: 2.5px !important;
        opacity: 1 !important;
    }

    /* Search Filter */
    .country-search {
        margin-bottom: 1.5rem;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 0.875rem 1rem 0.875rem 3rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8f9fa;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--theme-color-primary, #119A48);
        background: white;
        box-shadow: 0 0 0 3px rgba(17, 154, 72, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.1rem;
    }

    @media (max-width: 991.98px) {
        .map-container-wrapper {
            margin-bottom: 2rem;
        }

        .country-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 0.75rem;
        }

        .country-flag {
            width: 60px;
            height: 40px;
        }
    }

    @media (max-width: 575.98px) {
        .stats-container {
            flex-direction: column;
        }

        .country-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        }

        .page-header {
            padding: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }
    }

    /* Loading state */
    .loading-shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes shimmer {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="countries-page-wrapper">
<div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fa fa-globe-americas me-2"></i>Member States</h1>
            <p>Explore health resources and publications by country and region</p>
            
            <div class="stats-container">
                @php
                    $totalCountries = count($countries);
                    $totalResources = $countries->sum('resources');
                @endphp
                <div class="stat-item">
                    <h3>{{ $totalCountries }}</h3>
                    <p>Member States</p>
                </div>
                <div class="stat-item">
                    <h3>{{ count($regions) }}</h3>
                    <p>Regions</p>
                </div>
                <div class="stat-item">
                    <h3>{{ number_format($totalResources) }}</h3>
                    <p>Total Resources</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Map Section -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="map-container-wrapper">
                    <h3><i class="fa fa-map me-2"></i>Interactive Map</h3>
                    <div class="country-search">
                        <div class="search-input-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" 
                                   id="countrySearch" 
                                   class="search-input" 
                                   placeholder="Search for a country...">
                        </div>
                    </div>
                @include('countries.map')
                </div>
            </div>

            <!-- Regions Sidebar -->
            <div class="col-lg-4 col-md-12">
                <div class="regions-sidebar">
                    <h3><i class="fa fa-layer-group me-2"></i>Browse by Region</h3>
                    
                    <div id="regionsAccordion">
                        @foreach($regions as $index => $region)
                                        @php
                $region_countries = array_filter($countries->toArray(), function ($cnt) use ($region) {
                                    return isset($cnt['region_id']) && $cnt['region_id'] == $region->id;
                });
                
                // Get unique resources (publications + forums) for this region
                $regionResourceData = $region_resources[$region->id] ?? [
                    'publications' => 0,
                    'forums' => 0,
                    'total' => 0
                ];
                $regionalResources = $regionResourceData['total'];
                                        @endphp
                            
                            <div class="region-card" data-region-id="{{ $region->id }}">
                                <div class="region-card-header" data-toggle="collapse" 
                                     data-target="#region{{$region->id}}" 
                                     aria-expanded="false">
                                    <h5>
                                        <i class="fa fa-globe region-icon"></i>
                                        {{ $region->region_name }}
                                        <span class="region-resources">{{ $regionalResources }} Resources</span>
                                                </h5>
                                    <i class="fa fa-chevron-down chevron"></i>
                                            </div>
                                <div id="region{{$region->id}}" 
                                     class="region-countries collapse" 
                                     data-parent="#regionsAccordion">
                                    @if(count($region_countries) > 0)
                                        <div class="country-grid">
                                                        @foreach($region_countries as $country)
                                                                                        @php
                                                    $country = (object) $country;
                                                                                        @endphp
                                                <a href="{{ country_detail_url($country) }}" 
                                                   class="country-card" 
                                                   data-country-id="{{$country->id}}"
                                                   data-country-name="{{ strtolower($country->name) }}">
                                                    <div class="country-flag" 
                                                         style="background-image: url('{{ asset('assets/img/flags/' . ($country->flag ?? '')) }}');">
                                                    </div>
                                                    <div class="country-name">{{ $country->name }}</div>
                                                    <div class="country-resources">{{$country->resources}} Resources</div>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="empty-state">
                                            <i class="fa fa-inbox"></i>
                                            <p>No countries found in this region</p>
                                        </div>
                                    @endif
                                                                                                </div>
                                                                                        </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
        </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/js/map.js')}}"></script>
<script>
    const countryDetailUrls = @json(
        collect($countries ?? [])->mapWithKeys(fn ($country) => [(string) $country->id => country_detail_url($country)])->all()
    );

    $(document).ready(function() {
        // Toggle region cards
        $('.region-card-header').on('click', function() {
            const card = $(this).closest('.region-card');
            const isActive = card.hasClass('active');
            
            // Close all other cards
            $('.region-card').removeClass('active');
            $('.region-countries').collapse('hide');
            
            // Toggle current card
            if (!isActive) {
                card.addClass('active');
                card.find('.region-countries').collapse('show');
            }
        });

        // Update chevron on collapse events
        $('.region-countries').on('show.bs.collapse', function () {
            $(this).closest('.region-card').addClass('active');
        }).on('hide.bs.collapse', function () {
            $(this).closest('.region-card').removeClass('active');
        });

        // Country search functionality
        $('#countrySearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            if (searchTerm.length === 0) {
                $('.country-card').show();
                $('.region-card').show();
                return;
            }
            
            // Filter country cards
            $('.country-card').each(function() {
                const countryName = $(this).data('country-name') || '';
                if (countryName.includes(searchTerm)) {
                    $(this).show();
                    $(this).closest('.region-card').show();
                    $(this).closest('.region-countries').collapse('show');
                    $(this).closest('.region-card').addClass('active');
                } else {
                    $(this).hide();
                }
            });
            
            // Hide empty regions
            $('.region-card').each(function() {
                const visibleCountries = $(this).find('.country-card:visible').length;
                if (visibleCountries === 0) {
                    $(this).hide();
                }
            });
        });

        // Track active country for click optimization
        let activeCountryId = null;

        // Map country hover interaction
        $('.country-card').on('mouseenter', function() {
            const countryId = $(this).data('country-id');
            $('#admin0 path[id="' + countryId + '"]').addClass('active');
        }).on('mouseleave', function() {
            const countryId = $(this).data('country-id');
            // Only remove active if it's not the clicked country
            if (activeCountryId !== countryId) {
                $('#admin0 path[id="' + countryId + '"]').removeClass('active');
            }
        });

        // SVG path hover interaction
        $('#admin0 path.our-member').on('mouseenter', function() {
            const countryId = $(this).attr('id');
            // Skip if this is the active clicked country
            if (activeCountryId !== countryId) {
                $('.country-card[data-country-id="' + countryId + '"]').css({
                    'border-color': 'var(--theme-color-primary, #119A48)',
                    'box-shadow': '0 4px 12px rgba(17, 154, 72, 0.2)'
                });
            }
        }).on('mouseleave', function() {
            const countryId = $(this).attr('id');
            // Keep styling if it's the active clicked country
            if (activeCountryId !== countryId) {
                $('.country-card[data-country-id="' + countryId + '"]').css({
                    'border-color': '#e2e8f0',
                    'box-shadow': 'none'
                });
            }
        }).on('click', function(e) {
            e.preventDefault();
            const countryId = $(this).attr('id');
            
            // Remove previous active country styling
            if (activeCountryId && activeCountryId !== countryId) {
                $('#admin0 path[id="' + activeCountryId + '"]').removeClass('active');
                $('.country-card[data-country-id="' + activeCountryId + '"]').css({
                    'border-color': '#e2e8f0',
                    'box-shadow': 'none'
                }).removeClass('country-selected');
            }
            
            // Set new active country
            activeCountryId = countryId;
            
            // Add active styling
            $(this).addClass('active');
            $('.country-card[data-country-id="' + countryId + '"]').css({
                'border-color': 'var(--theme-color-primary, #119A48)',
                'box-shadow': '0 6px 16px rgba(17, 154, 72, 0.3)'
            }).addClass('country-selected');
            
            const countryCard = $('.country-card[data-country-id="' + countryId + '"]');
            if (countryCard.length) {
                // Expand the region containing this country
                countryCard.closest('.region-card').addClass('active');
                countryCard.closest('.region-countries').collapse('show');
                
                // Scroll to the country card
                $('html, body').animate({
                    scrollTop: countryCard.offset().top - 100
                }, 500);
            }
            
            // Navigate after a short delay for visual feedback
            setTimeout(() => {
                const targetUrl = countryDetailUrls[String(countryId)] || ('{{ url('countries/details') }}?state=' + countryId);
                window.location.href = targetUrl;
            }, 300);
        });
    });
</script>
@endsection