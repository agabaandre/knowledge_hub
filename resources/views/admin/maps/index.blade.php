@extends(admin_layout())

@section('content')
<div class="container-fluid py-3">
    @if(session('message'))
        <div class="alert alert-{{ session('status') === 'success' ? 'success' : 'warning' }}">{{ session('message') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="mb-0"><i class="fa fa-map me-2"></i>Maps Management</h3>
                <p class="text-muted small mb-0">Manage ISO-keyed choropleth maps using Highcharts Map Collection TopoJSON (also usable with FusionCharts and other ISO providers).</p>
            </div>
            <a href="{{ route('admin.maps.create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus me-1"></i>Add map definition</a>
        </div>
        <div class="card-body">
            <p class="text-muted small">Topology collection version: <code>{{ $topologyVersion }}</code> · Base URL pattern: <code>{{ config('maps.topology_base_url') }}</code></p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>View assignments</strong></div>
                <div class="card-body">
                    <form method="post" action="{{ route('admin.maps.assignments') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Default map</label>
                            <select name="default_map_id" class="form-control">
                                @foreach($definitions as $id => $definition)
                                    <option value="{{ $id }}" {{ $defaultMapId === $id ? 'selected' : '' }}>
                                        {{ $definition['label'] ?? $id }}
                                        @if(!empty($definition['provider'])) ({{ $providers[$definition['provider']]['label'] ?? $definition['provider'] }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @foreach($viewContexts as $contextKey => $contextLabel)
                            <div class="mb-3">
                                <label class="form-label">{{ $contextLabel }}</label>
                                <select name="view_map_{{ $contextKey }}" class="form-control">
                                    <option value="">Use default</option>
                                    @foreach($definitions as $id => $definition)
                                        <option value="{{ $id }}" {{ ($viewAssignments[$contextKey] ?? '') === $id ? 'selected' : '' }}>
                                            {{ $definition['label'] ?? $id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="show_admin_units_map" id="show_admin_units_map" value="1" @if($showAdminUnitsMap) checked @endif>
                            <label class="form-check-label" for="show_admin_units_map">Show admin units map on frontend (country hubs)</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Save assignments</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header"><strong>Map definitions</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Provider</th>
                                <th>Join</th>
                                <th>Topology</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($definitions as $id => $definition)
                                @php $resolved = resolved_map_config(null, $id); @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $definition['label'] ?? $id }}</strong>
                                        <div class="small text-muted"><code>{{ $id }}</code>
                                            @if(!empty($definition['builtin'])) <span class="badge bg-secondary">built-in</span> @endif
                                            @if(!empty($definition['managed'])) <span class="badge bg-info">managed</span> @endif
                                        </div>
                                    </td>
                                    <td>{{ $providers[$definition['provider'] ?? 'highcharts']['label'] ?? ($definition['provider'] ?? 'highcharts') }}</td>
                                    <td><code>{{ $definition['join_by'] ?? 'iso-a3' }}</code></td>
                                    <td class="small">
                                        @if(!empty($resolved['topology_url']))
                                            <a href="{{ $resolved['topology_url'] }}" target="_blank" rel="noopener">TopoJSON</a>
                                        @elseif(!empty($definition['script']))
                                            Script map
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-outline-secondary btn-sm js-map-preview" data-slug="{{ $id }}">Preview</button>
                                        @if(!empty($definition['managed_id']))
                                            <a href="{{ route('admin.maps.edit', $definition['managed_id']) }}" class="btn btn-outline-primary btn-sm">Edit</a>
                                            <form method="post" action="{{ route('admin.maps.destroy', $definition['managed_id']) }}" class="d-inline" onsubmit="return confirm('Delete this map definition?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header"><strong>Topology presets</strong></div>
        <div class="card-body">
            <div class="row">
                @foreach($topologyPresets as $presetKey => $presetPath)
                    <div class="col-md-6 col-lg-4 mb-2">
                        <code>{{ $presetKey }}</code>
                        <div class="small text-muted">{{ map_build_topology_url(['topology_preset' => $presetKey, 'collection_version' => $topologyVersion, 'country_iso2' => str_contains($presetPath, '{iso2}') ? 'ke' : null]) }}</div>
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
document.querySelectorAll('.js-map-preview').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var slug = this.getAttribute('data-slug');
        var out = document.getElementById('mapPreviewOutput');
        fetch(@json(route('admin.maps.preview', ['slug' => '__SLUG__'])).replace('__SLUG__', encodeURIComponent(slug)), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); }).then(function (json) {
            out.classList.remove('d-none');
            out.textContent = JSON.stringify(json, null, 2);
        }).catch(function () {
            out.classList.remove('d-none');
            out.textContent = 'Preview failed.';
        });
    });
});
</script>
@endsection
