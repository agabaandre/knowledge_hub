@php
    $africaMapConfig = resolved_africa_map_config();
    $africaMapJs = africa_map_settings_for_js();
@endphp
<script>
window.__khAfricaMapSettings = @json($africaMapJs);
window.__khAfricaMapVersionLabel = @json($africaMapConfig['label'] ?? '');
</script>
