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
                    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 0;">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem; gap: 8px;">
                            <div>
                                <h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">Visits by Country</h3>
                                <p id="visitsMapPeriodLabel" class="text-muted mb-0 small mt-1">Showing all recorded visits</p>
                            </div>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div id="visits-over-time-chart" style="width:100%;height:280px;margin-bottom:1.5rem;"></div>
                            <div id="world-map" style="width:100%;height:504px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
