@php
    $mapIndicators = $map_indicators ?? collect();
    $mapRegions = $map_regions ?? collect();
    $initialKpiMap = $initial_kpi_map ?? null;
    $continentalIndicators = $continental_indicators ?? [];
@endphp
<style>
    .admin-africa-map-tabs { border-bottom: 2px solid #e2e8f0; margin-bottom: 1rem; gap: 0; }
    .admin-africa-map-tabs .nav-link {
        border: none; border-radius: 8px 8px 0 0; color: #64748b; font-weight: 600; font-size: 0.9rem;
        padding: 0.65rem 1.1rem; cursor: pointer; background: transparent;
    }
    .admin-africa-map-tabs .nav-link.active {
        color: {{ settings()->au_corporate_green ?? '#1A5632' }};
        background: #f0f7f4;
        box-shadow: inset 0 -3px 0 {{ settings()->au_corporate_green ?? '#1A5632' }};
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
        font-size: 1.2rem; font-weight: 700; color: {{ settings()->au_red ?? '#9F2241' }}; margin-left: 0.5rem;
    }
    .admin-map-scope-summary__meta { display: block; font-size: 0.78rem; color: #64748b; margin-top: 0.25rem; }
    .admin-map-stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; margin-bottom: 1rem; }
    .admin-map-stat {
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.85rem; text-align: center;
    }
    .admin-map-stat strong { display: block; font-size: 1.25rem; color: {{ settings()->au_corporate_green ?? '#1A5632' }}; line-height: 1.2; }
    .admin-map-stat span { font-size: 0.72rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; }
    .admin-map-controls { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem; align-items: flex-end; }
    .admin-map-controls label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-bottom: 0.25rem; display: block; }
    .admin-map-controls select {
        min-width: 200px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem 0.65rem; background: #f8fafc; font-size: 0.9rem;
    }
    #admin-africa-map { min-height: 500px; border-radius: 10px; border: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc 0%, #fff 100%); overflow: hidden; }
    .admin-map-legend {
        margin-top: 1rem; padding: 1rem; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0;
    }
    .admin-map-legend__title { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; margin-bottom: 0.5rem; }
    .admin-map-legend__bar { height: 12px; border-radius: 999px; background: linear-gradient(90deg, #f0f7f4 0%, {{ settings()->au_corporate_green ?? '#1A5632' }} 100%); border: 1px solid rgba(26,86,50,0.2); margin-bottom: 0.4rem; }
    .admin-map-legend__labels { display: flex; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-weight: 600; }
    .admin-continental-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; }
    .admin-continental-chip {
        border: 1px solid #e2e8f0; border-radius: 999px; padding: 0.35rem 0.75rem; font-size: 0.75rem; background: #fff; cursor: pointer;
        transition: all 0.15s ease;
    }
    .admin-continental-chip:hover, .admin-continental-chip.is-active {
        border-color: {{ settings()->au_corporate_green ?? '#1A5632' }};
        background: #f0f7f4; color: {{ settings()->au_corporate_green ?? '#1A5632' }};
    }
</style>

<div class="col-md-12">
    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem;">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; gap: 12px;">
            <h3 class="card-title mb-0">System Metrics</h3>
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
        <div class="card-body" style="padding: 1.5rem;">
            <div id="chart-container" class="row" style="margin-left: -15px; margin-right: -15px;"></div>

            <div class="row" style="margin-left: -15px; margin-right: -15px; margin-top: 1.5rem;">
                <div class="col-12" style="padding-left: 15px; padding-right: 15px;">
                    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden;">
                        <div class="card-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
                            <h3 class="card-title mb-1" style="font-size: 1.05rem; font-weight: 700;">
                                <i class="fa fa-globe-africa mr-1"></i> Africa map
                            </h3>
                            <p class="text-muted mb-0 small">Portal traffic and published member-state indicators (same map as the public Member States page).</p>
                        </div>
                        <div class="card-body" style="padding: 1.25rem 1.5rem;">
                            <div id="visits-over-time-chart" style="width:100%;height:260px;margin-bottom:1.25rem;"></div>

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
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.__adminInitialKpiMap = @json($initialKpiMap);
    window.__adminMapDataUrl = @json($map_data_url ?? route('countries.map-data'));
    window.__adminMapRegions = @json($mapRegions->map(fn ($r) => ['id' => (int) $r->id, 'name' => $r->region_name])->values());
</script>
