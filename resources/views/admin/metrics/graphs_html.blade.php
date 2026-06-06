@php
    $mapIndicators = $map_indicators ?? collect();
    $mapRegions = $map_regions ?? collect();
    $initialKpiMap = $initial_kpi_map ?? null;
    $continentalIndicators = $continental_indicators ?? [];
    $green = settings()->au_corporate_green ?? '#1A5632';
    $red = settings()->au_red ?? '#9F2241';
@endphp
<style>
    .metrics-panel {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
    }
    .metrics-panel__header {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .metrics-panel__title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; }
    .metrics-panel__subtitle { font-size: 0.82rem; color: #64748b; margin: 0.15rem 0 0; }
    .metrics-panel__body { padding: 1.5rem; }
    .metrics-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
        margin-bottom: 1.25rem;
    }
    .metrics-kpi {
        background: linear-gradient(145deg, #fff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        position: relative;
        overflow: hidden;
    }
    .metrics-kpi::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, {{ $green }}, {{ $red }});
        opacity: 0.85;
    }
    .metrics-kpi__value { font-size: 1.5rem; font-weight: 800; color: {{ $green }}; line-height: 1.1; }
    .metrics-kpi__label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; margin-top: 0.25rem; }
    .metrics-live-row { margin-bottom: 1.25rem; }
    .metrics-live-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fff;
        height: 100%;
        overflow: hidden;
    }
    .metrics-live-card__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .metrics-live-card__head h4 { font-size: 0.9rem; font-weight: 700; margin: 0; color: #334155; }
    .metrics-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: {{ $green }};
        background: #ecfdf5;
        border: 1px solid rgba(26, 86, 50, 0.2);
        border-radius: 999px;
        padding: 0.2rem 0.55rem;
    }
    .metrics-live-badge__dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: {{ $green }};
        animation: metrics-pulse 1.8s ease-in-out infinite;
    }
    @keyframes metrics-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.45; transform: scale(0.85); }
    }
    .metrics-live-card__body { padding: 0.5rem 0.75rem 0.75rem; min-height: 260px; }
    .metrics-map-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 1.25rem;
    }
    .metrics-map-card__head {
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #f0f7f4 0%, #fff 100%);
        border-bottom: 1px solid #e2e8f0;
    }
    .metrics-map-card__head h3 { font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem; }
    .admin-africa-map-tabs { border-bottom: 2px solid #e2e8f0; margin-bottom: 1rem; gap: 0; }
    .admin-africa-map-tabs .nav-link {
        border: none; border-radius: 8px 8px 0 0; color: #64748b; font-weight: 600; font-size: 0.9rem;
        padding: 0.65rem 1.1rem; cursor: pointer; background: transparent;
    }
    .admin-africa-map-tabs .nav-link.active {
        color: {{ $green }};
        background: #f0f7f4;
        box-shadow: inset 0 -3px 0 {{ $green }};
    }
    .admin-map-scope-summary {
        background: linear-gradient(135deg, #f0f7f4 0%, #fff 100%);
        border: 1px solid rgba(26, 86, 50, 0.15);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
    .admin-map-scope-summary__value {
        font-size: 1.2rem; font-weight: 700; color: {{ $red }}; margin-left: 0.5rem;
    }
    .admin-map-scope-summary__meta { display: block; font-size: 0.78rem; color: #64748b; margin-top: 0.25rem; }
    .admin-map-stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1rem; }
    .admin-map-stat {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.85rem; text-align: center;
    }
    .admin-map-stat strong { display: block; font-size: 1.25rem; color: {{ $green }}; line-height: 1.2; }
    .admin-map-stat span { font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; }
    .admin-map-controls { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; align-items: flex-end; }
    .admin-map-controls label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 0.25rem; display: block; }
    .admin-map-controls select {
        min-width: 200px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.65rem; background: #f8fafc; font-size: 0.9rem;
    }
    #admin-africa-map { min-height: 480px; border-radius: 10px; border: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc 0%, #fff 100%); overflow: hidden; }
    .admin-map-legend {
        margin-top: 1rem; padding: 1rem; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0;
    }
    .admin-map-legend__title { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; margin-bottom: 0.5rem; }
    .admin-map-legend__bar { height: 12px; border-radius: 999px; background: linear-gradient(90deg, #f0f7f4 0%, {{ $green }} 100%); border: 1px solid rgba(26,86,50,0.2); margin-bottom: 0.4rem; }
    .admin-map-legend__labels { display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600; }
    .admin-continental-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; }
    .admin-continental-chip {
        border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.35rem 0.75rem; font-size: 0.75rem; background: #fff; cursor: pointer;
        transition: all 0.15s ease;
    }
    .admin-continental-chip:hover, .admin-continental-chip.is-active {
        border-color: {{ $green }};
        background: #f0f7f4; color: {{ $green }};
    }
    .metrics-secondary-charts .card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: none;
    }
    .metrics-secondary-charts .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.85rem 1.1rem;
    }
    .metrics-cache-hint {
        font-size: 0.72rem;
        color: #94a3b8;
        margin-top: 0.5rem;
    }
    .metrics-cache-hint.is-redis { color: {{ $green }}; }
</style>

<div class="col-md-12">
    <div class="metrics-panel">
        <div class="metrics-panel__header">
            <div>
                <h3 class="metrics-panel__title"><i class="fa fa-chart-area mr-1"></i> Analytics &amp; Insights</h3>
                <p class="metrics-panel__subtitle">Portal traffic, registrations, and member-state indicators</p>
            </div>
            <div class="filters-toolbar">
                <div class="filter-item metrics-period-presets">
                    <button type="button" class="btn btn-sm btn-outline-secondary metrics-preset" data-preset="7">7 days</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary metrics-preset" data-preset="30">30 days</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary metrics-preset" data-preset="90">90 days</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary metrics-preset" data-preset="ytd">YTD</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary metrics-preset" data-preset="all">All time</button>
                </div>
                <div class="filter-item">
                    <i class="fa fa-calendar filter-icon"></i>
                    <input type="date" id="fromDate" class="filter-control" value="{{ $from ?? '' }}" />
                </div>
                <div class="filter-item">
                    <i class="fa fa-calendar filter-icon"></i>
                    <input type="date" id="toDate" class="filter-control" value="{{ $to ?? '' }}" />
                </div>
                <div class="filter-item">
                    <i class="fa fa-globe filter-icon"></i>
                    <select id="countryFilter" class="filter-control" style="min-width:200px;">
                        <option value="">All Countries</option>
                    </select>
                </div>
                <button id="applyFilters" class="btn btn-apply"><i class="fa fa-filter mr-1"></i>Apply</button>
            </div>
        </div>

        <div class="metrics-panel__body">
            <div id="metricsKpiGrid" class="metrics-kpi-grid">
                <div class="metrics-kpi"><div class="metrics-kpi__value" id="kpiTotalVisits">—</div><div class="metrics-kpi__label">Visits (period)</div></div>
                <div class="metrics-kpi"><div class="metrics-kpi__value" id="kpiTotalSignups" style="color:{{ $red }}">—</div><div class="metrics-kpi__label">Signups (period)</div></div>
                <div class="metrics-kpi"><div class="metrics-kpi__value" id="kpiVisitCountries">—</div><div class="metrics-kpi__label">Countries (visits)</div></div>
                <div class="metrics-kpi"><div class="metrics-kpi__value" id="kpiSignupCountries" style="color:{{ $red }}">—</div><div class="metrics-kpi__label">Countries (signups)</div></div>
            </div>

            <div class="row metrics-live-row">
                <div class="col-lg-6 mb-3 mb-lg-0">
                    <div class="metrics-live-card">
                        <div class="metrics-live-card__head">
                            <h4><i class="fa fa-chart-line mr-1"></i> Visits over time</h4>
                            <span class="metrics-live-badge"><span class="metrics-live-badge__dot"></span> Live</span>
                        </div>
                        <div class="metrics-live-card__body">
                            <div id="visits-over-time-chart" style="width:100%;height:240px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="metrics-live-card">
                        <div class="metrics-live-card__head">
                            <h4><i class="fa fa-user-plus mr-1"></i> Signups over time</h4>
                            <span class="metrics-live-badge"><span class="metrics-live-badge__dot"></span> Live</span>
                        </div>
                        <div class="metrics-live-card__body">
                            <div id="signups-over-time-chart" style="width:100%;height:240px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="metrics-map-card">
                <div class="metrics-map-card__head">
                    <h3><i class="fa fa-globe-africa mr-1"></i> Africa map</h3>
                    <p class="text-muted mb-0 small">Portal traffic and published member-state indicators</p>
                </div>
                <div style="padding: 1.25rem 1.5rem;">
                    <ul class="nav admin-africa-map-tabs" id="adminAfricaMapTabs" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" data-map-mode="visits" id="adminMapTabVisits">
                                <i class="fa fa-chart-line mr-1"></i> Portal traffic
                            </button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" data-map-mode="indicators" id="adminMapTabIndicators">
                                <i class="fa fa-chart-bar mr-1"></i> Member state indicators
                            </button>
                        </li>
                    </ul>

                    <div id="adminMapScopeSummary" class="admin-map-scope-summary"></div>
                    <div id="adminVisitsMapStats" class="admin-map-stat-grid"></div>

                    <div id="adminKpiMapControls" class="admin-map-controls" style="display:none;">
                        <div>
                            <label for="adminMapIndicatorSelect">Indicator</label>
                            <select id="adminMapIndicatorSelect">
                                @forelse($mapIndicators as $indicator)
                                    <option value="{{ $indicator->id }}" @if(($initialKpiMap['kpi_id'] ?? null) == $indicator->id) selected @endif>{{ $indicator->name }}</option>
                                @empty
                                    <option value="">No published indicators</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label for="adminMapRegionSelect">Region scope</label>
                            <select id="adminMapRegionSelect">
                                <option value="">All Africa (member states)</option>
                                @foreach($mapRegions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="admin-africa-map"></div>

                    <div class="admin-map-legend">
                        <div class="admin-map-legend__title" id="adminMapLegendTitle">Map scale</div>
                        <div class="admin-map-legend__bar" id="adminMapLegendBar"></div>
                        <div class="admin-map-legend__labels">
                            <span id="adminMapLegendMin">—</span>
                            <span>Low → High</span>
                            <span id="adminMapLegendMax">—</span>
                        </div>
                    </div>

                    @if(count($continentalIndicators) > 0)
                    <div id="adminContinentalChips" style="display:none;">
                        <p class="small text-muted mb-2 mt-3 mb-2">Continental aggregates — click to load on map:</p>
                        <div class="admin-continental-chips">
                            @foreach(array_slice($continentalIndicators, 0, 12) as $indicator)
                                @php $d = $indicator['display'] ?? []; @endphp
                                <button type="button" class="admin-continental-chip js-admin-kpi-chip" data-kpi-id="{{ $indicator['kpi_id'] }}">
                                    {{ \Illuminate\Support\Str::limit($indicator['name'], 42) }}:
                                    <strong>{{ ($d['type'] ?? '') === 'percent' ? rtrim($d['value'] ?? '').'%' : ($d['value'] ?? '—') }}</strong>
                                </button>
                            @endforeach
                        </div>
                        @include('common.owid_attribution', ['compact' => true])
                    </div>
                    @endif

                    <p id="visitsMapPeriodLabel" class="text-muted mb-0 small mt-3">Showing all recorded visits</p>
                </div>
            </div>

            <div id="chart-container" class="row metrics-secondary-charts" style="margin-left: -12px; margin-right: -12px;"></div>
            <p id="metricsCacheHint" class="metrics-cache-hint mb-0"></p>
        </div>
    </div>
</div>

<script>
    window.__adminInitialKpiMap = @json($initialKpiMap);
    window.__adminMapDataUrl = @json($map_data_url ?? route('countries.map-data'));
    window.__adminMapRegions = @json($mapRegions->map(fn ($r) => ['id' => (int) $r->id, 'name' => $r->region_name])->values());
    window.__metricsMapModuleUrl = @json(asset('assets/plugins/highcharts/modules/map.js'));
    window.__metricsLiveUrl = @json(url('admin/metrics/live'));
</script>
