@extends(admin_layout())

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ $map ? 'Edit map definition' : 'Add map definition' }}</h3>
            <a href="{{ route('admin.maps.index') }}" class="btn btn-outline-secondary btn-sm">Back to maps</a>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('admin.maps.store') }}">
                @csrf
                @if($map)
                    <input type="hidden" name="id" value="{{ $map->id }}">
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Label</label>
                            <input type="text" name="label" class="form-control" required value="{{ old('label', $map->label ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug', $map->slug ?? '') }}" placeholder="auto-generated if blank">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $map->description ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Provider / renderer</label>
                            <select name="provider" class="form-control">
                                @foreach($providers as $key => $provider)
                                    <option value="{{ $key }}" {{ old('provider', $map->provider ?? 'highcharts') === $key ? 'selected' : '' }}>{{ $provider['label'] ?? $key }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Source type</label>
                            <select name="source_type" class="form-control" id="mapSourceType">
                                <option value="topojson_url" {{ old('source_type', $map->source_type ?? 'topojson_url') === 'topojson_url' ? 'selected' : '' }}>TopoJSON URL (Highcharts Map Collection)</option>
                                <option value="geojson_script" {{ old('source_type', $map->source_type ?? '') === 'geojson_script' ? 'selected' : '' }}>GeoJSON script (Highcharts only)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Collection version</label>
                            <input type="text" name="collection_version" class="form-control" value="{{ old('collection_version', $map->collection_version ?? $topologyVersion) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Topology preset</label>
                            <select name="topology_preset" class="form-control">
                                <option value="">Custom URL below</option>
                                @foreach($topologyPresets as $presetKey => $presetPath)
                                    <option value="{{ $presetKey }}" {{ old('topology_preset', $map->topology_preset ?? '') === $presetKey ? 'selected' : '' }}>{{ $presetKey }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Examples: <code>world-highres</code>, <code>africa-sadr</code>, <code>country-admin1</code></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Custom topology URL</label>
                            <input type="url" name="topology_url" class="form-control" value="{{ old('topology_url', $map->topology_url ?? '') }}" placeholder="https://code.highcharts.com/mapdata/2.3.3/custom/world-highres.topo.json">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">ISO join property</label>
                            <select name="join_by" class="form-control">
                                @foreach($joinOptions as $joinKey => $joinLabel)
                                    <option value="{{ $joinKey }}" {{ old('join_by', $map->join_by ?? 'iso-a3') === $joinKey ? 'selected' : '' }}>{{ $joinLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">TopoJSON ISO property override</label>
                            <input type="text" name="iso_property" class="form-control" value="{{ old('iso_property', $map->iso_property ?? '') }}" placeholder="Defaults to join property">
                            <small class="text-muted">Useful for FusionCharts (<code>id</code> often maps to ISO alpha-3).</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Scope</label>
                            <input type="text" name="scope" class="form-control" value="{{ old('scope', $map->scope ?? 'custom') }}" placeholder="africa, world, country, custom">
                        </div>
                    </div>

                    <div class="col-md-4 js-script-fields">
                        <div class="mb-3">
                            <label class="form-label">Highcharts map key</label>
                            <input type="text" name="map_key" class="form-control" value="{{ old('map_key', $map->map_key ?? '') }}" placeholder="custom/africa">
                        </div>
                    </div>
                    <div class="col-md-8 js-script-fields">
                        <div class="mb-3">
                            <label class="form-label">Script path</label>
                            <input type="text" name="script_path" class="form-control" value="{{ old('script_path', $map->script_path ?? '') }}" placeholder="assets/js/maps/africa.js">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Country ISO2 (for admin1 maps)</label>
                            <input type="text" maxlength="2" name="country_iso2" class="form-control text-uppercase" value="{{ old('country_iso2', $map->country_iso2 ?? '') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Sort order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $map->sort_order ?? 0) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @if(old('is_active', $map->is_active ?? true)) checked @endif>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Save map definition</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var sourceType = document.getElementById('mapSourceType');
    function toggleScriptFields() {
        var show = sourceType && sourceType.value === 'geojson_script';
        document.querySelectorAll('.js-script-fields').forEach(function (el) {
            el.style.display = show ? '' : 'none';
        });
    }
    if (sourceType) {
        sourceType.addEventListener('change', toggleScriptFields);
        toggleScriptFields();
    }
})();
</script>
@endsection
