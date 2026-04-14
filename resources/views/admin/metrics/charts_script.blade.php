{{-- Defines window.renderMetricsCharts(chartData) for dashboard AJAX. Requires Highcharts. --}}
<script>
(function() {
    var auColors = {
        red: '{{ settings()->au_red ?? "#9F2241" }}',
        gold: '{{ settings()->au_gold ?? "#B4A269" }}',
        corporateGreen: '{{ settings()->au_corporate_green ?? "#1A5632" }}',
        green: '{{ settings()->au_green ?? "#1A5632" }}',
        plum: '{{ settings()->au_plum ?? "#522B39" }}',
        greyText: '{{ settings()->au_grey_text ?? "#58595B" }}',
        white: '{{ settings()->au_white ?? "#FFFFFF" }}'
    };
    function lightenColor(color, amount) {
        if (color.startsWith('#')) {
            var num = parseInt(color.replace('#', ''), 16);
            var r = Math.min(255, (num >> 16) + Math.round(amount * 255));
            var g = Math.min(255, ((num >> 8) & 0x00FF) + Math.round(amount * 255));
            var b = Math.min(255, (num & 0x0000FF) + Math.round(amount * 255));
            return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
        }
        return color;
    }
    var africaCDCColors = [
        auColors.corporateGreen, auColors.red, auColors.gold, auColors.green, auColors.plum, auColors.greyText,
        lightenColor(auColors.corporateGreen, 0.3), lightenColor(auColors.red, 0.3), lightenColor(auColors.gold, 0.3), lightenColor(auColors.green, 0.3)
    ];

    window.renderMetricsCharts = function(chartData) {
        if (!chartData || typeof Highcharts === 'undefined') return;
        var jsonData = chartData;
        var container = document.getElementById('chart-container');
        if (!container) return;
        container.innerHTML = '';

        function renderChart(key) {
            var data = jsonData[key];
            if (!data || !data.labels || !data.values) return;
            var chartType = data.chartType;
            var labels = data.labels;
            var values = data.values;
            var title = key.replace(/_/g, ' ').toUpperCase();
            var col = document.createElement('div');
            col.className = 'col-xl-6 col-lg-6 col-md-12 mb-4';
            col.style.cssText = 'padding-left: 15px; padding-right: 15px;';
            var card = document.createElement('div');
            card.className = 'card h-100';
            card.style.cssText = 'border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 0;';
            var header = document.createElement('div');
            header.className = 'card-header';
            header.style.cssText = 'background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;';
            header.innerHTML = '<h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">' + title + '</h3>';
            var body = document.createElement('div');
            body.className = 'card-body';
            body.style.cssText = 'padding: 1.5rem;';
            var chartContainer = document.createElement('div');
            chartContainer.id = key + '-chart';
            chartContainer.style.cssText = 'height:360px;';
            body.appendChild(chartContainer);
            card.appendChild(header);
            card.appendChild(body);
            col.appendChild(card);
            container.appendChild(col);
            var categories = (chartType === 'bar' || chartType === 'line') ? labels : null;
            Highcharts.chart(chartContainer.id, {
                chart: { type: chartType, backgroundColor: 'transparent' },
                colors: africaCDCColors,
                title: { text: null },
                credits: { enabled: false },
                xAxis: {
                    categories: categories,
                    lineColor: '#e2e8f0',
                    tickColor: '#e2e8f0',
                    labels: { style: { color: auColors.greyText } }
                },
                yAxis: {
                    title: { text: null },
                    gridLineColor: '#e2e8f0',
                    lineColor: '#e2e8f0',
                    labels: { style: { color: auColors.greyText } }
                },
                legend: { enabled: chartType !== 'pie', itemStyle: { color: auColors.greyText } },
                plotOptions: {
                    bar: { colorByPoint: true, dataLabels: { style: { color: '#0f172a', fontWeight: '600' } } },
                    pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { style: { color: '#0f172a', fontWeight: '600' } }, colors: africaCDCColors },
                    line: { marker: { fillColor: auColors.corporateGreen, lineColor: auColors.plum, lineWidth: 2 }, dataLabels: { style: { color: '#0f172a', fontWeight: '600' } } }
                },
                series: [{
                    name: title,
                    color: africaCDCColors[0],
                    data: chartType === 'pie' ? labels.map(function(label, index) {
                        return { name: label, y: values[index], color: africaCDCColors[index % africaCDCColors.length] };
                    }) : chartType === 'line' ? values.map(function(value, index) {
                        return { name: labels[index], y: value, color: africaCDCColors[index % africaCDCColors.length] };
                    }) : values.map(function(value, index) {
                        return { y: value, color: africaCDCColors[index % africaCDCColors.length] };
                    }),
                    dataLabels: {
                        enabled: true,
                        format: chartType === 'pie' ? '{point.name}: {point.percentage:.1f}%' : '{point.y}',
                        style: { color: '#0f172a', fontWeight: '600' }
                    }
                }],
                tooltip: { shared: chartType !== 'pie', backgroundColor: auColors.white, borderColor: auColors.corporateGreen, borderRadius: 8, style: { color: '#0f172a' } }
            });
        }
        Object.keys(jsonData).forEach(renderChart);

        var countrySelect = document.getElementById('countryFilter');
        if (countrySelect && jsonData.visits_by_country && Array.isArray(jsonData.visits_by_country.labels)) {
            countrySelect.innerHTML = '<option value="">All Countries</option>';
            jsonData.visits_by_country.labels.forEach(function(l) {
                var opt = document.createElement('option');
                opt.value = l;
                opt.textContent = l;
                countrySelect.appendChild(opt);
            });
        }

        renderWorldMap(jsonData);
    };

    function renderWorldMap(jsonData) {
        var data = jsonData && jsonData.visits_by_country;
        var mapContainer = document.getElementById('world-map');
        if (!mapContainer) return;
        if (!data || !data.labels || !data.values) {
            mapContainer.innerHTML = '<div class="text-muted text-center p-4">No visit data available</div>';
            return;
        }
        mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading map...</div>';
        var valueByISO2 = {}, valueByName = {};
        data.labels.forEach(function(label, i) {
            var text = (label || '').toString();
            var lower = text.toLowerCase();
            if (text.length === 2) valueByISO2[lower] = data.values[i];
            valueByName[lower] = data.values[i];
        });
        var geoNameToKey = {
            'united republic of tanzania': 'tanzania', 'tanzania': 'tanzania',
            'democratic republic of the congo': 'congo', 'republic of the congo': 'congo', 'congo': 'congo',
            'united states of america': 'united states', 'united states': 'united states',
            'republic of kenya': 'kenya', 'kenya': 'kenya',
            'federal republic of nigeria': 'nigeria', 'nigeria': 'nigeria',
            'republic of south africa': 'south africa', 'south africa': 'south africa',
            'republic of uganda': 'uganda', 'uganda': 'uganda',
            'republic of ghana': 'ghana', 'ghana': 'ghana',
            'republic of senegal': 'senegal', 'senegal': 'senegal',
            'republic of zambia': 'zambia', 'zambia': 'zambia',
            'republic of zimbabwe': 'zimbabwe', 'zimbabwe': 'zimbabwe',
            'republic of mali': 'mali', 'mali': 'mali',
            'republic of niger': 'niger', 'niger': 'niger',
            "côte d'ivoire": 'ivory coast', 'ivory coast': 'ivory coast', 'republic of côte d\'ivoire': 'ivory coast',
            'burkina faso': 'burkina faso', 'republic of burkina faso': 'burkina faso',
            'republic of cameroon': 'cameroon', 'cameroon': 'cameroon',
            'republic of ethiopia': 'ethiopia', 'ethiopia': 'ethiopia',
            'republic of mozambique': 'mozambique', 'mozambique': 'mozambique',
            'republic of madagascar': 'madagascar', 'madagascar': 'madagascar',
            'republic of malawi': 'malawi', 'malawi': 'malawi',
            'republic of rwanda': 'rwanda', 'rwanda': 'rwanda',
            'republic of botswana': 'botswana', 'botswana': 'botswana',
            'kingdom of morocco': 'morocco', 'morocco': 'morocco',
            'arab republic of egypt': 'egypt', 'egypt': 'egypt',
            'republic of tunisia': 'tunisia', 'tunisia': 'tunisia',
            'republic of algeria': 'algeria', 'algeria': 'algeria',
            'republic of libya': 'libya', 'libya': 'libya',
            'republic of sudan': 'sudan', 'sudan': 'sudan',
            'republic of somalia': 'somalia', 'somalia': 'somalia',
            'republic of mauritius': 'mauritius', 'mauritius': 'mauritius',
            'republic of namibia': 'namibia', 'namibia': 'namibia',
            'republic of angola': 'angola', 'angola': 'angola',
            'republic of guinea': 'guinea', 'guinea': 'guinea',
            'republic of sierra leone': 'sierra leone', 'sierra leone': 'sierra leone',
            'republic of liberia': 'liberia', 'liberia': 'liberia',
            'republic of togo': 'togo', 'togo': 'togo',
            'republic of benin': 'benin', 'benin': 'benin',
            'republic of gabon': 'gabon', 'gabon': 'gabon',
            'republic of chad': 'chad', 'chad': 'chad',
            'republic of central african republic': 'central african republic', 'central african republic': 'central african republic',
            'republic of malawi': 'malawi', 'eswatini': 'swaziland', 'swaziland': 'swaziland',
            'republic of lesotho': 'lesotho', 'lesotho': 'lesotho',
            'republic of gambia': 'gambia', 'gambia': 'gambia', 'the gambia': 'gambia',
            'republic of guinea-bissau': 'guinea-bissau', 'guinea-bissau': 'guinea-bissau',
            'republic of equatorial guinea': 'equatorial guinea', 'equatorial guinea': 'equatorial guinea',
            'republic of djibouti': 'djibouti', 'djibouti': 'djibouti',
            'republic of mauritania': 'mauritania', 'mauritania': 'mauritania',
            'republic of burundi': 'burundi', 'burundi': 'burundi',
            'republic of comoros': 'comoros', 'comoros': 'comoros',
            'republic of seychelles': 'seychelles', 'seychelles': 'seychelles',
            'republic of cape verde': 'cape verde', 'cape verde': 'cape verde', 'cabo verde': 'cape verde',
            'republic of sao tome and principe': 'são tomé and príncipe', 'são tomé and príncipe': 'são tomé and príncipe'
        };
        function getVal(props) {
            var iso2 = (props.iso_a2 || props.ISO_A2 || props.iso2 || props.cca2 || props.ISO2 || props['ISO-2'] || props.ADM0_A3 || '').toString().toLowerCase();
            var name = (props.name || props.ADMIN || props.admin || props.COUNTRY || props.NAME || '').toString().toLowerCase();
            var key = geoNameToKey[name] || name;
            if (valueByISO2[iso2] !== undefined) return valueByISO2[iso2];
            if (valueByName[key] !== undefined) return valueByName[key];
            if (valueByName[name] !== undefined) return valueByName[name];
            return 0;
        }
        function colorFor(v) {
            return v > 10000 ? auColors.plum : v > 5000 ? auColors.corporateGreen : v > 1000 ? auColors.green : v > 100 ? auColors.gold : v > 0 ? auColors.red : '#f0f0f0';
        }
        function loadLeaflet(cb) {
            if (window.L && typeof window.L.map === 'function') { cb(); return; }
            if (!document.querySelector('link[href*="leaflet"]')) {
                var css = document.createElement('link');
                css.rel = 'stylesheet';
                css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(css);
            }
            if (document.querySelector('script[src*="leaflet"]')) {
                var existing = document.querySelector('script[src*="leaflet"]');
                if (existing.onload) existing.onload = cb;
                else { var i = setInterval(function() { if (window.L) { clearInterval(i); cb(); } }, 50); }
                return;
            }
            var s = document.createElement('script');
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            s.onload = cb;
            s.onerror = function() {
                mapContainer.innerHTML = '<div class="text-danger text-center p-4">Failed to load map library. Please refresh the page.</div>';
            };
            document.body.appendChild(s);
        }
        loadLeaflet(function() {
            try {
                var map = L.map('world-map', { scrollWheelZoom: false }).setView([20, 0], 2);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 6, attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
                var sources = [
                    'https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson',
                    'https://cdn.jsdelivr.net/npm/geojson-world@1/world.geo.json',
                    'https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json'
                ];
                function loadGeo(idx) {
                    if (idx >= sources.length) return Promise.reject(new Error('No GeoJSON'));
                    return fetch(sources[idx], { mode: 'cors' }).then(function(r) { if (!r.ok) throw new Error(); return r.json(); }).catch(function() { return loadGeo(idx + 1); });
                }
                loadGeo(0).then(function(geo) {
                    var features = (geo.type === 'FeatureCollection' && geo.features) ? geo.features : [];
                    if (features.length === 0) throw new Error('No features');
                    var layer = L.geoJSON(features, {
                        style: function(f) { return { color: '#e2e8f0', weight: 1, fillColor: colorFor(getVal(f.properties)), fillOpacity: 0.9 }; },
                        onEachFeature: function(feature, lyr) {
                            var v = getVal(feature.properties);
                            var name = feature.properties.name || feature.properties.NAME || feature.properties.ADMIN || 'Unknown';
                            lyr.bindTooltip(name + ': <b>' + v + '</b>', { sticky: true });
                        }
                    }).addTo(map);
                    if (layer.getBounds().isValid()) map.fitBounds(layer.getBounds(), { padding: [10, 10] });
                    var legend = L.control({ position: 'bottomright' });
                    legend.onAdd = function() {
                        var div = L.DomUtil.create('div', 'info legend');
                        div.style.background = '#fff'; div.style.padding = '8px 10px'; div.style.border = '1px solid #e2e8f0'; div.style.borderRadius = '8px';
                        var grades = [0, 1, 100, 1000, 5000, 10000];
                        var html = '<div style="font-weight:600;margin-bottom:4px;">Visits</div>';
                        for (var i = 0; i < grades.length; i++) {
                            html += '<div><span style="display:inline-block;width:12px;height:12px;background:' + colorFor(grades[i] + 0.1) + ';margin-right:6px;border:1px solid #cbd5e1;"></span>' + grades[i] + (grades[i + 1] ? ('–' + grades[i + 1]) : '+') + '</div>';
                        }
                        div.innerHTML = html;
                        return div;
                    };
                    legend.addTo(map);
                }).catch(function() {
                    mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map unavailable. Please try refreshing the page.</div>';
                });
            } catch (e) {
                mapContainer.innerHTML = '<div class="text-danger text-center p-4">Error initializing map. Please refresh the page.</div>';
            }
        });
    }
})();
</script>
