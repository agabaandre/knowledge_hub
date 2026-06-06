@php $stats = kpi_admin_stats(); @endphp
<div class="card mb-3">
    <div class="card-header" style="background:#f8fafc;">
        <h3 class="card-title mb-1">Indicator data management</h3>
        <small class="text-muted">Use these actions to fetch, publish, and refresh country indicators from Our World in Data. No technical steps required.</small>
    </div>
    <div class="card-body">
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

        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-cloud-download-alt text-success"></i> Fetch new indicators</h6>
                    <p class="text-muted small mb-2">Search Our World in Data for new charts based on your subject areas. New items are saved as drafts for your review.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/discover') }}" onsubmit="return confirm('Fetch new indicators from Our World in Data? This may take a minute.');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Run fetch</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-sync text-primary"></i> Refresh published values</h6>
                    <p class="text-muted small mb-2">Download the latest country values for all published indicators (matched by ISO country codes).</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/sync') }}" onsubmit="return confirm('Refresh country values for all published indicators?');">
                        @csrf
                        <input type="hidden" name="published_only" value="1">
                        <button type="submit" class="btn btn-primary btn-sm">Refresh values</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-check-circle text-info"></i> Publish recommended set</h6>
                    <p class="text-muted small mb-2">Approve the curated default indicators (life expectancy, child mortality, GDP, etc.) and sync their country data.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/approve-defaults') }}" onsubmit="return confirm('Publish the recommended indicator set? Existing published items will be kept.');">
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
                    <p class="text-muted small mb-2">Fetch new indicators, then refresh values for everything already published. Best for a scheduled monthly update.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/fresh-fetch') }}" onsubmit="return confirm('Run a full refresh from Our World in Data? This fetches new charts and updates published country values.');">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm">Run full refresh</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-comment-dots text-secondary"></i> Regenerate AI summaries</h6>
                    <p class="text-muted small mb-2">Queue new AI narrations for every published indicator and member state. Requires OpenAI settings and a queue worker.</p>
                    <form method="POST" action="{{ url('admin/kpi/owid/generate-narrations') }}" onsubmit="return confirm('Regenerate AI summaries for all published indicators?');">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm">Regenerate summaries</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="border rounded p-3 h-100">
                    <h6 class="mb-1"><i class="fa fa-plus"></i> Manual indicator</h6>
                    <p class="text-muted small mb-2">Add a custom indicator manually instead of importing from Our World in Data.</p>
                    @if($show_create ?? true)
                        <a href="#create-modal" data-toggle="modal" class="btn btn-outline-primary btn-sm">Add indicator</a>
                    @else
                        <a href="{{ url('admin/kpi') }}#create-modal" class="btn btn-outline-primary btn-sm">Go to add indicator</a>
                    @endif
                </div>
            </div>
        </div>

        @include('common.owid_attribution', ['compact' => true])
    </div>
</div>
