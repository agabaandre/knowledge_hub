<div class="col-md-12">
    <div class="card" style="border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem;">
        <div class="card-header d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
            <h3 class="card-title mb-0">System Metrics</h3>
            <div class="filters-toolbar">
                <div class="filter-item">
                    <i class="fa fa-calendar filter-icon"></i>
                    <input type="text" id="fromDate" class="filter-control datepicker" placeholder="From" />
                </div>
                <div class="filter-item">
                    <i class="fa fa-calendar filter-icon"></i>
                    <input type="text" id="toDate" class="filter-control datepicker" placeholder="To" />
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
                        <div class="card-header d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
                            <h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">Visits by Country</h3>
                        </div>
                        <div class="card-body" style="padding: 1.5rem;">
                            <div id="world-map" style="width:100%;height:504px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
