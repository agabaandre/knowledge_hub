@forelse($definitions as $id => $definition)
    @php $resolved = resolved_map_config(null, $id); @endphp
    <tr>
        <td class="text-muted small fw-semibold map-row-num">{{ $loop->iteration }}</td>
        <td class="map-label-cell">
            <strong>{{ $definition['label'] ?? $id }}</strong>
            <div class="map-slug">{{ $id }}</div>
            <div class="map-badge-row">
                @if(!empty($definition['builtin']))
                    <span class="map-badge map-badge--builtin">Built-in</span>
                @endif
                @if(($definition['scope'] ?? '') === 'country')
                    <span class="map-badge map-badge--country">Country</span>
                @endif
                @if(!empty($definition['managed']))
                    <span class="map-badge map-badge--managed">Managed</span>
                @endif
            </div>
        </td>
        <td>{{ $providers[$definition['provider'] ?? 'highcharts']['label'] ?? ($definition['provider'] ?? 'highcharts') }}</td>
        <td><span class="map-join-code">{{ $definition['join_by'] ?? 'iso-a3' }}</span></td>
        <td class="small">
            @if(!empty($resolved['topology_url']))
                <a href="{{ $resolved['topology_url'] }}" target="_blank" rel="noopener" class="text-decoration-none">
                    <i class="fa fa-external-link me-1"></i>TopoJSON
                </a>
            @elseif(!empty($definition['script']))
                <span class="text-muted">Script map</span>
            @else
                <span class="text-muted">—</span>
            @endif
        </td>
        <td class="text-end">
            <div class="d-inline-flex flex-wrap gap-1 justify-content-end">
                <button type="button" class="btn btn-outline-secondary btn-sm js-map-preview" data-slug="{{ $id }}">
                    <i class="fa fa-eye me-1"></i>Preview
                </button>
                @if(!empty($definition['managed_id']))
                    <a href="{{ route('admin.maps.edit', $definition['managed_id']) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-pencil me-1"></i>Edit
                    </a>
                    <form method="post" action="{{ route('admin.maps.destroy', $definition['managed_id']) }}" class="d-inline" onsubmit="return confirm('Delete this map definition?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr class="map-definitions-empty">
        <td colspan="6" class="text-center text-muted py-5">
            <i class="fa fa-search fa-2x mb-2 d-block opacity-50"></i>
            No map definitions match your search.
        </td>
    </tr>
@endforelse
