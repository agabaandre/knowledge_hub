@extends('layouts.plain')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}"/>
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

    .map-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
        margin-bottom: 1rem;
    }
    .map-control-group {
        flex: 1;
        min-width: 200px;
    }
    .map-control-group label {
        display: block;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.35rem;
    }
    .map-control-group select,
    .map-scope-reset {
        width: 100%;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.65rem 0.85rem;
        font-size: 0.95rem;
        background: #f8fafc;
    }
    .map-scope-reset {
        width: auto;
        cursor: pointer;
        background: #fff;
        color: #1A5632;
        font-weight: 600;
    }
    .map-scope-reset:hover {
        border-color: #1A5632;
        background: #f0f7f4;
    }
    .map-scope-summary {
        background: linear-gradient(135deg, #f0f7f4 0%, #fff 100%);
        border: 1px solid rgba(26, 86, 50, 0.15);
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    .map-scope-summary__value {
        font-size: 1.15rem;
        font-weight: 700;
        color: #9F2241;
    }
    .map-scope-summary__value em {
        font-style: normal;
        font-size: 0.85rem;
        font-weight: 500;
        color: #64748b;
    }
    .map-scope-summary__meta {
        font-size: 0.78rem;
        color: #64748b;
        width: 100%;
    }
    #countriesMapChart {
        min-height: 520px;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(180deg, #f8fafc 0%, #fff 100%);
        border: 1px solid #e2e8f0;
    }
    .map-legend {
        margin-top: 1rem;
        padding: 1rem 1.1rem;
        border-radius: 12px;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }
    .map-legend__title {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        margin-bottom: 0.65rem;
    }
    .map-legend__bar {
        height: 14px;
        border-radius: 999px;
        background: linear-gradient(90deg, #f0f7f4 0%, #1A5632 100%);
        border: 1px solid rgba(26, 86, 50, 0.2);
        margin-bottom: 0.5rem;
    }
    .map-legend__labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 600;
    }
    .region-card.map-region-active {
        border-color: #1A5632;
        box-shadow: 0 4px 16px rgba(26, 86, 50, 0.18);
    }
    .region-map-filter {
        font-size: 0.72rem;
        font-weight: 600;
        color: #1A5632;
        background: rgba(26, 86, 50, 0.1);
        border: none;
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
        margin-left: 0.5rem;
        cursor: pointer;
    }
    .region-map-filter:hover {
        background: rgba(26, 86, 50, 0.2);
    }
    .continental-indicators__title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.35rem;
    }
    .continental-indicators__subtitle {
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    .continental-indicators__list {
        display: flex;
        flex-direction: column;
        gap: 0.65rem;
        max-height: 420px;
        overflow-y: auto;
        padding-right: 0.25rem;
    }
    .continental-indicator-card {
        text-align: left;
        width: 100%;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.85rem 1rem;
        background: #fff;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .continental-indicator-card:hover,
    .continental-indicator-card.is-active {
        border-color: #1A5632;
        background: #f8fdf9;
        box-shadow: 0 4px 12px rgba(26, 86, 50, 0.12);
    }
    .continental-indicator-card__head {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.35rem;
    }
    .continental-indicator-card__subject {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #1A5632;
    }
    .continental-indicator-card__agg {
        font-size: 0.65rem;
        color: #94a3b8;
        text-align: right;
    }
    .continental-indicator-card__name {
        font-size: 0.88rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.35;
        margin-bottom: 0.25rem;
    }
    .continental-indicator-card__value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #9F2241;
        line-height: 1.2;
    }
    .continental-indicator-card__denom {
        font-size: 0.72rem;
        color: #64748b;
    }
    .continental-indicator-card__meta {
        font-size: 0.7rem;
        margin-top: 0.35rem;
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
                    <h3><i class="fa fa-map me-2"></i>Indicator Map</h3>
                    <div class="map-controls">
                        <div class="map-control-group">
                            <label for="mapIndicatorSelect">Indicator</label>
                            <select id="mapIndicatorSelect" class="search-input" style="padding-left:0.85rem;">
                                @forelse($map_indicators as $indicator)
                                    <option value="{{ $indicator->id }}" @if(($initial_map_data['kpi_id'] ?? null) == $indicator->id) selected @endif>
                                        {{ $indicator->name }}
                                    </option>
                                @empty
                                    <option value="">No published indicators</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="map-control-group" style="flex:0 0 auto; min-width:140px;">
                            <label>Map scope</label>
                            <button type="button" class="map-scope-reset" id="mapResetRegion">
                                <i class="fa fa-globe-africa me-1"></i> <span id="mapScopeLabel">All Africa</span>
                            </button>
                        </div>
                    </div>
                    <div class="country-search">
                        <div class="search-input-wrapper">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text"
                                   id="countrySearch"
                                   class="search-input"
                                   placeholder="Search for a country in the list…">
                        </div>
                    </div>
                    <div id="mapScopeSummary" class="map-scope-summary"></div>
                    <div id="countriesMapChart">
                        @if($map_indicators->isEmpty())
                            <div class="text-muted text-center p-5">Publish KPI indicators to enable the interactive map.</div>
                        @endif
                    </div>
                    <div class="map-legend" aria-hidden="false">
                        <div class="map-legend__title" id="mapLegendTitle">Indicator scale</div>
                        <div class="map-legend__bar" id="mapLegendBar"></div>
                        <div class="map-legend__labels">
                            <span id="mapLegendMin">—</span>
                            <span>Low → High</span>
                            <span id="mapLegendMax">—</span>
                        </div>
                    </div>
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
                                        <button type="button" class="region-map-filter js-map-region-filter" data-region-id="{{ $region->id }}" title="Show regional indicators on map">
                                            <i class="fa fa-map-marked-alt"></i> Map
                                        </button>
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

                    @include('countries.partials.continental_indicators')
                </div>
            </div>
        </div>
</div>
@endsection

@section('scripts')
<script>
    const countryDetailUrls = @json(
        collect($countries ?? [])->mapWithKeys(fn ($country) => [(string) $country->id => country_detail_url($country)])->all()
    );

    $(document).ready(function() {
        // Toggle region cards
        $('.region-card-header').on('click', function(e) {
            if ($(e.target).closest('.js-map-region-filter').length) {
                return;
            }
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

    });
</script>
@if($map_indicators->isNotEmpty())
    @include('countries.partials.map_script')
@endif
@endsection