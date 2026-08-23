@extends(admin_layout())

@section('styles')
<style>
    .fed-shell { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(15,23,42,.08); overflow: hidden; margin-bottom: 1.5rem; }
    .fed-hero { background: linear-gradient(135deg, var(--theme-color-primary, #119A48) 0%, #0d7a38 100%); color: #fff; padding: 1.5rem 1.75rem; }
    .fed-hero h1 { font-size: 1.45rem; margin: 0 0 .35rem; color: #fff; }
    .fed-hero p { margin: 0; opacity: .92; font-size: .92rem; }
    .fed-tabs { display: flex; flex-wrap: wrap; gap: .25rem; padding: .75rem 1.25rem; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; }
    .fed-tab { border: none; background: transparent; color: #64748b; font-weight: 500; font-size: .88rem; padding: .65rem 1rem; border-bottom: 3px solid transparent; }
    .fed-tab.active { color: var(--theme-color-primary, #119A48); background: #fff; border-bottom-color: var(--theme-color-primary, #119A48); }
    .fed-section { display: none; padding: 1.5rem 1.75rem; }
    .fed-section.active { display: block; }
    .fed-endpoint-table code { font-size: .78rem; word-break: break-all; }
    .fed-kpi { border: 1px solid #e2e8f0; border-radius: 10px; padding: .85rem 1rem; background: #f8fafc; height: 100%; }
    .fed-kpi-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #64748b; margin-bottom: .25rem; }
</style>
@endsection

@section('content')
<div class="page-header mb-3">
    <div>
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin_nav.federated_hubs') }}</li>
        </ol>
    </div>
</div>

@if(session('alert-success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('alert-success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('alert-danger'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('alert-danger') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(($pendingFederatedContentCount ?? 0) > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span><strong>{{ $pendingFederatedContentCount }}</strong> federated publication(s)/forum(s) are waiting for central approval.</span>
        <a href="{{ route('admin.federation.pending-content') }}" class="btn btn-sm btn-warning">Review now</a>
    </div>
@endif

<div class="fed-shell">
    <div class="fed-hero">
        <h1>{{ __('admin_nav.federated_hubs') }}</h1>
        <p>Exchange publications, forums, branding, and lookup metadata between continental and country Knowledge Hub instances.</p>
    </div>

    <div class="fed-tabs">
        <button type="button" class="fed-tab active" data-fed-tab="provider">Provider API</button>
        @if($isCountryHub)
            <button type="button" class="fed-tab" data-fed-tab="central">Central hub connection</button>
        @endif
        <button type="button" class="fed-tab" data-fed-tab="remote">Remote hubs</button>
        @if(!empty($provisionEnabled))
            <button type="button" class="fed-tab" data-fed-tab="provision">Provision country hub</button>
        @endif
        @if(function_exists('federation_consumer_enabled') && federation_consumer_enabled())
            <a href="{{ route('admin.federation.pending-content') }}" class="fed-tab text-decoration-none {{ request()->routeIs('admin.federation.pending-content') ? 'active' : '' }}">
                Content review
                @if(($pendingFederatedContentCount ?? 0) > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingFederatedContentCount }}</span>
                @endif
            </a>
        @endif
    </div>

    <section class="fed-section active" id="fed-section-provider">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">This hub exposes</div>
                    <div class="fw-semibold">{{ count($lookupIndex['endpoints'] ?? []) }} federation endpoints</div>
                    <div class="small text-muted mt-1">Base: <code>{{ $federationApiBase }}</code></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">API security</div>
                    <div class="fw-semibold">{{ $federationToken ? 'Token required' : 'Open (no token)' }}</div>
                    <div class="small text-muted mt-1">
                        Generate and copy the token under
                        <a href="{{ route('admin.configure') }}#advanced" class="text-decoration-underline">System Configurations → Advanced</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">Hub type</div>
                    <div class="fw-semibold">{{ $localManifest['hub_type'] ?? '—' }}</div>
                    <div class="small text-muted mt-1">Site ID <code>{{ $localManifest['site_id'] ?? '—' }}</code></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title mb-0">Federation &amp; lookup endpoints</h3></div>
            <div class="card-body p-0">
                <div class="table-responsive fed-endpoint-table">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Endpoint</th><th>Purpose</th></tr></thead>
                        <tbody>
                            <tr><td><code>POST {{ $federationApiBase }}/auth/token</code></td><td>Issue or refresh OAuth access tokens for child hubs</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/lookup</code></td><td>Index of all federation URLs</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/manifest</code></td><td>Hub identity, type, owner country/region</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/lookup/settings</code></td><td>Branding &amp; mobile settings (colors, theme, feature flags, logo URLs)</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/lookup/metadata</code></td><td>Publication/forum lookup tables (themes, tags, categories, licenses, key links)</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/public/publications</code></td><td>Public approved publications (paginated)</td></tr>
                            <tr><td><code>GET {{ $federationApiBase }}/public/forums</code></td><td>Public approved forums (paginated)</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title mb-0">Local manifest preview</h3></div>
            <div class="card-body">
                <pre class="small bg-light p-3 rounded mb-0" style="max-height: 280px; overflow:auto;">{{ json_encode($localManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </section>

    @if($isCountryHub)
    <section class="fed-section" id="fed-section-central">
        <p class="text-muted small">Pull branding (for web &amp; mobile <code>/api/lookup/settings</code>) and taxonomy tables from the continental central hub. New country hubs can also do this during installation.</p>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">Connected central hub</div>
                    <div class="fw-semibold text-truncate" title="{{ $centralHubUrl }}">{{ $centralHubUrl ?: 'Not configured' }}</div>
                    @if($centralHubSiteId)
                        <div class="small text-muted">Remote site ID <code>{{ $centralHubSiteId }}</code></div>
                    @endif
                </div>
            </div>
            <div class="col-md-3">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">Last connected</div>
                    <div class="fw-semibold">{{ $centralHubConnectedAt ? \Illuminate\Support\Carbon::parse($centralHubConnectedAt)->diffForHumans() : '—' }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">Metadata synced</div>
                    <div class="fw-semibold">{{ $centralMetadataSyncedAt ? \Illuminate\Support\Carbon::parse($centralMetadataSyncedAt)->diffForHumans() : '—' }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="fed-kpi">
                    <div class="fed-kpi-label">Access token</div>
                    <div class="fw-semibold">
                        @if($centralHubTokenExpiresAt)
                            Expires {{ \Illuminate\Support\Carbon::parse($centralHubTokenExpiresAt)->diffForHumans() }}
                        @elseif($centralHubHasRefreshToken)
                            OAuth connected
                        @else
                            Static / not set
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title mb-0">Sync from central hub</h3></div>
            <div class="card-body">
                <form method="post" action="{{ route('admin.federation.sync-central') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Central hub URL</label>
                        <input type="url" name="central_hub_url" id="adminCentralUrl" class="form-control" required
                               value="{{ old('central_hub_url', $centralHubUrl ?: 'https://khub.africacdc.org') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Registration token</label>
                        <input type="text" name="central_hub_api_token" id="adminCentralToken" class="form-control"
                               value="{{ old('central_hub_api_token', '') }}" placeholder="Parent hub federation API token">
                        <div class="form-text">Exchanged once for refreshable OAuth tokens. Leave blank if already connected.</div>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="import_branding" value="1" id="adminImportBranding" checked>
                            <label class="form-check-label" for="adminImportBranding">Import branding &amp; theme settings</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="import_metadata" value="1" id="adminImportMetadata" checked>
                            <label class="form-check-label" for="adminImportMetadata">Import lookup metadata tables</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex flex-wrap gap-2 align-items-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="adminTestCentralBtn">Test connection</button>
                        <button type="submit" class="btn btn-primary btn-sm">Sync now</button>
                        @if($centralHubHasRefreshToken)
                            <form method="post" action="{{ route('admin.federation.refresh-central') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">Refresh token</button>
                            </form>
                        @endif
                        <span id="adminTestCentralResult" class="small text-muted"></span>
                    </div>
                </form>
            </div>
        </div>
    </section>
    @endif

    @if(!empty($provisionEnabled))
    <section class="fed-section" id="fed-section-provision">
        <div class="alert alert-info">
            Creates a country Knowledge Hub on this server at
            <code>{{ $provisionPublicBaseUrl }}/{slug}</code>
            (file copy, dedicated MySQL database, Apache Alias, verified admin account).
            Users and publications are <strong>not</strong> copied from continental.
            Defaults: <code>STATES_ENABLED=false</code>, <code>ADMIN_UNITS_ENABLED=true</code>,
            default owner country = selected country.
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title mb-0">Provision country hub</h3></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.federation.provision') }}" id="fedProvisionForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Country</label>
                                <select name="country_id" id="fedProvisionCountry" class="form-control select2" required>
                                    <option value="">— Select country —</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}"
                                            data-slug="{{ $country->slug ?: \Illuminate\Support\Str::slug($country->name) }}"
                                            data-name="{{ $country->name }}"
                                            @selected(old('country_id') == $country->id)>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">URL slug</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ $provisionPublicBaseUrl }}/</span>
                                    <input type="text" name="slug" id="fedProvisionSlug" class="form-control" required
                                           pattern="[a-z0-9\-]+" maxlength="64"
                                           value="{{ old('slug') }}" placeholder="uganda">
                                </div>
                                <div class="form-text">Lowercase letters, numbers, hyphens only.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Site name (optional)</label>
                                <input type="text" name="site_name" id="fedProvisionSiteName" class="form-control"
                                       value="{{ old('site_name') }}" placeholder="Uganda Knowledge Hub">
                            </div>
                            <hr>
                            <p class="small text-muted mb-2">Country admin account (not copied from continental)</p>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First name</label>
                                    <input type="text" name="admin_first_name" class="form-control" required value="{{ old('admin_first_name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last name</label>
                                    <input type="text" name="admin_last_name" class="form-control" required value="{{ old('admin_last_name') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin email</label>
                                <input type="email" name="admin_email" class="form-control" required value="{{ old('admin_email') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Admin password</label>
                                <input type="password" name="admin_password" class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm password</label>
                                <input type="password" name="admin_password_confirmation" class="form-control" required minlength="8" autocomplete="new-password">
                            </div>
                            <ul class="small text-muted mb-3">
                                <li>Copies branding + lookup metadata from continental</li>
                                <li>Sets administrative units (country hub mode) and default owner country</li>
                                <li>Requires queue worker and provision credentials in continental <code>.env</code></li>
                            </ul>
                            <button type="submit" class="btn btn-primary">Start provisioning</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title mb-0">Recent provisions</h3></div>
                    <div class="card-body p-0">
                        @if(($provisions ?? collect())->isEmpty())
                            <p class="p-3 mb-0 text-muted">No provision jobs yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm mb-0" id="fedProvisionTable">
                                    <thead>
                                        <tr>
                                            <th>Slug</th>
                                            <th>Country</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Message</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($provisions as $p)
                                            <tr data-provision-id="{{ $p->id }}" data-terminal="{{ $p->isTerminal() ? '1' : '0' }}">
                                                <td>
                                                    <code>{{ $p->slug }}</code>
                                                    @if($p->status === 'completed')
                                                        <br><a href="{{ $p->base_url }}" target="_blank" rel="noopener" class="small">Open</a>
                                                    @endif
                                                </td>
                                                <td>{{ $p->country->name ?? '—' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $p->status === 'completed' ? 'success' : ($p->status === 'failed' ? 'danger' : 'secondary') }}">
                                                        {{ $p->status }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height:8px;min-width:80px;">
                                                        <div class="progress-bar" style="width:{{ $p->progress_percent }}%"></div>
                                                    </div>
                                                    <small class="text-muted">{{ $p->progress_percent }}% · {{ $p->current_step }}</small>
                                                </td>
                                                <td class="small">
                                                    {{ $p->error_message ?: $p->message }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="fed-section" id="fed-section-remote">
        @if(! $isCountryHub)
            <p class="text-muted small mb-3">As a continental hub, register country instances here to browse and sync their public publications and forums.</p>
        @else
            <p class="text-muted small mb-3">Optional: register peer hubs for cross-hub browsing (continental hubs register country hubs here).</p>
        @endif

        <div class="row">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title mb-0">Add remote Knowledge Hub</h3></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.federation.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Display name</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Kenya Knowledge Hub">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Base URL</label>
                                <input type="url" name="base_url" class="form-control" required placeholder="https://kenya.example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Registration token (optional)</label>
                                <input type="text" name="api_token" class="form-control" placeholder="Parent federation token — exchanged for refreshable OAuth tokens">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Map to country (optional)</label>
                                <select name="mapped_country_id" class="form-control select2">
                                    <option value="">— None —</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="new_is_active" checked>
                                <label class="form-check-label" for="new_is_active">Active</label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="auto_sync" value="1" id="new_auto_sync">
                                <label class="form-check-label" for="new_auto_sync">Include in nightly <code>federation:sync</code> (02:45)</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Add hub</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header"><h3 class="card-title mb-0">Registered hubs</h3></div>
                    <div class="card-body p-0">
                        @if($hubs->isEmpty())
                            <p class="p-3 mb-0 text-muted">No remote hubs registered yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>URL</th>
                                            <th>Status</th>
                                            <th>Last sync</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hubs as $hub)
                                            <tr>
                                                <td>
                                                    <strong>{{ $hub->name }}</strong>
                                                    @if($hub->mappedCountry)
                                                        @php $hubMap = federated_hub_map_settings_for_js($hub); @endphp
                                                        <br><small class="text-muted">{{ $hub->mappedCountry->name }}
                                                            @if(!empty($hubMap['topologyUrl']))
                                                                · <a href="{{ $hubMap['topologyUrl'] }}" target="_blank" rel="noopener">country map</a>
                                                            @endif
                                                        </small>
                                                    @endif
                                                </td>
                                                <td><code class="small">{{ $hub->base_url }}</code></td>
                                                <td>
                                                    <span class="badge bg-{{ $hub->connection_status === 'connected' ? 'success' : ($hub->connection_status === 'failed' ? 'danger' : 'secondary') }}">
                                                        {{ $hub->connection_status }}
                                                    </span>
                                                </td>
                                                <td>{{ $hub->last_synced_at ? $hub->last_synced_at->diffForHumans() : '—' }}</td>
                                                <td class="text-end text-nowrap">
                                                    <form method="post" action="{{ route('admin.federation.connect', $hub) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-primary">Connect</button></form>
                                                    <form method="post" action="{{ route('admin.federation.sync', $hub) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-success">Sync</button></form>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="bg-light">
                                                    <form method="post" action="{{ route('admin.federation.update', $hub) }}" class="row g-2 align-items-end">
                                                        @csrf @method('PUT')
                                                        <div class="col-md-3"><label class="form-label small mb-0">Name</label><input type="text" name="name" class="form-control form-control-sm" value="{{ $hub->name }}" required></div>
                                                        <div class="col-md-3"><label class="form-label small mb-0">Base URL</label><input type="url" name="base_url" class="form-control form-control-sm" value="{{ $hub->base_url }}" required></div>
                                                        <div class="col-md-2"><label class="form-label small mb-0">Token</label><input type="text" name="api_token" class="form-control form-control-sm" value="{{ $hub->api_token }}"></div>
                                                        <div class="col-md-2"><label class="form-label small mb-0">Country</label>
                                                            <select name="mapped_country_id" class="form-control form-control-sm">
                                                                <option value="">—</option>
                                                                @foreach($countries as $country)
                                                                    <option value="{{ $country->id }}" {{ (int) $hub->mapped_country_id === (int) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-2"><button type="submit" class="btn btn-sm btn-secondary">Save</button></div>
                                                    </form>
                                                    <form method="post" action="{{ route('admin.federation.destroy', $hub) }}" class="d-inline mt-1" onsubmit="return confirm('Remove this hub?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Remove</button></form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
(function () {
    function activateFedTab(tab) {
        if (!tab) return;
        document.querySelectorAll('.fed-tab').forEach(function (b) {
            b.classList.toggle('active', b.getAttribute('data-fed-tab') === tab);
        });
        document.querySelectorAll('.fed-section').forEach(function (s) {
            s.classList.toggle('active', s.id === 'fed-section-' + tab);
        });
    }

    document.querySelectorAll('.fed-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var tab = btn.getAttribute('data-fed-tab');
            if (!tab) return;
            activateFedTab(tab);
        });
    });

    var params = new URLSearchParams(window.location.search);
    if (params.get('fed_tab')) {
        activateFedTab(params.get('fed_tab'));
    }

    var testBtn = document.getElementById('adminTestCentralBtn');
    if (testBtn) {
        testBtn.addEventListener('click', function () {
            var result = document.getElementById('adminTestCentralResult');
            result.textContent = 'Testing…';
            var body = new FormData();
            body.append('_token', '{{ csrf_token() }}');
            body.append('central_hub_url', document.getElementById('adminCentralUrl').value);
            body.append('central_hub_api_token', document.getElementById('adminCentralToken').value);
            fetch('{{ route('admin.federation.test-central') }}', { method: 'POST', body: body })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.ok) {
                        result.textContent = 'Connected to ' + ((data.manifest && (data.manifest.site_name || data.manifest.title)) || 'central hub');
                        result.className = 'small text-success';
                    } else {
                        result.textContent = data.error || 'Failed';
                        result.className = 'small text-danger';
                    }
                });
        });
    }

    var countrySelect = document.getElementById('fedProvisionCountry');
    var slugInput = document.getElementById('fedProvisionSlug');
    var siteNameInput = document.getElementById('fedProvisionSiteName');
    if (countrySelect && slugInput) {
        countrySelect.addEventListener('change', function () {
            var opt = countrySelect.options[countrySelect.selectedIndex];
            if (!opt || !opt.value) return;
            var slug = opt.getAttribute('data-slug') || '';
            var name = opt.getAttribute('data-name') || '';
            if (slug && (!slugInput.value || slugInput.dataset.autofilled === '1')) {
                slugInput.value = slug;
                slugInput.dataset.autofilled = '1';
            }
            if (siteNameInput && name && (!siteNameInput.value || siteNameInput.dataset.autofilled === '1')) {
                siteNameInput.value = name + ' Knowledge Hub';
                siteNameInput.dataset.autofilled = '1';
            }
        });
        slugInput.addEventListener('input', function () {
            slugInput.dataset.autofilled = '0';
        });
        if (siteNameInput) {
            siteNameInput.addEventListener('input', function () {
                siteNameInput.dataset.autofilled = '0';
            });
        }
    }

    function pollProvisions() {
        var rows = document.querySelectorAll('#fedProvisionTable tr[data-provision-id][data-terminal="0"]');
        if (!rows.length) return;
        rows.forEach(function (row) {
            var id = row.getAttribute('data-provision-id');
            fetch('{{ url('/admin/federated-hubs/provision') }}/' + id, {
                headers: { 'Accept': 'application/json' }
            }).then(function (r) { return r.json(); }).then(function (data) {
                if (!data || !data.id) return;
                var badge = row.querySelector('.badge');
                if (badge) {
                    badge.textContent = data.status;
                    badge.className = 'badge bg-' + (data.status === 'completed' ? 'success' : (data.status === 'failed' ? 'danger' : 'secondary'));
                }
                var bar = row.querySelector('.progress-bar');
                if (bar) bar.style.width = (data.progress_percent || 0) + '%';
                var small = row.querySelector('small.text-muted');
                if (small) small.textContent = (data.progress_percent || 0) + '% · ' + (data.current_step || '');
                var msg = row.querySelector('td.small');
                if (msg) msg.textContent = data.error_message || data.message || '';
                if (data.status === 'completed' || data.status === 'failed') {
                    row.setAttribute('data-terminal', '1');
                    if (data.status === 'completed') {
                        setTimeout(function () { window.location.reload(); }, 1200);
                    }
                }
            }).catch(function () {});
        });
    }
    if (document.getElementById('fedProvisionTable')) {
        setInterval(pollProvisions, 4000);
    }
})();
</script>
@endsection
