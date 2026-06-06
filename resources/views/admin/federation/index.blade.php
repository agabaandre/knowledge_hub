@extends(admin_layout())

@section('content')
<div class="page-header">
    <h1 class="page-title">{{ __('admin_nav.federated_hubs') }}</h1>
    <div>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('admin') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.configure') }}">{{ __('admin_nav.settings') }}</a></li>
            <li class="breadcrumb-item active">{{ __('admin_nav.federated_hubs') }}</li>
        </ol>
    </div>
</div>

@if(session('alert-success'))
    <div class="alert alert-success">{{ session('alert-success') }}</div>
@endif
@if(session('alert-danger'))
    <div class="alert alert-danger">{{ session('alert-danger') }}</div>
@endif

<div class="alert alert-info">
    <strong>This hub</strong> exposes non-sensitive configuration at
    <code>{{ $federationApiBase }}/manifest</code>.
    Public publications and forums (where <code>public_availability = 1</code>) are listed at
    <code>{{ $federationApiBase }}/public/publications</code> and
    <code>{{ $federationApiBase }}/public/forums</code>.
    @if($federationToken)
        A federation API token is configured; remote hubs must send <code>Authorization: Bearer …</code>.
    @else
        No federation token is set — these endpoints are open. Set one under System Configurations → Hub deployment.
    @endif
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-header"><h3 class="card-title mb-0">Local manifest (preview)</h3></div>
            <div class="card-body">
                <pre class="small bg-light p-3 rounded mb-0" style="max-height: 320px; overflow:auto;">{{ json_encode($localManifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>

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
                        <label class="form-label">API token (optional)</label>
                        <input type="text" name="api_token" class="form-control" placeholder="Bearer token if remote hub requires it">
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
                        <label class="form-check-label" for="new_auto_sync">Include in nightly <code>federation:sync</code> job (02:45)</label>
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
                                                <br><small class="text-muted">{{ $hub->mappedCountry->name }}</small>
                                            @endif
                                        </td>
                                        <td><code class="small">{{ $hub->base_url }}</code></td>
                                        <td>
                                            <span class="badge bg-{{ $hub->connection_status === 'connected' ? 'success' : ($hub->connection_status === 'failed' ? 'danger' : 'secondary') }}">
                                                {{ $hub->connection_status }}
                                            </span>
                                            @if($hub->connection_error)
                                                <br><small class="text-danger">{{ Str::limit($hub->connection_error, 80) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $hub->last_synced_at ? $hub->last_synced_at->diffForHumans() : '—' }}</td>
                                        <td class="text-end text-nowrap">
                                            <form method="post" action="{{ route('admin.federation.connect', $hub) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Connect</button>
                                            </form>
                                            <form method="post" action="{{ route('admin.federation.sync', $hub) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success">Sync public data</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="bg-light">
                                            <form method="post" action="{{ route('admin.federation.update', $hub) }}" class="row g-2 align-items-end">
                                                @csrf
                                                @method('PUT')
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-0">Name</label>
                                                    <input type="text" name="name" class="form-control form-control-sm" value="{{ $hub->name }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small mb-0">Base URL</label>
                                                    <input type="url" name="base_url" class="form-control form-control-sm" value="{{ $hub->base_url }}" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small mb-0">API token</label>
                                                    <input type="text" name="api_token" class="form-control form-control-sm" value="{{ $hub->api_token }}" placeholder="optional">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label small mb-0">Country map</label>
                                                    <select name="mapped_country_id" class="form-control form-control-sm">
                                                        <option value="">—</option>
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" {{ (int) $hub->mapped_country_id === (int) $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active_{{ $hub->id }}" {{ $hub->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="active_{{ $hub->id }}">Active</label>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-secondary mt-1">Save</button>
                                                </div>
                                            </form>
                                            <form method="post" action="{{ route('admin.federation.destroy', $hub) }}" class="d-inline mt-1" onsubmit="return confirm('Remove this hub?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                            </form>
                                            @if($hub->last_manifest)
                                                <details class="mt-2">
                                                    <summary class="small text-muted">Remote manifest</summary>
                                                    <pre class="small bg-white border rounded p-2 mt-1 mb-0" style="max-height: 200px; overflow:auto;">{{ json_encode($hub->last_manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                                </details>
                                            @endif
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
@endsection
