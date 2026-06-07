@php
    $mapContext = $mapContext ?? null;
    $africaMapConfig = resolved_map_config($mapContext);
    $africaMapJs = map_settings_for_js($mapContext);
@endphp
@include('partials.maps.map_theme_config')
<script>
window.__khAfricaMapSettings = @json($africaMapJs);
window.__khAfricaMapVersionLabel = @json($africaMapConfig['label'] ?? '');
</script>
