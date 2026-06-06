{{-- Shared Highcharts credits/subtitle for OWID-sourced KPI graphs --}}
credits: {
    enabled: true,
    text: 'Data: Our World in Data (CC BY 4.0)',
    href: '{{ owid_site_url() }}/',
    style: { fontSize: '11px', color: '#64748b' }
},
subtitle: {
    useHTML: true,
    text: 'Source: <a href="{{ owid_site_url() }}/" target="_blank" rel="noopener">Our World in Data</a> (<a href="{{ owid_license_url() }}" target="_blank" rel="noopener">CC BY 4.0</a>)'
},
