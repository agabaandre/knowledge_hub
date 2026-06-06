@extends(admin_layout())

@section('styles')
    @include('common.table')
    @include('admin.publications.partials.filter_styles')
<style>
    .stat-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        display: block;
        text-decoration: none;
        color: inherit;
    }
    .stat-card:hover {
        text-decoration: none;
        color: inherit;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: {{ settings()->au_corporate_green ?? '#1A5632' }};
        margin-bottom: 0.5rem;
    }
    .stat-card .stat-label {
        font-size: 0.875rem;
        color: {{ settings()->au_grey_text ?? '#58595B' }};
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    .stat-card .stat-icon {
        font-size: 1.5rem;
        color: {{ settings()->au_corporate_green ?? '#1A5632' }};
        margin-top: 0.5rem;
    }
    .stat-card .stat-link {
        font-size: 0.75rem;
        color: {{ settings()->au_grey_text ?? '#58595B' }};
        margin-top: 0.5rem;
        display: block;
    }
    .card {
        border: 1px solid #e2e8f0;
        border-radius: 0;
        margin-bottom: 1.5rem;
    }
    .card-header {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
    }
    .card-body {
        padding: 1.5rem;
    }
    /* Most Recent Resources: ensure STATUS badges are readable (no white text on light) */
    #resource-table tbody td .badge-success,
    #resource-table tbody td .badge-warning {
        color: #1a1a1a !important;
        background-color: #d4edda;
    }
    #resource-table tbody td .badge-warning {
        background-color: #fff3cd;
    }
    #resource-table tbody td .badge-danger {
        color: #fff !important;
        background-color: #dc3545;
    }
    #resource-table_wrapper table.dataTable { table-layout: fixed !important; }
    #resource-table .dash-col-index { width: 3rem; min-width: 3rem; }
    #resource-table .dash-col-date { width: 9%; }
    #resource-table .dash-col-title { width: 18%; }
    #resource-table .dash-col-description { width: 28%; }
    #resource-table .dash-col-author { width: 12%; }
    #resource-table .dash-col-status { width: 8%; }
    #resource-table .dash-col-actions { width: 7rem; white-space: nowrap; }
    .pub-desc-preview {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--theme-color-primary, #119A48) !important;
        text-decoration: none !important;
        white-space: nowrap;
    }
    .pub-desc-preview:hover { text-decoration: underline !important; }
    #pubDescriptionPreviewBody {
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.6;
        color: #334155;
    }
    /* Metrics filters (injected) */
    .charts .filters-toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .charts .filters-toolbar .filter-item { position: relative; }
    .charts .filters-toolbar .filter-control {
        height: 36px; padding: 6px 12px 6px 34px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f8fafc; color: #0f172a; outline: none;
    }
    .charts .filters-toolbar .filter-control:focus { border-color: #cbd5e1; background: #fff; }
    .charts .filters-toolbar .filter-icon { position: absolute; left: 10px; top: 9px; color: #58595B; font-size: 14px; }
    .charts .btn-apply {
        height: 36px; border-radius: 10px; background: {{ settings()->au_corporate_green ?? '#1A5632' }}; color: #fff; padding: 6px 14px; border: 1px solid {{ settings()->au_corporate_green ?? '#1A5632' }};
    }
    .charts .btn-apply:hover { opacity: 0.9; color: #fff; }
</style>
@endsection

@section('content')
<!-- PAGE-HEADER -->
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>
<!-- PAGE-HEADER END -->

<div class="container-fluid">
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('admin/publications') }}" class="stat-card">
                <div class="stat-value">{{ $publications_count }}</div>
                <div class="stat-label">Publications</div>
                <div class="stat-icon"><i class="fa fa-pen"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('admin/authors') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_green ?? '#1A5632' }};">{{ $authors_count }}</div>
                <div class="stat-label">Resource Authors</div>
                <div class="stat-icon" style="color: {{ settings()->au_green ?? '#1A5632' }};"><i class="fa fa-users"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('admin/experts') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_gold ?? '#B4A269' }};">{{ $experts_count }}</div>
                <div class="stat-label">Workforce Experts</div>
                <div class="stat-icon" style="color: {{ settings()->au_gold ?? '#B4A269' }};"><i class="fas fa-user-graduate"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('admin/forums') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_gold ?? '#B4A269' }};">{{ $forums_count }}</div>
                <div class="stat-label">Forum Discussions</div>
                <div class="stat-icon" style="color: {{ settings()->au_gold ?? '#B4A269' }};"><i class="fab fa-forumbee"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_red ?? '#9F2241' }};">{{ $states_count }}</div>
                <div class="stat-label">Total Member States</div>
                <div class="stat-icon" style="color: {{ settings()->au_red ?? '#9F2241' }};"><i class="fas fa-globe-africa"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('admin/logs/user') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_red ?? '#9F2241' }};">{{ $visits_count }}</div>
                <div class="stat-label">Avg Daily Visits</div>
                <div class="stat-icon" style="color: {{ settings()->au_red ?? '#9F2241' }};"><i class="fas fa-signal"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ states_enabled() ? url('admin/areas') : url('admin/adminunits') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_grey_text ?? '#58595B' }};">{{ $admin_units_count }}</div>
                <div class="stat-label">Total Administrative Units</div>
                <div class="stat-icon" style="color: {{ settings()->au_grey_text ?? '#58595B' }};"><i class="far fa-building"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
        <div class="col-md-4 col-lg-3 mb-4">
            <a href="{{ url('permissions/users') }}" class="stat-card">
                <div class="stat-value" style="color: {{ settings()->au_corporate_green ?? '#1A5632' }};">{{ $users_count }}</div>
                <div class="stat-label">Total Platform Users</div>
                <div class="stat-icon" style="color: {{ settings()->au_corporate_green ?? '#1A5632' }};"><i class="fas fa-users-cog"></i></div>
                <div class="stat-link">View list <i class="fa fa-arrow-right ml-1"></i></div>
            </a>
        </div>
    </div>

    <div class="row charts" style="row-gap:12px;"></div>

    {{-- Admin-only Dashboards List --}}
    @if(isset($dashboards) && count($dashboards))
    <div class="row mt-4" style="row-gap:12px;">
        <div class="col-md-12">
            <div class="card shadow-sm" style="border-radius:12px;">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Admin Dashboards</h4>
                    <span class="text-muted small">{{ count($dashboards) }} available</span>
                </div>
                <div class="card-body">
                    <div class="row" style="row-gap:12px;">
                        @foreach($dashboards as $db)
                        @php
                            // Get image with proper fallback logic like frontend
                            $raw_cover = $db->getRawOriginal('cover');
                            $cover_is_external = $db->cover_is_exteranl ?? false;
                            
                            // Determine image link
                            if (!empty($raw_cover)) {
                                if ($cover_is_external) {
                                    // External URL - use as is
                                    $image_link = $raw_cover;
                                } else {
                                    // Local file - build storage path
                                    $image_link = storage_link('uploads/publications/' . $raw_cover);
                                }
                            } else {
                                // No cover - use default
                                $image_link = null;
                            }
                            
                            // Default image
                            $default_image = asset('assets/images/cover.png');
                            
                            // Final image to use
                            $final_image = (!empty($image_link) && filter_var($image_link, FILTER_VALIDATE_URL)) 
                                ? $image_link 
                                : $default_image;
                            
                            $statusText = $db->is_approved ? 'Approved' : ($db->is_rejected ? 'Rejected' : 'Pending');
                            $statusClass = $db->is_approved ? 'badge-success' : ($db->is_rejected ? 'badge-danger' : 'badge-secondary');
                            
                            // Use publication link directly if is_embedded != 1, otherwise use admin view
                            $publication_url = $db->publication ?? '#';
                            $url = ($db->is_embedded == 1) 
                                ? url('/admin/dashboards').'?resource='.$db->id 
                                : $publication_url;
                            
                            // Check if URL is external (doesn't match APP_URL)
                            $app_url = config('app.url');
                            $url_parsed = parse_url($url);
                            $app_url_parsed = parse_url($app_url);
                            $is_external = isset($url_parsed['host']) && 
                                          isset($app_url_parsed['host']) && 
                                          $url_parsed['host'] !== $app_url_parsed['host'];
                        @endphp
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100" style="border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                                <div style="height:140px;background:#f8fafc;background-position:center;background-size:cover;overflow:hidden;">
                                    <img src="{{ $final_image }}" 
                                         alt="{{ strip_tags($db->title) }}" 
                                         style="width:100%;height:100%;object-fit:cover;"
                                         onerror="this.onerror=null; this.src='{{ $default_image }}';">
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h6 class="mb-1" style="font-weight:700;line-height:1.2;">{{ strip_tags(Str::limit($db->title, 60)) }}</h6>
                                    @if(!empty($db->theme))
                                        <div class="text-muted" style="font-size:.85rem;">{!! Str::limit($db->theme->description ?? '', 60) !!}</div>
                                    @endif
                                    <p class="mt-2 mb-3 text-muted" style="font-size:.9rem;">{{ Str::limit(strip_tags($db->description), 100) }}</p>
                                    <div class="mt-auto d-flex align-items-center justify-content-between">
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                        <a href="{{ $url }}" target="{{ $is_external ? '_blank' : '_self' }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-chart-line mr-1"></i> Open
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card pub-list-card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Most Recent Resources</h3>
                </div>
                <div class="card-body">
                    <div class="pub-filters-card mb-3">
                        <div class="pub-filters-card__header">
                            <div class="pub-filters-card__heading">
                                <span class="pub-filters-card__icon"><i class="fa fa-filter"></i></span>
                                <div>
                                    <h3 class="pub-filters-card__title">Filter Resources</h3>
                                    <p class="pub-filters-card__subtitle">Filters apply automatically as you type</p>
                                </div>
                            </div>
                        </div>
                        <div class="pub-filters-card__body">
                            <form id="dashboardResourcesFiltersForm" method="GET" action="{{ url('admin') }}" class="mb-0">
                                <div class="pub-filters-grid">
                                    <div class="pub-filter-field">
                                        <label class="pub-filter-label" for="filterResourceTitle">Title</label>
                                        <input type="text" name="search[title]" id="filterResourceTitle" class="form-control pub-filter-input" placeholder="Filter by title" value="{{ request('search.title') }}">
                                    </div>
                                    <div class="pub-filter-field">
                                        <label class="pub-filter-label" for="filterResourceAuthor">Author</label>
                                        <input type="text" name="search[author]" id="filterResourceAuthor" class="form-control pub-filter-input" placeholder="Filter by author" value="{{ request('search.author') }}">
                                    </div>
                                    <div class="pub-filter-field">
                                        <label class="pub-filter-label" for="filterResourceDescription">Description</label>
                                        <input type="text" name="search[description]" id="filterResourceDescription" class="form-control pub-filter-input" placeholder="Filter by description" value="{{ request('search.description') }}">
                                    </div>
                                </div>
                                <div class="pub-filters-actions">
                                    <a href="{{ url('admin') }}" class="pub-filters-btn pub-filters-btn--clear" id="clearDashboardResourceFilters">
                                        <i class="fa fa-rotate-left"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="publication-table-wrap">
                        <table id="resource-table" data-kh-datatable="custom" class="table table-striped table-bordered table-hover w-100 kh-table-wrap-cells">
                            <thead>
                                <tr>
                                    <th class="dash-col-index">#</th>
                                    <th class="dash-col-date">Created</th>
                                    <th class="dash-col-title">Title</th>
                                    <th class="dash-col-description">Description</th>
                                    <th class="dash-col-author">Author</th>
                                    <th class="dash-col-status">Status</th>
                                    <th class="dash-col-actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pubDescriptionPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pubDescriptionPreviewTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pubDescriptionPreviewTitle">Description</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="pubDescriptionPreviewBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
@include('admin.metrics.charts_script')
@include('admin.publications.partials.datatable_assets')
<script>
    let dashboardResourcesTable = null;
    let dashboardFilterTimer = null;

    function collectDashboardResourceFilterParams() {
        const params = {};
        $('#dashboardResourcesFiltersForm').find('input').each(function () {
            const $el = $(this);
            const name = $el.attr('name');
            if (!name) return;
            const value = $el.val();
            if (value !== null && value !== '') {
                params[name] = value;
            }
        });
        return params;
    }

    function reloadDashboardResourcesTable() {
        if (dashboardResourcesTable) dashboardResourcesTable.ajax.reload();
    }

    function scheduleDashboardFilterReload() {
        clearTimeout(dashboardFilterTimer);
        dashboardFilterTimer = setTimeout(reloadDashboardResourcesTable, 350);
    }

    $(document).ready(function() {
        dashboardResourcesTable = $('#resource-table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            autoWidth: false,
            scrollX: false,
            pageLength: 10,
            lengthMenu: [[10, 20, 50, 100], [10, 20, 50, 100]],
            order: [[1, 'desc']],
            ajax: {
                url: '{{ url('admin') }}',
                data: function (d) {
                    d.datatable = 1;
                    return Object.assign(d, collectDashboardResourceFilterParams());
                }
            },
            columns: [
                { data: 'index', orderable: false, searchable: false, className: 'dash-col-index text-center' },
                { data: 'date_created', orderable: true, className: 'dash-col-date' },
                { data: 'title', orderable: true, className: 'dash-col-title' },
                { data: 'description', orderable: true, className: 'dash-col-description' },
                { data: 'author', orderable: false, className: 'dash-col-author' },
                { data: 'status', orderable: true, searchable: false, className: 'dash-col-status' },
                { data: 'actions', orderable: false, searchable: false, className: 'dash-col-actions text-center' }
            ],
            columnDefs: [
                { targets: [0, 4, 6], orderable: false }
            ],
            language: {
                processing: '<i class="fa fa-spinner fa-spin"></i> Loading resources...',
                emptyTable: 'No resources match your filters.',
                zeroRecords: 'No matching resources found.'
            }
        });

        $('#filterResourceTitle, #filterResourceAuthor, #filterResourceDescription').on('input', scheduleDashboardFilterReload);

        $('#dashboardResourcesFiltersForm').on('submit', function (e) {
            e.preventDefault();
            reloadDashboardResourcesTable();
        });

        $('#clearDashboardResourceFilters').on('click', function (e) {
            e.preventDefault();
            window.location.href = '{{ url('admin') }}';
        });

        $(document).on('click', '.pub-desc-preview', function (e) {
            e.preventDefault();
            var title = $(this).attr('data-title') || 'Description';
            var description = $(this).attr('data-description') || '';
            try {
                description = JSON.parse(description);
            } catch (err) {
                // keep raw string fallback
            }
            $('#pubDescriptionPreviewTitle').text(title);
            $('#pubDescriptionPreviewBody').text(description);
            $('#pubDescriptionPreviewModal').modal('show');
        });

        var metricsUrl = '{{ url("admin/metrics") }}';
        function loadMetrics(params) {
            var url = metricsUrl;
            if (params && (params.from || params.to || params.country)) {
                url += (url.indexOf('?') === -1 ? '?' : '&') + $.param(params);
            }
            $.ajax({
                method: 'GET',
                url: url,
                dataType: 'json',
                success: function(response) {
                    if (response && response.html) {
                        $('.charts').html(response.html);
                    }
                    if (response && response.chart_data && typeof window.renderMetricsCharts === 'function') {
                        window.renderMetricsCharts(response.chart_data);
                    }
                },
                error: function(xhr) {
                    if (xhr.responseJSON) {
                        $('.charts').html('<div class="col-12 alert alert-danger">Failed to load metrics.</div>');
                    } else {
                        $('.charts').html('<div class="col-12 alert alert-warning">Metrics not available (response may be HTML). Refresh the page.</div>');
                    }
                }
            });
        }
        loadMetrics();
        $(document).on('click', '#applyFilters', function() {
            var from = $('#fromDate').val();
            var to = $('#toDate').val();
            var country = $('#countryFilter').val() || '';
            loadMetrics({ from: from || undefined, to: to || undefined, country: country || undefined });
        });
    });
</script>
@endsection
