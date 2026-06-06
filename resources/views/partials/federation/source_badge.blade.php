@php
    $hubName = $hubName ?? ($item->federation_hub_name ?? 'Partner hub');
@endphp
<span class="badge rounded-pill federation-source-badge" style="background: {{ settings()->au_gold ?? '#B4A269' }}; color: #3d3420; font-size: 0.72rem; font-weight: 600;">
    <i class="fa fa-globe-africa me-1"></i>Source: {{ $hubName }}
</span>
