{{-- Defines window.renderMetricsCharts(chartData) for dashboard AJAX. Requires Highcharts + Maps. --}}
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

    var HIGHCHARTS_MAP_TOPOLOGY = 'https://code.highcharts.com/mapdata/custom/world-highres3.topo.json';
    var mapTopologyCache = null;
    var mapTopologyPromise = null;

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

    function formatPeriodLabel(from, to) {
        if (from && to) return 'Showing visits from ' + from + ' to ' + to;
        if (from) return 'Showing visits from ' + from + ' onward';
        if (to) return 'Showing visits up to ' + to;
        return 'Showing all recorded visits';
    }

    function ensureHighchartsMapsLoaded(callback) {
        if (typeof Highcharts !== 'undefined' && typeof Highcharts.mapChart === 'function') {
            callback();
            return;
        }
        var localMaps = '{{ asset('assets/plugins/highcharts/highmaps.js') }}';
        var cdnMaps = 'https://code.highcharts.com/maps/highmaps.js';
        var scripts = [localMaps, cdnMaps];
        var index = 0;
        function loadNext() {
            if (typeof Highcharts !== 'undefined' && typeof Highcharts.mapChart === 'function') {
                callback();
                return;
            }
            if (index >= scripts.length) {
                console.error('Highcharts Maps could not be loaded');
                callback();
                return;
            }
            var src = scripts[index++];
            var existing = document.querySelector('script[src="' + src + '"]');
            if (existing) {
                if (existing.getAttribute('data-loaded') === '1') {
                    loadNext();
                    return;
                }
                existing.addEventListener('load', loadNext);
                existing.addEventListener('error', loadNext);
                return;
            }
            var s = document.createElement('script');
            s.src = src;
            s.onload = function() {
                s.setAttribute('data-loaded', '1');
                loadNext();
            };
            s.onerror = loadNext;
            document.head.appendChild(s);
        }
        loadNext();
    }

    function loadMapTopology() {
        if (mapTopologyCache) {
            return Promise.resolve(mapTopologyCache);
        }
        if (mapTopologyPromise) {
            return mapTopologyPromise;
        }
        mapTopologyPromise = fetch(HIGHCHARTS_MAP_TOPOLOGY, { mode: 'cors' })
            .then(function(r) {
                if (!r.ok) throw new Error('Map topology unavailable');
                return r.json();
            })
            .then(function(topology) {
                mapTopologyCache = topology;
                return topology;
            })
            .catch(function(err) {
                mapTopologyPromise = null;
                throw err;
            });
        return mapTopologyPromise;
    }

    function renderVisitsTimeline(jsonData) {
        var container = document.getElementById('visits-over-time-chart');
        if (!container || typeof Highcharts === 'undefined') return;

        var data = jsonData.visits_over_time;
        if (!data || !data.labels || !data.values || data.labels.length === 0) {
            container.innerHTML = '<div class="text-muted text-center p-4">No visit trend data for the selected period.</div>';
            return;
        }

        container.innerHTML = '';
        var granularity = data.granularity === 'daily' ? 'Daily' : 'Monthly';
        Highcharts.chart('visits-over-time-chart', {
            chart: { type: 'areaspline', backgroundColor: 'transparent' },
            title: { text: granularity + ' visits over time', style: { fontSize: '14px', color: auColors.greyText } },
            credits: { enabled: false },
            xAxis: {
                categories: data.labels,
                lineColor: '#e2e8f0',
                tickColor: '#e2e8f0',
                labels: { style: { color: auColors.greyText } }
            },
            yAxis: {
                title: { text: 'Visits' },
                gridLineColor: '#e2e8f0',
                labels: { style: { color: auColors.greyText } }
            },
            legend: { enabled: false },
            tooltip: {
                shared: true,
                backgroundColor: auColors.white,
                borderColor: auColors.corporateGreen,
                borderRadius: 8
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.2,
                    marker: { enabled: data.labels.length <= 31, radius: 3 }
                }
            },
            series: [{
                name: 'Visits',
                color: auColors.corporateGreen,
                data: data.values
            }]
        });
    }

    var adminMapChart = null;
    var adminMapMode = 'visits';
    var adminKpiRegionId = null;

    function destroyAdminMap() {
        if (adminMapChart) {
            adminMapChart.destroy();
            adminMapChart = null;
        }
    }

    function updateAdminMapLegend(title, min, max, formatFn) {
        var titleEl = document.getElementById('adminMapLegendTitle');
        var minEl = document.getElementById('adminMapLegendMin');
        var maxEl = document.getElementById('adminMapLegendMax');
        var barEl = document.getElementById('adminMapLegendBar');
        if (titleEl) titleEl.textContent = title || 'Map scale';
        if (!minEl || !maxEl) return;
        if (min === null || max === null || !isFinite(min) || !isFinite(max)) {
            minEl.textContent = 'No data';
            maxEl.textContent = '';
            if (barEl) barEl.style.opacity = '0.35';
            return;
        }
        if (barEl) barEl.style.opacity = '1';
        var fmt = formatFn || function (v) { return Number(v).toLocaleString(); };
        minEl.textContent = fmt(min);
        maxEl.textContent = fmt(max);
    }

    function updateAdminScopeSummary(html) {
        var el = document.getElementById('adminMapScopeSummary');
        if (el) el.innerHTML = html || '';
    }

    function africaMapChartOptions(topology, mapData, config) {
        config = config || {};
        return {
            chart: { map: topology, backgroundColor: 'transparent', height: 500, style: { fontFamily: 'inherit' } },
            title: { text: null },
            credits: { enabled: true, text: config.credits || 'Map © Natural Earth · Highcharts', style: { fontSize: '10px', color: '#94a3b8' } },
            mapNavigation: { enabled: true, buttonOptions: { verticalAlign: 'bottom', align: 'right' } },
            mapView: { zoom: 2.8, center: [20, 2], projection: { name: 'WebMercator' } },
            colorAxis: { min: config.min, max: config.max, minColor: '#f0f7f4', maxColor: auColors.corporateGreen },
            legend: { enabled: false },
            plotOptions: {
                map: {
                    nullColor: '#f1f5f9', borderColor: '#cbd5e1', borderWidth: 0.6,
                    states: { hover: { color: auColors.gold, borderColor: auColors.red, borderWidth: 1.1 } }
                }
            },
            series: [{
                type: 'map', name: config.seriesName || 'Value', data: mapData, joinBy: 'hc-key', dataLabels: { enabled: false },
                tooltip: config.tooltip || { pointFormat: '<b>{point.name}</b><br/>{point.value}' }
            }]
        };
    }

    function renderAdminAfricaMap(topology, mapData, config) {
        var mapContainer = document.getElementById('admin-africa-map');
        if (!mapContainer || typeof Highcharts === 'undefined') return;
        mapContainer.innerHTML = '';
        destroyAdminMap();
        adminMapChart = Highcharts.mapChart('admin-africa-map', africaMapChartOptions(topology, mapData, config));
    }

    function updateVisitsMapStats(data) {
        var grid = document.getElementById('adminVisitsMapStats');
        if (!grid) return;
        if (!data || !data.values || !data.values.length) { grid.innerHTML = ''; return; }
        var total = data.values.reduce(function (a, b) { return a + b; }, 0);
        var maxIdx = 0;
        data.values.forEach(function (v, i) { if (v > data.values[maxIdx]) maxIdx = i; });
        var topName = (data.labels && data.labels[maxIdx]) ? data.labels[maxIdx] : '—';
        grid.innerHTML =
            '<div class="admin-map-stat"><strong>' + total.toLocaleString() + '</strong><span>Total visits</span></div>' +
            '<div class="admin-map-stat"><strong>' + data.values.length + '</strong><span>Countries</span></div>' +
            '<div class="admin-map-stat"><strong>' + topName + '</strong><span>Top: ' + data.values[maxIdx].toLocaleString() + ' visits</span></div>';
    }

    function renderVisitsMap(jsonData) {
        var data = jsonData && jsonData.visits_by_country;
        var mapContainer = document.getElementById('admin-africa-map');
        var periodLabel = document.getElementById('visitsMapPeriodLabel');
        if (!mapContainer) return;
        var period = (data && data.period) || (jsonData.visits_over_time && jsonData.visits_over_time.period) || {};
        if (periodLabel) periodLabel.textContent = formatPeriodLabel(period.from || '', period.to || '');
        var stats = document.getElementById('adminVisitsMapStats');
        var kpiControls = document.getElementById('adminKpiMapControls');
        var chips = document.getElementById('adminContinentalChips');
        if (stats) stats.style.display = '';
        if (kpiControls) kpiControls.style.display = 'none';
        if (chips) chips.style.display = 'none';
        if (!data || !data.iso2 || !data.values || data.iso2.length === 0) {
            mapContainer.innerHTML = '<div class="text-muted text-center p-4">No visit data for the selected period.</div>';
            updateAdminScopeSummary('<strong>Portal traffic</strong><span class="admin-map-scope-summary__meta">No visits recorded for this filter.</span>');
            updateAdminMapLegend('Visits', null, null);
            updateVisitsMapStats(null);
            return;
        }
        updateVisitsMapStats(data);
        var total = data.values.reduce(function (a, b) { return a + b; }, 0);
        updateAdminScopeSummary('<strong>Portal traffic</strong><span class="admin-map-scope-summary__value">' + total.toLocaleString() + ' visits</span><span class="admin-map-scope-summary__meta">' + data.values.length + ' countries with activity</span>');
        var min = Math.min.apply(null, data.values);
        var max = Math.max.apply(null, data.values);
        updateAdminMapLegend('Visits by country', min, max, function (v) { return Number(v).toLocaleString() + ' visits'; });
        mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading map…</div>';
        ensureHighchartsMapsLoaded(function() {
            loadMapTopology().then(function(topology) {
                var mapData = data.iso2.map(function(code, i) {
                    return { 'hc-key': code, value: data.values[i], name: (data.labels && data.labels[i]) ? data.labels[i] : code.toUpperCase() };
                });
                renderAdminAfricaMap(topology, mapData, {
                    min: 0, max: max, seriesName: 'Visits', credits: 'Map © Natural Earth · Portal access logs',
                    tooltip: { useHTML: true, pointFormat: '<b>{point.name}</b><br/><strong>{point.value:,.0f}</strong> visits' }
                });
            }).catch(function() {
                mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map unavailable.</div>';
            });
        });
    }

    function renderKpiMap(mapPayload) {
        var mapContainer = document.getElementById('admin-africa-map');
        if (!mapContainer || !mapPayload) return;
        var stats = document.getElementById('adminVisitsMapStats');
        var kpiControls = document.getElementById('adminKpiMapControls');
        var chips = document.getElementById('adminContinentalChips');
        if (stats) stats.style.display = 'none';
        if (kpiControls) kpiControls.style.display = 'flex';
        if (chips) chips.style.display = 'block';
        var periodLabel = document.getElementById('visitsMapPeriodLabel');
        if (periodLabel) periodLabel.textContent = 'Indicator data from Our World in Data (CC BY 4.0).';
        var scopeName = 'Africa (all member states)';
        if (adminKpiRegionId && window.__adminMapRegions) {
            var region = window.__adminMapRegions.find(function (r) { return r.id === adminKpiRegionId; });
            if (region) scopeName = region.name;
        }
        var agg = mapPayload.aggregate || {};
        updateAdminScopeSummary('<strong>' + (mapPayload.kpi_name || 'Indicator') + '</strong> · ' + scopeName + '<span class="admin-map-scope-summary__value">' + (agg.value || '—') + (agg.unit_plain ? ' <em style="font-size:0.85rem;font-weight:500;color:#64748b">' + agg.unit_plain + '</em>' : '') + '</span><span class="admin-map-scope-summary__meta">' + (mapPayload.aggregation_label || '') + ' · ' + mapPayload.country_count + ' countries</span>');
        if (!mapPayload.points || !mapPayload.points.length) {
            mapContainer.innerHTML = '<div class="text-muted text-center p-4">No indicator data for this selection.</div>';
            updateAdminMapLegend(mapPayload.kpi_name || 'Indicator', null, null);
            return;
        }
        updateAdminMapLegend(mapPayload.kpi_name, mapPayload.min, mapPayload.max, function (v) {
            return agg.unit_plain ? Number(v).toLocaleString(undefined, { maximumFractionDigits: 2 }) + ' ' + agg.unit_plain : Number(v).toLocaleString(undefined, { maximumFractionDigits: 2 });
        });
        mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading map…</div>';
        ensureHighchartsMapsLoaded(function() {
            loadMapTopology().then(function(topology) {
                var mapData = mapPayload.points.map(function (p) {
                    return { 'hc-key': p['hc-key'], value: p.value, name: p.name, display_value: p.display_value, period: p.period };
                });
                renderAdminAfricaMap(topology, mapData, {
                    min: mapPayload.min, max: mapPayload.max, seriesName: mapPayload.kpi_name,
                    credits: 'Map © Natural Earth · Data: Our World in Data (CC BY 4.0)',
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            var p = this.point;
                            return '<b>' + (p.name || '') + '</b><br/>' + (p.display_value || p.value) + '<br/><span style="color:#64748b">Period: ' + (p.period || '—') + '</span>';
                        }
                    }
                });
            });
        });
    }

    function fetchAdminKpiMap(kpiId, regionId) {
        var url = (window.__adminMapDataUrl || '') + '?kpi_id=' + encodeURIComponent(kpiId);
        if (regionId) url += '&region_id=' + encodeURIComponent(regionId);
        var mapContainer = document.getElementById('admin-africa-map');
        if (mapContainer) mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading…</div>';
        return fetch(url, { headers: { 'Accept': 'application/json' } }).then(function (r) { return r.json(); }).then(function (json) { renderKpiMap(json.map || json); });
    }

    function setAdminMapMode(mode) {
        adminMapMode = mode;
        document.querySelectorAll('#adminAfricaMapTabs .nav-link').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-map-mode') === mode);
        });
    }

    function bindAdminMapUi(lastChartData) {
        document.querySelectorAll('#adminAfricaMapTabs .nav-link').forEach(function (btn) {
            btn.onclick = function () {
                var mode = this.getAttribute('data-map-mode');
                setAdminMapMode(mode);
                if (mode === 'visits') renderVisitsMap(lastChartData);
                else {
                    var kpiId = parseInt((document.getElementById('adminMapIndicatorSelect') || {}).value || '0', 10);
                    if (kpiId > 0) fetchAdminKpiMap(kpiId, adminKpiRegionId);
                    else if (window.__adminInitialKpiMap) renderKpiMap(window.__adminInitialKpiMap);
                }
            };
        });
        var indicatorSelect = document.getElementById('adminMapIndicatorSelect');
        if (indicatorSelect && !indicatorSelect.dataset.bound) {
            indicatorSelect.dataset.bound = '1';
            indicatorSelect.addEventListener('change', function () {
                setAdminMapMode('indicators');
                document.querySelectorAll('#adminAfricaMapTabs .nav-link').forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-map-mode') === 'indicators');
                });
                fetchAdminKpiMap(parseInt(this.value, 10), adminKpiRegionId);
            });
        }
        var regionSelect = document.getElementById('adminMapRegionSelect');
        if (regionSelect && !regionSelect.dataset.bound) {
            regionSelect.dataset.bound = '1';
            regionSelect.addEventListener('change', function () {
                adminKpiRegionId = this.value ? parseInt(this.value, 10) : null;
                if (adminMapMode === 'indicators') {
                    var kpiId = parseInt((document.getElementById('adminMapIndicatorSelect') || {}).value || '0', 10);
                    if (kpiId > 0) fetchAdminKpiMap(kpiId, adminKpiRegionId);
                }
            });
        }
        document.querySelectorAll('.js-admin-kpi-chip').forEach(function (chip) {
            chip.onclick = function () {
                var kpiId = parseInt(this.getAttribute('data-kpi-id'), 10);
                setAdminMapMode('indicators');
                document.querySelectorAll('#adminAfricaMapTabs .nav-link').forEach(function (b) {
                    b.classList.toggle('active', b.getAttribute('data-map-mode') === 'indicators');
                });
                if (indicatorSelect) indicatorSelect.value = String(kpiId);
                document.querySelectorAll('.js-admin-kpi-chip').forEach(function (c) {
                    c.classList.toggle('is-active', parseInt(c.getAttribute('data-kpi-id'), 10) === kpiId);
                });
                fetchAdminKpiMap(kpiId, adminKpiRegionId);
            };
        });
    }

    window.renderMetricsCharts = function(chartData, options) {
        options = options || {};
        if (!chartData || typeof Highcharts === 'undefined') return;

        var jsonData = chartData;
        var container = document.getElementById('chart-container');
        if (!container) return;
        container.innerHTML = '';

        function shouldRenderChart(key, data) {
            if (key === 'visits_by_country' || key === 'visits_over_time') return false;
            if (data && data.renderAsChart === false) return false;
            return true;
        }

        function renderChart(key) {
            var data = jsonData[key];
            if (!shouldRenderChart(key, data) || !data || !data.labels || !data.values) return;
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
        var visitCountries = options.visitCountries || [];
        if (countrySelect) {
            var selected = options.selectedCountry || countrySelect.value || '';
            countrySelect.innerHTML = '<option value="">All Countries</option>';
            if (visitCountries.length) {
                visitCountries.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.code;
                    opt.textContent = item.name + ' (' + item.count + ')';
                    countrySelect.appendChild(opt);
                });
            } else if (jsonData.visits_by_country && Array.isArray(jsonData.visits_by_country.iso2)) {
                jsonData.visits_by_country.iso2.forEach(function(code, i) {
                    var opt = document.createElement('option');
                    opt.value = code.toUpperCase();
                    opt.textContent = (jsonData.visits_by_country.labels[i] || code.toUpperCase()) + ' (' + jsonData.visits_by_country.values[i] + ')';
                    countrySelect.appendChild(opt);
                });
            }
            if (selected) {
                countrySelect.value = selected;
            }
        }

        renderVisitsTimeline(jsonData);
        bindAdminMapUi(jsonData);
        if (adminMapMode === 'indicators') {
            var kpiId = parseInt(document.getElementById('adminMapIndicatorSelect')?.value || '0', 10);
            if (kpiId > 0) {
                fetchAdminKpiMap(kpiId, adminKpiRegionId);
            } else if (window.__adminInitialKpiMap) {
                renderKpiMap(window.__adminInitialKpiMap);
            }
        } else {
            renderVisitsMap(jsonData);
        }
    };

    window.applyMetricsDatePreset = function(preset) {
        var fromInput = document.getElementById('fromDate');
        var toInput = document.getElementById('toDate');
        if (!fromInput || !toInput) return;

        var today = new Date();
        var to = today.toISOString().slice(0, 10);
        var from = '';

        if (preset === '7' || preset === '30' || preset === '90') {
            var days = parseInt(preset, 10);
            var start = new Date(today);
            start.setDate(start.getDate() - (days - 1));
            from = start.toISOString().slice(0, 10);
        } else if (preset === 'ytd') {
            from = today.getFullYear() + '-01-01';
        } else if (preset === 'all') {
            from = '';
            to = '';
        }

        fromInput.value = from;
        toInput.value = to;
    };
})();
</script>
