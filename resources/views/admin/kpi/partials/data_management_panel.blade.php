@php
    $stats = kpi_admin_stats();
    $manualOnly = kpi_manual_data_only();
    $autoFetch = kpi_owid_auto_fetch_enabled();
@endphp
<div class="card mb-3">
    <div class="card-header" style="background:#f8fafc;">
        <h3 class="card-title mb-1">Indicator data management</h3>
        <small class="text-muted">Configure how indicators are populated, then run background tasks with live progress.</small>
    </div>
    <div class="card-body">
        @include('admin.kpi.partials.task_progress')

        <form method="POST" action="{{ url('admin/kpi/settings/save') }}" class="border rounded p-3 mb-3">
            @csrf
            <h6 class="mb-2"><i class="fa fa-cog"></i> KPI data source settings</h6>
            <div class="row">
                <div class="col-md-6">
                    <input type="hidden" name="kpi_owid_auto_fetch_enabled" value="0">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="kpi_owid_auto_fetch_enabled" name="kpi_owid_auto_fetch_enabled" value="1" {{ $autoFetch ? 'checked' : '' }}>
                        <label class="form-check-label" for="kpi_owid_auto_fetch_enabled">
                            Enable automatic weekly fetch from Our World in Data
                        </label>
                        <small class="text-muted d-block">When enabled, the system discovers new charts and refreshes published country values every Sunday.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <input type="hidden" name="kpi_manual_data_only" value="0">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="kpi_manual_data_only" name="kpi_manual_data_only" value="1" {{ $manualOnly ? 'checked' : '' }}>
                        <label class="form-check-label" for="kpi_manual_data_only">
                            Manual data entry only (disable Our World in Data import)
                        </label>
                        <small class="text-muted d-block">When enabled, admins add indicators and country values manually. OWID fetch buttons are hidden.</small>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-outline-secondary btn-sm">Save KPI settings</button>
            <small class="text-muted d-block mt-2">Background tasks require a queue worker (<code>php artisan queue:work</code>) on the server. Progress updates every few seconds while a task runs.</small>
        </form>

        @if($manualOnly)
            <div class="alert alert-warning">
                <strong>Manual mode is active.</strong> Use <em>Add indicator</em> and <a href="{{ url('admin/kpi/data') }}">Country values</a> to populate data. Our World in Data import actions are disabled.
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded p-2 text-center h-100">
                    <div class="h4 mb-0">{{ number_format($stats['indicators_published']) }}</div>
                    <small class="text-muted">Published indicators</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded p-2 text-center h-100">
                    <div class="h4 mb-0">{{ number_format($stats['indicators_draft']) }}</div>
                    <small class="text-muted">Draft (awaiting approval)</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded p-2 text-center h-100">
                    <div class="h4 mb-0">{{ number_format($stats['country_values']) }}</div>
                    <small class="text-muted">Country values stored</small>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="border rounded p-2 text-center h-100">
                    <div class="h4 mb-0">{{ number_format($stats['narrations']) }}</div>
                    <small class="text-muted">AI country summaries</small>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap mb-3">
            <a href="{{ url('admin/kpi') }}" class="btn btn-outline-secondary btn-sm mr-2 mb-2 {{ request()->is('admin/kpi') && !request()->is('admin/kpi/*') ? 'active' : '' }}">
                <i class="fa fa-list"></i> Manage indicators
            </a>
            <a href="{{ url('admin/kpi/subject-areas') }}" class="btn btn-outline-secondary btn-sm mr-2 mb-2 {{ request()->is('admin/kpi/subject-areas*') ? 'active' : '' }}">
                <i class="fa fa-folder-open"></i> Subject areas
            </a>
            <a href="{{ url('admin/kpi/data') }}" class="btn btn-outline-secondary btn-sm mr-2 mb-2 {{ request()->is('admin/kpi/data*') ? 'active' : '' }}">
                <i class="fa fa-table"></i> Country values
            </a>
        </div>

        @unless($manualOnly)
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-cloud-download-alt text-success"></i> Fetch new indicators</h6>
                    <p class="text-muted small mb-2">Search Our World in Data for new charts based on your subject areas. Runs in the background queue.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/discover') }}" class="js-kpi-queued-task" data-confirm="Fetch new indicators from Our World in Data?">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Run fetch</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-sync text-primary"></i> Refresh published values</h6>
                    <p class="text-muted small mb-2">Download the latest country values for all published indicators (ISO country codes).</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/sync') }}" class="js-kpi-queued-task" data-confirm="Refresh country values for all published indicators?">
                        @csrf
                        <input type="hidden" name="published_only" value="1">
                        <button type="submit" class="btn btn-primary btn-sm">Refresh values</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-check-circle text-info"></i> Publish recommended set</h6>
                    <p class="text-muted small mb-2">Approve the curated default indicators and sync their country data.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/approve-defaults') }}" class="js-kpi-queued-task" data-confirm="Publish the recommended indicator set?">
                        @csrf
                        <div class="form-check mb-2">
                            <input type="checkbox" class="form-check-input" name="narrations" id="approve_defaults_narrations" value="1" checked>
                            <label class="form-check-label small" for="approve_defaults_narrations">Also queue AI country summaries</label>
                        </div>
                        <button type="submit" class="btn btn-info btn-sm">Publish recommended ({{ $stats['default_set_size'] }})</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-redo text-warning"></i> Full refresh</h6>
                    <p class="text-muted small mb-2">Fetch new indicators, then refresh values for everything already published.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/fresh-fetch') }}" class="js-kpi-queued-task" data-confirm="Run a full refresh from Our World in Data?">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">Run full refresh</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-comment-dots text-secondary"></i> Regenerate AI summaries</h6>
                    <p class="text-muted small mb-2">Queue new AI narrations for every published indicator. Requires OpenAI settings and a queue worker.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/generate-narrations') }}" class="js-kpi-queued-task" data-confirm="Regenerate AI summaries for all published indicators?">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Regenerate summaries</button>
                    </form>
                </div>
            </div>
        </div>
        @endunless

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-plus"></i> Manual indicator</h6>
                    <p class="text-muted small mb-2">Add a custom indicator and enter country values yourself.</p>
                    @if($show_create ?? true)
                        <a href="#create-modal" data-toggle="modal" class="btn btn-outline-primary btn-sm mr-2">Add indicator</a>
                    @else
                        <a href="{{ url('admin/kpi') }}#create-modal" class="btn btn-outline-primary btn-sm mr-2">Go to add indicator</a>
                    @endif
                    <a href="{{ url('admin/kpi/data') }}" class="btn btn-outline-info btn-sm">Enter country values</a>
                </div>
            </div>
        </div>

        @include('common.owid_attribution', ['compact' => true])
    </div>
</div>
