@extends(admin_layout())

@section('styles')
<style>
    .maps-admin-page .maps-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
    }
    .maps-admin-page .maps-section-card {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .maps-admin-page .maps-section-card .card-header {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }
    .maps-admin-page .maps-section-card .card-header h4 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .maps-admin-page .maps-section-card .card-header p {
        margin: 0.25rem 0 0;
        font-size: 0.8125rem;
        color: #64748b;
    }
    .maps-admin-page .assignment-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .maps-admin-page .assignment-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (min-width: 1200px) {
        .maps-admin-page .assignment-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }
    .maps-admin-page .assignment-field label {
        font-weight: 600;
        font-size: 0.8125rem;
        color: #334155;
        margin-bottom: 0.35rem;
    }
    .maps-admin-page .assignment-field .form-select {
        font-size: 0.875rem;
        border-color: #cbd5e1;
        min-height: 2.5rem;
    }
    .maps-admin-page .assignment-field .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.15);
    }
    .maps-admin-page .assignment-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-top: 0.5rem;
        margin-top: 0.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .maps-admin-page .maps-table thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
        font-weight: 700;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .maps-admin-page .maps-table tbody td {
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .maps-admin-page .map-label-cell strong {
        display: block;
        color: #0f172a;
        font-size: 0.9375rem;
        line-height: 1.35;
        margin-bottom: 0.35rem;
    }
    .maps-admin-page .map-slug {
        display: inline-block;
        font-size: 0.75rem;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 0.35rem;
        padding: 0.1rem 0.45rem;
        margin-bottom: 0.45rem;
        word-break: break-all;
    }
    .maps-admin-page .map-badge-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
    }
    .maps-admin-page .map-badge {
        display: inline-flex;
        align-items: center;
        font-size: 0.6875rem;
        font-weight: 700;
        line-height: 1.2;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        padding: 0.2rem 0.5rem;
        border-radius: 999px;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .maps-admin-page .map-badge--builtin {
        color: #4338ca;
        background: #eef2ff;
        border-color: #c7d2fe;
    }
    .maps-admin-page .map-badge--country {
        color: #047857;
        background: #ecfdf5;
        border-color: #a7f3d0;
    }
    .maps-admin-page .map-badge--managed {
        color: #0e7490;
        background: #ecfeff;
        border-color: #a5f3fc;
    }
    .maps-admin-page .map-join-code {
        font-size: 0.8125rem;
        color: #b45309;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 0.35rem;
        padding: 0.15rem 0.45rem;
    }
    .maps-admin-page .topology-preset {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.75rem;
        height: 100%;
    }
    .maps-admin-page .topology-preset code {
        color: #4338ca;
        font-size: 0.8125rem;
    }
    .maps-admin-page .maps-stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.8125rem;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.25rem 0.65rem;
    }
    .maps-admin-page .maps-definitions-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .maps-admin-page .maps-definitions-search {
        position: relative;
        min-width: min(100%, 320px);
        max-width: 420px;
        flex: 1 1 280px;
    }
    .maps-admin-page .maps-definitions-search .form-control {
        padding-left: 2.25rem;
        padding-right: 2.25rem;
        border-color: #cbd5e1;
        font-size: 0.875rem;
    }
    .maps-admin-page .maps-definitions-search .search-icon {
        position: absolute;
        left: 0.8rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
    }
    .maps-admin-page .maps-definitions-search .search-clear {
        position: absolute;
        right: 0.35rem;
        top: 50%;
        transform: translateY(-50%);
        border: 0;
        background: transparent;
        color: #64748b;
        padding: 0.15rem 0.45rem;
        line-height: 1;
    }
    .maps-admin-page .maps-definitions-counter {
        font-size: 0.8125rem;
        font-weight: 600;
        color: #334155;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        white-space: nowrap;
    }
    .maps-admin-page .maps-definitions-counter.is-filtered {
        color: #4338ca;
        border-color: #c7d2fe;
        background: #eef2ff;
    }
    .maps-admin-page .map-row-num {
        width: 3rem;
        text-align: center;
        color: #94a3b8 !important;
    }
    .maps-admin-page .maps-table.is-loading {
        opacity: 0.55;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3 maps-admin-page">
    @if(session('message'))
        <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'danger' }}">{{ session('message') }}</div>
    @endif

    @if(empty($mapColumnsReady))
        <div class="alert alert-warning">
            Map preference columns are missing from the database
            @if(!empty($missingMapColumns))
                (<code>{{ implode('</code>, <code>', $missingMapColumns) }}</code>)
            @endif
            . On the server run <code>php artisan migrate</code>, then reload this page and save again.
        </div>
    @endif

    <div class="card maps-hero shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h3 class="mb-2"><i class="fa fa-map me-2 text-primary"></i>Maps Management</h3>
                    <p class="text-muted mb-2 mb-md-0" style="max-width: 52rem;">
                        Manage ISO-keyed choropleth maps using Highcharts Map Collection TopoJSON.
                        Assign maps to frontend views, dashboards, and country hubs.
                    </p>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="maps-stat-pill"><i class="fa fa-layer-group"></i> Collection v{{ $topologyVersion }}</span>
                        <span class="maps-stat-pill"><i class="fa fa-globe-africa"></i> {{ count($definitionGroups['African countries'] ?? []) }} African country maps</span>
                        <span class="maps-stat-pill"><i class="fa fa-database"></i> {{ count($definitions) }} definitions</span>
                    </div>
                </div>
                <a href="{{ route('admin.maps.create') }}" class="btn btn-success">
                    <i class="fa fa-plus me-1"></i>Add map definition
                </a>
            </div>
            <p class="small text-muted mt-3 mb-0">
                Topology base URL: <code>{{ config('maps.topology_base_url') }}</code> ·
                Source: <a href="https://code.highcharts.com/mapdata/" target="_blank" rel="noopener">Highcharts Map Collection</a>.
                When a country hub is configured, map views auto-use that country's TopoJSON unless overridden below.
            </p>
        </div>
    </div>

    <div class="card maps-section-card shadow-sm mb-4">
        <div class="card-header">
            <h4><i class="fa fa-link me-2 text-primary"></i>View assignments</h4>
            <p>Choose which map definition each part of the hub should use. Changes apply after you save.</p>
        </div>
        <div class="card-body p-4">
            <form method="post" action="{{ route('admin.maps.assignments') }}" id="mapAssignmentsForm">
                @csrf
                <div class="assignment-grid">
                    <div class="assignment-field">
                        <label for="default_map_id">Default map</label>
                        <select name="default_map_id" id="default_map_id" class="form-select map-assignment-select">
                            @if($defaultMapId !== '' && empty($definitions[$defaultMapId]))
                                <option value="{{ $defaultMapId }}" selected>Saved: {{ $defaultMapId }} (missing definition)</option>
                            @endif
                            @foreach($definitionGroups ?? ['Maps' => $definitions] as $groupLabel => $groupDefinitions)
                                @if(!empty($groupDefinitions))
                                    <optgroup label="{{ $groupLabel }}">
                                        @foreach($groupDefinitions as $id => $definition)
                                            <option value="{{ $id }}" {{ $defaultMapId === $id ? 'selected' : '' }}>
                                                {{ $definition['label'] ?? $id }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    @foreach($viewContexts as $contextKey => $contextLabel)
                        <div class="assignment-field">
                            <label for="view_map_{{ $contextKey }}">{{ $contextLabel }}</label>
                            <select name="view_map_{{ $contextKey }}" id="view_map_{{ $contextKey }}" class="form-select map-assignment-select">
                                @php $selectedViewMap = $viewAssignments[$contextKey] ?? ''; @endphp
                                <option value="" {{ $selectedViewMap === '' ? 'selected' : '' }}>Use default</option>
                                @if($selectedViewMap !== '' && empty($definitions[$selectedViewMap]))
                                    <option value="{{ $selectedViewMap }}" selected>Saved: {{ $selectedViewMap }} (missing definition)</option>
                                @endif
                                @foreach($definitionGroups ?? ['Maps' => $definitions] as $groupLabel => $groupDefinitions)
                                    @if(!empty($groupDefinitions))
                                        <optgroup label="{{ $groupLabel }}">
                                            @foreach($groupDefinitions as $id => $definition)
                                                <option value="{{ $id }}" {{ $selectedViewMap === $id ? 'selected' : '' }}>
                                                    {{ $definition['label'] ?? $id }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="assignment-actions">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="show_admin_units_map" id="show_admin_units_map" value="1" @if($showAdminUnitsMap) checked @endif>
                        <label class="form-check-label" for="show_admin_units_map">Show admin units map on frontend (country hubs)</label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save me-1"></i>Save assignments
                    </button>
                </div>
            </form>

            @if(!empty($mapSettingsDebug['rawDefault']))
                <div class="mt-3 p-3 bg-light rounded small text-muted border">
                    <strong class="d-block mb-1">Saved in database</strong>
                    Default: <code>{{ $mapSettingsDebug['rawDefault'] }}</code>
                    @if(!empty($mapSettingsDebug['rawViews']))
                        · Views: <code>{{ $mapSettingsDebug['rawViews'] }}</code>
                    @endif
                    @if(!empty($mapSettingsDebug['rowId']))
                        · Setting row #{{ $mapSettingsDebug['rowId'] }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="card maps-section-card shadow-sm mb-4" id="mapDefinitionsCard">
        <div class="card-header">
            <h4><i class="fa fa-list me-2 text-primary"></i>Map definitions</h4>
            <p>All built-in and custom map sources available for assignment.</p>
        </div>
        <div class="maps-definitions-toolbar">
            <div class="maps-definitions-search">
                <i class="fa fa-search search-icon" aria-hidden="true"></i>
                <input type="search"
                       id="mapDefinitionsSearch"
                       class="form-control"
                       placeholder="Search by name, slug, provider, join key, or tag…"
                       autocomplete="off"
                       aria-label="Search map definitions">
                <button type="button" class="search-clear d-none" id="mapDefinitionsSearchClear" aria-label="Clear search">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <span class="maps-definitions-counter" id="mapDefinitionsCounter">
                Showing {{ count($definitions) }} of {{ count($definitions) }}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table maps-table mb-0" id="mapDefinitionsTable">
                <thead>
                    <tr>
                        <th class="map-row-num">#</th>
                        <th style="min-width: 280px;">Map</th>
                        <th>Provider</th>
                        <th>Join key</th>
                        <th>Topology</th>
                        <th class="text-end" style="min-width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="mapDefinitionsBody">
                    @include('admin.maps.partials.definitions_table_rows', [
                        'definitions' => $definitions,
                        'providers' => $providers,
                    ])
                </tbody>
            </table>
        </div>
    </div>

    <div class="card maps-section-card shadow-sm">
        <div class="card-header">
            <h4><i class="fa fa-sitemap me-2 text-primary"></i>Topology presets</h4>
            <p>Reference URLs for Highcharts topology presets included in this hub.</p>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($topologyPresets as $presetKey => $presetPath)
                    <div class="col-md-6 col-xl-4">
                        <div class="topology-preset">
                            <code>{{ $presetKey }}</code>
                            <div class="small text-muted mt-1 text-break">
                                {{ map_build_topology_url(['topology_preset' => $presetKey, 'collection_version' => $topologyVersion, 'country_iso2' => str_contains($presetPath, '{iso2}') ? 'ke' : null]) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<pre id="mapPreviewOutput" class="bg-light border rounded p-3 mt-3 d-none"></pre>
@endsection

@section('scripts')
<script>
(function () {
    var form = document.getElementById('mapAssignmentsForm');
    if (form) {
        form.addEventListener('submit', function () {
            form.querySelectorAll('select.map-assignment-select').forEach(function (select) {
                if (typeof $ !== 'undefined' && $(select).data('select2')) {
                    $(select).select2('destroy');
                }
            });
        });
    }
})();

(function () {
    var previewUrlTemplate = @json(route('admin.maps.preview', ['slug' => '__SLUG__']));
    var searchUrl = @json(route('admin.maps.definitions.search'));
    var searchInput = document.getElementById('mapDefinitionsSearch');
    var searchClear = document.getElementById('mapDefinitionsSearchClear');
    var tableBody = document.getElementById('mapDefinitionsBody');
    var table = document.getElementById('mapDefinitionsTable');
    var counter = document.getElementById('mapDefinitionsCounter');
    var debounceTimer = null;
    var activeController = null;

    function updateCounter(filtered, total) {
        if (!counter) return;
        counter.textContent = filtered === total
            ? 'Showing ' + total + ' of ' + total
            : 'Showing ' + filtered + ' of ' + total;
        counter.classList.toggle('is-filtered', filtered !== total);
    }

    function setLoading(isLoading) {
        if (table) table.classList.toggle('is-loading', isLoading);
    }

    function runSearch() {
        if (!searchInput || !tableBody) return;

        var term = searchInput.value.trim();
        if (searchClear) {
            searchClear.classList.toggle('d-none', term === '');
        }

        if (activeController) {
            activeController.abort();
        }
        activeController = new AbortController();
        setLoading(true);

        var url = searchUrl + (term !== '' ? ('?q=' + encodeURIComponent(term)) : '');
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            signal: activeController.signal
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                tableBody.innerHTML = data.html || '';
                updateCounter(data.filtered || 0, data.total || 0);
            })
            .catch(function (err) {
                if (err && err.name === 'AbortError') return;
            })
            .finally(function () {
                setLoading(false);
            });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(runSearch, 250);
        });
    }

    if (searchClear) {
        searchClear.addEventListener('click', function () {
            searchInput.value = '';
            searchClear.classList.add('d-none');
            runSearch();
            searchInput.focus();
        });
    }

    document.addEventListener('click', function (event) {
        var btn = event.target.closest('.js-map-preview');
        if (!btn) return;

        var slug = btn.getAttribute('data-slug');
        var out = document.getElementById('mapPreviewOutput');
        fetch(previewUrlTemplate.replace('__SLUG__', encodeURIComponent(slug)), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); }).then(function (json) {
            out.classList.remove('d-none');
            out.textContent = JSON.stringify(json, null, 2);
        }).catch(function () {
            out.classList.remove('d-none');
            out.textContent = 'Preview failed.';
        });
    });
})();
</script>
@endsection
