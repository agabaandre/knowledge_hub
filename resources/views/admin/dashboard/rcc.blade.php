@extends(admin_layout())

@php
    $green = settings()->au_corporate_green ?? '#1A5632';
    $red = settings()->au_red ?? '#9F2241';
    $gold = settings()->au_gold ?? '#B4A269';
@endphp

@section('styles')
@include('common.select2')
<style>
    .rcc-panel {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 24px rgba(15, 23, 42, 0.06);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .rcc-panel__head {
        background: linear-gradient(135deg, #f8fafc 0%, #f0f7f4 100%);
        border-bottom: 1px solid #e2e8f0;
        padding: 1.25rem 1.5rem;
    }
    .rcc-panel__body { padding: 1.25rem 1.5rem; }
    .rcc-filters {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
        align-items: end;
    }
    .rcc-filters label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        margin-bottom: 0.35rem;
        display: block;
    }
    .rcc-filters .form-control, .rcc-filters .select2-container--default .select2-selection--single {
        border-radius: 10px !important;
        border-color: #e2e8f0 !important;
        min-height: 38px;
    }
    .rcc-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 0;
        margin-bottom: 1.25rem;
    }
    .rcc-nav-tabs .nav-link {
        border: none;
        border-radius: 10px 10px 0 0;
        color: #64748b;
        font-weight: 600;
        padding: 0.65rem 1.1rem;
    }
    .rcc-nav-tabs .nav-link.active {
        color: {{ $green }};
        background: #f0f7f4;
        box-shadow: inset 0 -3px 0 {{ $green }};
    }
    .rcc-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.85rem;
        margin-bottom: 1.25rem;
    }
    .rcc-kpi-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.9rem 1rem;
        background: linear-gradient(145deg, #fff, #f8fafc);
        position: relative;
        overflow: hidden;
    }
    .rcc-kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, {{ $green }}, {{ $gold }});
    }
    .rcc-kpi-card__name { font-size: 0.8rem; font-weight: 600; color: #334155; line-height: 1.35; margin-bottom: 0.35rem; }
    .rcc-kpi-card__value { font-size: 1.15rem; font-weight: 800; color: {{ $red }}; }
    .rcc-kpi-card__unit { font-size: 0.72rem; color: #64748b; }
    .rcc-kpi-card__yoy { font-size: 0.7rem; color: #64748b; margin-top: 0.4rem; }
    .rcc-subject-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: {{ $green }};
        margin: 1.25rem 0 0.75rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid #e2e8f0;
    }
    #rccMapChart { min-height: 460px; border-radius: 12px; border: 1px solid #e2e8f0; background: #f8fafc; }
    #rccMainChart { min-height: 420px; }
    .rcc-subject-charts {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1rem;
        margin-top: 1.25rem;
    }
    .rcc-subject-chart {
        min-height: 300px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.5rem;
        background: #fafbfc;
    }
    .rcc-summary-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem; }
    .rcc-summary-chip {
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        background: #fff;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .rcc-summary-chip:hover, .rcc-summary-chip.is-active {
        border-color: {{ $green }};
        background: #f0f7f4;
        color: {{ $green }};
    }
    .rcc-data-table { font-size: 0.85rem; }
    .rcc-data-table thead th {
        background: #f8fafc;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
    }
    .rcc-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }
    .rcc-scope-banner {
        background: linear-gradient(135deg, #f0f7f4 0%, #fff 100%);
        border: 1px solid rgba(26, 86, 50, 0.15);
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1 class="page-title"><i class="fa fa-globe-africa me-2"></i>RCC Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('admin/kpi') }}">KPIs</a></li>
            <li class="breadcrumb-item active" aria-current="page">RCC Dashboard</li>
        </ol>
    </div>
</div>

<div class="container-fluid">
    <div class="rcc-panel">
        <div class="rcc-panel__head">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1 fw-bold">Regional Coordinating Centre performance</h2>
                    <p class="text-muted mb-0 small">Published indicators from Our World in Data — filter by RCC, member state, subject area, or indicator.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('admin/kpi') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-cog me-1"></i> Manage indicators</a>
                    <a href="{{ url('admin/kpi/data') }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-table me-1"></i> Country values</a>
                    <a href="{{ route('countries') }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="fa fa-external-link-alt me-1"></i> Public map</a>
                </div>
            </div>
            <form id="rccFilterForm" class="rcc-filters" method="get" action="{{ route('admin.rccdashboards') }}">
                <div>
                    <label for="rccRegion">RCC / Region</label>
                    <select name="region_id" id="rccRegion" class="form-control select2">
                        <option value="">All Africa</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @if(!empty($filter['region_id']) && (int) $filter['region_id'] === (int) $region->id) selected @endif>{{ $region->region_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rccCountry">Member state</label>
                    <select name="country_id" id="rccCountry" class="form-control select2">
                        <option value="">All in scope</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" data-region="{{ $country->region_id }}" @if(!empty($filter['country_id']) && (int) $filter['country_id'] === (int) $country->id) selected @endif>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rccYear">Year</label>
                    <select name="period_year" id="rccYear" class="form-control select2">
                        @php
                            $defaultYear = (int) ($default_period_year ?? ($years[0] ?? date('Y')));
                            $selectedYear = ! empty($filter['period_year']) ? (int) $filter['period_year'] : $defaultYear;
                            if (! in_array($selectedYear, array_map('intval', $years ?? []), true) && ! empty($years)) {
                                $selectedYear = (int) $years[0];
                            }
                        @endphp
                        @foreach($years as $year)
                            <option value="{{ $year }}" @if($selectedYear === (int) $year) selected @endif>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rccSubject">Subject area</label>
                    <select name="subject_area" id="rccSubject" class="form-control select2">
                        <option value="">All subject areas</option>
                        @foreach($subjectareas as $subject)
                            <option value="{{ $subject->id }}" @if(!empty($filter['subject_area']) && (int) $filter['subject_area'] === (int) $subject->id) selected @endif>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rccIndicator">Indicator</label>
                    <select name="kpi_id" id="rccIndicator" class="form-control select2">
                        <option value="">All published indicators</option>
                        @foreach($indicators as $indicator)
                            <option value="{{ $indicator->id }}" data-subject="{{ $indicator->subject_area }}" @if(!empty($filter['kpi_id']) && (int) $filter['kpi_id'] === (int) $indicator->id) selected @endif>{{ $indicator->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="rccChartType">Chart type</label>
                    <select name="chart_type" id="rccChartType" class="form-control select2">
                        <option value="column" @if(($filter['chart_type'] ?? 'column') === 'column') selected @endif>Column</option>
                        <option value="bar" @if(($filter['chart_type'] ?? '') === 'bar') selected @endif>Bar</option>
                        <option value="line" @if(($filter['chart_type'] ?? '') === 'line') selected @endif>Line</option>
                        <option value="areaspline" @if(($filter['chart_type'] ?? '') === 'areaspline') selected @endif>Area</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="rcc-panel__body">
            <ul class="nav rcc-nav-tabs" id="rccTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="rccTabOverviewBtn" href="#rccTabOverview" role="tab" aria-controls="rccTabOverview" aria-selected="true">Overview</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="rccTabChartsBtn" href="#rccTabCharts" role="tab" aria-controls="rccTabCharts" aria-selected="false">Charts</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="rccTabDataBtn" href="#rccTabData" role="tab" aria-controls="rccTabData" aria-selected="false">Data table</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="rccTabOverview" role="tabpanel" aria-labelledby="rccTabOverviewBtn">
                    <div id="rccScopeBanner" class="rcc-scope-banner"></div>
                    <div id="rccSummaryChips" class="rcc-summary-chips"></div>
                    <div id="rccKpiCards"></div>
                    <div id="rccMapChart"><div class="rcc-empty"><i class="fa fa-spinner fa-spin me-2"></i>Loading map…</div></div>
                    @include('common.owid_attribution', ['compact' => true])
                </div>
                <div class="tab-pane fade" id="rccTabCharts" role="tabpanel" aria-labelledby="rccTabChartsBtn">
                    <div id="rccMainChart"><div class="rcc-empty">Select filters to load chart</div></div>
                    <div id="rccSubjectCharts" class="rcc-subject-charts"></div>
                    @include('common.owid_attribution', ['compact' => true])
                </div>
                <div class="tab-pane fade" id="rccTabData" role="tabpanel" aria-labelledby="rccTabDataBtn">
                    <div class="table-responsive">
                        <table class="table table-hover rcc-data-table" id="rccDataTable">
                            <thead>
                                <tr>
                                    <th>Member state</th>
                                    <th>Indicator</th>
                                    <th>Period</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody id="rccDataTableBody"></tbody>
                        </table>
                    </div>
                    @include('common.owid_attribution', ['compact' => true])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.__rccDataUrl = @json(route('admin.rccdashboards.data'));
    window.__rccMapModuleUrl = @json(asset('assets/plugins/highcharts/modules/map.js'));
    window.__rccMapTopologyUrl = 'https://code.highcharts.com/mapdata/custom/world-highres3.topo.json';
    window.__rccInitialPayload = @json($initial_payload);
    window.__rccRegions = @json($regions_json);
    window.__rccColors = {
        green: @json($green),
        red: @json($red),
        gold: @json($gold),
        palette: [@json($green), @json($red), @json($gold), '#3D96AE', '#80699B', '#AA4643', '#89A54E', '#4572A7']
    };
</script>
@include('admin.dashboard.partials.rcc_dashboard_script')
@endsection
