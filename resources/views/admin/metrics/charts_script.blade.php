{{-- Defines window.renderMetricsCharts(chartData) for dashboard AJAX. Highcharts core is loaded in admin header. --}}
@include('partials.maps.africa_map_config', ['mapContext' => 'admin_metrics'])
<script>
window.__khWorldMapSettings = @json(map_settings_for_js('admin_visits'));
</script>
@include('partials.maps.africa_map_loader')
<script>
(function() {
    var auColors = {
        red: '{{ settings()->au_red ?? "#9F2241" }}',
        gold: '{{ settings()->au_gold ?? "#B4A269" }}',
        corporateGreen: '{{ settings()->au_corporate_green ?? "#1A5632" }}',
        green: '{{ settings()->au_corporate_green ?? "#1A5632" }}',
        plum: '{{ settings()->au_plum ?? "#522B39" }}',
        greyText: '{{ settings()->au_grey_text ?? "#58595B" }}',
        white: '{{ settings()->au_white ?? "#FFFFFF" }}',
        light: '#f0f7f4',
        mapBorder: '{{ settings()->au_grey_text ?? "#58595B" }}'
    };

    function normalizeMapAxisRange(min, max) {
        min = Number(min);
        max = Number(max);
        if (!isFinite(min)) min = 0;
        if (!isFinite(max)) max = min;
        if (min === max) {
            max = min + (min === 0 ? 1 : Math.abs(min) * 0.05 || 1);
        }
        return { min: min, max: max };
    }

    function themeMapColorAxis(min, max) {
        var axis = normalizeMapAxisRange(min, max);
        return {
            min: axis.min,
            max: axis.max,
            minColor: auColors.light,
            maxColor: auColors.green,
            labels: { style: { color: '#475569', fontSize: '10px' } }
        };
    }

    /** Choropleth styling aligned with /countries indicator map. */
    function choroplethMapPlotOptions() {
        return {
            map: {
                nullColor: '#f1f5f9',
                borderColor: '#ffffff',
                borderWidth: 0.5,
                states: {
                    hover: {
                        color: auColors.gold,
                        borderColor: auColors.red,
                        borderWidth: 1.2
                    }
                }
            }
        };
    }

    /** World visits map — clearer borders on the global basemap. */
    function worldVisitsMapPlotOptions() {
        return {
            map: {
                nullColor: '#f1f5f9',
                borderColor: auColors.mapBorder,
                borderWidth: 1,
                states: {
                    hover: {
                        color: auColors.gold,
                        borderColor: auColors.red,
                        borderWidth: 1.5
                    }
                }
            }
        };
    }

    var mapModulePromise = null;
    var visitsTimelineChart = null;
    var signupsTimelineChart = null;
    var livePollTimer = null;
    var lastLiveParams = null;

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

    /**
     * Load map module only — never load full highmaps.js when Highcharts core exists (error #16).
     */
    function ensureHighchartsMapsLoaded(callback) {
        if (typeof Highcharts !== 'undefined' && typeof Highcharts.mapChart === 'function') {
            callback();
            return;
        }
        if (typeof Highcharts === 'undefined') {
            console.error('Highcharts core not loaded');
            callback();
            return;
        }
        if (mapModulePromise) {
            mapModulePromise.then(function() { callback(); }).catch(function() { callback(); });
            return;
        }
        var mapModuleUrl = window.__metricsMapModuleUrl || '{{ asset('assets/plugins/highcharts/modules/map.js') }}';
        mapModulePromise = new Promise(function(resolve, reject) {
            var existing = document.querySelector('script[data-kh-map-module="1"]');
            if (existing) {
                if (existing.getAttribute('data-loaded') === '1') {
                    resolve();
                    return;
                }
                existing.addEventListener('load', function() { existing.setAttribute('data-loaded', '1'); resolve(); });
                existing.addEventListener('error', reject);
                return;
            }
            var s = document.createElement('script');
            s.src = mapModuleUrl;
            s.setAttribute('data-kh-map-module', '1');
            s.onload = function() {
                s.setAttribute('data-loaded', '1');
                resolve();
            };
            s.onerror = reject;
            document.head.appendChild(s);
        });
        mapModulePromise.then(function() { callback(); }).catch(function(err) {
            console.error('Highcharts map module failed to load', err);
            callback();
        });
    }

    function timelineChartOptions(title, color, data) {
        var granularity = data.granularity === 'daily' ? 'Daily' : 'Monthly';
        return {
            chart: { type: 'areaspline', backgroundColor: 'transparent', height: 240 },
            title: { text: granularity + ' ' + title, style: { fontSize: '13px', color: auColors.greyText } },
            credits: { enabled: false },
            xAxis: {
                categories: data.labels,
                lineColor: '#e2e8f0',
                tickColor: '#e2e8f0',
                labels: { style: { color: auColors.greyText, fontSize: '10px' } }
            },
            yAxis: {
                title: { text: null },
                gridLineColor: '#e2e8f0',
                labels: { style: { color: auColors.greyText } },
                min: 0
            },
            legend: { enabled: false },
            tooltip: {
                shared: true,
                backgroundColor: auColors.white,
                borderColor: color,
                borderRadius: 8
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.18,
                    marker: { enabled: data.labels.length <= 31, radius: 3 },
                    animation: { duration: 400 }
                },
                series: { animation: { duration: 400 } }
            },
            series: [{
                name: title,
                color: color,
                data: data.values
            }]
        };
    }

    function renderVisitsTimeline(jsonData, animate) {
        var container = document.getElementById('visits-over-time-chart');
        if (!container || typeof Highcharts === 'undefined') return;

        var data = jsonData.visits_over_time;
        if (!data || !data.labels || !data.values || data.labels.length === 0) {
            container.innerHTML = '<div class="text-muted text-center p-4">No visit trend data for the selected period.</div>';
            visitsTimelineChart = null;
            return;
        }

        if (visitsTimelineChart && visitsTimelineChart.renderTo === container) {
            visitsTimelineChart.xAxis[0].setCategories(data.labels, false);
            visitsTimelineChart.series[0].setData(data.values, animate !== false);
            visitsTimelineChart.redraw();
            return;
        }

        container.innerHTML = '';
        visitsTimelineChart = Highcharts.chart('visits-over-time-chart', timelineChartOptions('visits', auColors.corporateGreen, data));
    }

    function renderSignupsTimeline(jsonData, animate) {
        var container = document.getElementById('signups-over-time-chart');
        if (!container || typeof Highcharts === 'undefined') return;

        var data = jsonData.signups_over_time;
        if (!data || !data.labels || !data.values || data.labels.length === 0) {
            container.innerHTML = '<div class="text-muted text-center p-4">No signup trend data for the selected period.</div>';
            signupsTimelineChart = null;
            return;
        }

        if (signupsTimelineChart && signupsTimelineChart.renderTo === container) {
            signupsTimelineChart.xAxis[0].setCategories(data.labels, false);
            signupsTimelineChart.series[0].setData(data.values, animate !== false);
            signupsTimelineChart.redraw();
            return;
        }

        container.innerHTML = '';
        signupsTimelineChart = Highcharts.chart('signups-over-time-chart', timelineChartOptions('signups', auColors.red, data));
    }

    function updateKpiSummary(summary) {
        if (!summary) return;
        var map = {
            kpiTotalVisits: summary.total_visits,
            kpiTotalSignups: summary.total_signups,
            kpiVisitCountries: summary.countries_with_visits,
            kpiSignupCountries: summary.countries_with_signups
        };
        Object.keys(map).forEach(function(id) {
            var el = document.getElementById(id);
            if (el && map[id] !== undefined && map[id] !== null) {
                el.textContent = Number(map[id]).toLocaleString();
            }
        });
    }

    function updateCacheHint(cacheInfo) {
        var el = document.getElementById('metricsCacheHint');
        if (!el || !cacheInfo) return;
        el.textContent = cacheInfo.redis
            ? 'Metrics cached via Redis for faster loads · live charts refresh every 60s'
            : 'Live charts refresh every 60s';
        el.classList.toggle('is-redis', !!cacheInfo.redis);
    }

    var adminMapChart = null;
    var adminMapMode = 'visits';
    var adminKpiRegionId = null;

    function useMapSettings(scope) {
        if (!window.KhAfricaMap || typeof KhAfricaMap.configure !== 'function') {
            return;
        }
        var settings = scope === 'world'
            ? (window.__khWorldMapSettings || {})
            : (window.__khAfricaMapSettings || {});
        KhAfricaMap.configure(settings);
    }

    var worldVisitsTopologyPromise = null;

    function worldVisitsTopologyUrl() {
        return (window.__khWorldMapSettings && window.__khWorldMapSettings.topologyUrl)
            || 'https://code.highcharts.com/mapdata/2.3.3/custom/world.topo.json';
    }

    function loadWorldVisitsTopology() {
        if (!worldVisitsTopologyPromise) {
            worldVisitsTopologyPromise = fetch(worldVisitsTopologyUrl(), { mode: 'cors', credentials: 'omit' })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('World map topology unavailable');
                    }
                    return response.json();
                });
        }
        return worldVisitsTopologyPromise;
    }

    function buildVisitsMapSeriesData(data) {
        if (data.map_points && data.map_points.length) {
            return data.map_points.map(function (point) {
                var iso2 = String(point['hc-key'] || point.iso2 || '').toLowerCase();
                return {
                    'hc-key': iso2,
                    name: point.name || iso2.toUpperCase(),
                    value: Number(point.value)
                };
            }).filter(function (point) { return point['hc-key'] && isFinite(point.value); });
        }

        var points = [];
        var iso2Codes = data.iso2 || [];
        var values = data.values || [];
        for (var i = 0; i < values.length; i++) {
            var iso2 = String(iso2Codes[i] || '').toLowerCase();
            if (!iso2) {
                var label = String((data.labels && data.labels[i]) || '').trim();
                if (label.length === 2) {
                    iso2 = label.toLowerCase();
                }
            }
            if (!iso2) {
                continue;
            }
            points.push({
                'hc-key': iso2,
                name: (data.labels && data.labels[i]) ? data.labels[i] : iso2.toUpperCase(),
                value: Number(values[i])
            });
        }
        return points;
    }

    function renderWorldVisitsMap(topology, mapData, config) {
        var mapContainer = document.getElementById('admin-africa-map');
        if (!mapContainer || typeof Highcharts === 'undefined' || typeof Highcharts.mapChart !== 'function') {
            return;
        }
        config = config || {};
        mapContainer.innerHTML = '';
        destroyAdminMap();
        adminMapChart = Highcharts.mapChart('admin-africa-map', {
            chart: {
                map: topology,
                backgroundColor: '#f8f9fa',
                height: config.height || 504,
                style: { fontFamily: 'inherit' }
            },
            title: {
                text: config.title || 'Visits by country',
                style: { fontSize: '14px', color: '#64748b', fontWeight: '600' }
            },
            credits: {
                enabled: true,
                text: config.credits || 'Map © Natural Earth · Portal access logs',
                style: { fontSize: '10px', color: '#94a3b8' }
            },
            mapNavigation: {
                enabled: true,
                buttonOptions: { verticalAlign: 'bottom', align: 'right' }
            },
            colorAxis: themeMapColorAxis(config.min, config.max),
            legend: { enabled: false },
            plotOptions: worldVisitsMapPlotOptions(),
            series: [{
                type: 'map',
                name: config.seriesName || 'Visits by country',
                joinBy: ['hc-key', 'hc-key'],
                colorAxis: 0,
                colorKey: 'value',
                nullColor: '#f1f5f9',
                data: mapData,
                dataLabels: { enabled: false },
                tooltip: config.tooltip || {
                    useHTML: true,
                    formatter: function () {
                        var p = this.point;
                        return '<b>' + (p.name || '') + '</b><br/><strong>'
                            + Number(p.value || 0).toLocaleString() + '</strong> visits';
                    }
                }
            }]
        });
    }

    function destroyAdminMap() {
        if (adminMapChart) {
            try { adminMapChart.destroy(); } catch (e) { /* ignore */ }
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

    function africaMapChartOptions(mapAsset, mapData, config) {
        config = config || {};
        var joinBy = config.joinBy || (window.KhAfricaMap && KhAfricaMap.joinKey()) || 'iso-a3';
        return {
            chart: {
                map: KhAfricaMap.chartMapOption(mapAsset),
                backgroundColor: '#f8f9fa',
                height: config.height || 480,
                style: { fontFamily: 'inherit' }
            },
            title: config.title === null
                ? { text: null }
                : {
                    text: config.title || config.seriesName || 'Indicator value',
                    style: { fontSize: '14px', color: '#64748b', fontWeight: '600' }
                },
            credits: { enabled: true, text: config.credits || KhAfricaMap.creditsPrefix(), style: { fontSize: '10px', color: '#94a3b8' } },
            mapNavigation: { enabled: true, buttonOptions: { verticalAlign: 'bottom', align: 'right' } },
            colorAxis: themeMapColorAxis(config.min, config.max),
            legend: { enabled: false },
            plotOptions: choroplethMapPlotOptions(),
            series: [{
                type: 'map',
                name: config.seriesName || 'Indicator value',
                mapData: KhAfricaMap.seriesMapData(mapAsset),
                data: mapData,
                joinBy: joinBy,
                colorAxis: 0,
                colorKey: 'value',
                nullColor: '#f1f5f9',
                dataLabels: config.dataLabels !== undefined ? config.dataLabels : KhAfricaMap.dataLabels(),
                tooltip: config.tooltip || { pointFormat: '<b>{point.name}</b><br/>{point.value}' }
            }]
        };
    }

    function renderAdminAfricaMap(mapAsset, mapData, config) {
        var mapContainer = document.getElementById('admin-africa-map');
        if (!mapContainer || typeof Highcharts === 'undefined') return;
        if (typeof Highcharts.mapChart !== 'function') {
            mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map module not loaded.</div>';
            return;
        }
        mapContainer.innerHTML = '';
        destroyAdminMap();
        adminMapChart = Highcharts.mapChart('admin-africa-map', africaMapChartOptions(mapAsset, mapData, config));
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
        var mapData = buildVisitsMapSeriesData(data || {});
        if (!data || !mapData.length) {
            mapContainer.innerHTML = '<div class="text-muted text-center p-4">No visit data for the selected period.</div>';
            updateAdminScopeSummary('<strong>Portal traffic</strong><span class="admin-map-scope-summary__meta">No visits recorded for this filter.</span>');
            updateAdminMapLegend('Visits', null, null);
            updateVisitsMapStats(null);
            return;
        }
        updateVisitsMapStats(data);
        var total = data.values.reduce(function (a, b) { return a + b; }, 0);
        updateAdminScopeSummary('<strong>Worldwide portal traffic</strong><span class="admin-map-scope-summary__value">' + total.toLocaleString() + ' visits</span><span class="admin-map-scope-summary__meta">' + mapData.length + ' countries with activity</span>');
        var values = mapData.map(function (point) { return point.value; });
        var min = Math.min.apply(null, values);
        var max = Math.max.apply(null, values);
        updateAdminMapLegend('Visits by country', min, max, function (v) { return Number(v).toLocaleString() + ' visits'; });
        mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading map…</div>';
        ensureHighchartsMapsLoaded(function() {
            loadWorldVisitsTopology().then(function (topology) {
                renderWorldVisitsMap(topology, mapData, {
                    min: 0,
                    max: max,
                    height: 504,
                    title: 'Visits by country',
                    seriesName: 'Visits by country',
                    credits: KhAfricaMap && KhAfricaMap.creditsPrefix
                        ? KhAfricaMap.creditsPrefix() + ' · Portal access logs'
                        : 'Map © Natural Earth · Portal access logs'
                });
            }).catch(function () {
                mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map unavailable. Please refresh and try again.</div>';
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
            useMapSettings('africa');
            KhAfricaMap.load().then(function(mapAsset) {
                var mapData = mapPayload.points.map(function (p) {
                    var point = KhAfricaMap.mapPoint(p);
                    if (point.value !== null && point.value !== undefined && point.value !== '') {
                        point.value = Number(point.value);
                    }
                    return point;
                });
                renderAdminAfricaMap(mapAsset, mapData, {
                    min: mapPayload.min,
                    max: mapPayload.max,
                    title: mapPayload.kpi_name || 'Indicator value',
                    seriesName: mapPayload.kpi_name || 'Indicator value',
                    credits: KhAfricaMap.creditsPrefix() + ' · Data: Our World in Data (CC BY 4.0)',
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            var p = this.point;
                            return '<b>' + (p.country_name || p.name || '') + '</b><br/>' + (p.display_value || p.value) + '<br/><span style="color:#64748b">Period: ' + (p.period || '—') + '</span>';
                        }
                    }
                });
            }).catch(function() {
                mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map unavailable.</div>';
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

    var chartTitleMap = {
        signups_by_country: 'Signups by country',
        monthly_signups: 'Monthly signups',
        monthly_publications: 'Monthly publications'
    };

    window.renderMetricsCharts = function(chartData, options) {
        options = options || {};
        if (!chartData || typeof Highcharts === 'undefined') return;

        var jsonData = chartData;
        var container = document.getElementById('chart-container');
        if (!container) return;

        updateKpiSummary(options.summary);
        updateCacheHint(options.cache);

        container.innerHTML = '';

        function shouldRenderChart(key, data) {
            if (key === 'visits_by_country' || key === 'visits_over_time' || key === 'signups_over_time') return false;
            if (data && data.renderAsChart === false) return false;
            return true;
        }

        function renderChart(key) {
            var data = jsonData[key];
            if (!shouldRenderChart(key, data) || !data || !data.labels || !data.values) return;
            var chartType = data.chartType;
            var labels = data.labels;
            var values = data.values;
            var title = chartTitleMap[key] || key.replace(/_/g, ' ').replace(/\b\w/g, function(c) { return c.toUpperCase(); });
            var col = document.createElement('div');
            col.className = 'col-xl-4 col-lg-6 col-md-12 mb-3';
            col.style.cssText = 'padding-left: 12px; padding-right: 12px;';
            var card = document.createElement('div');
            card.className = 'card h-100';
            var header = document.createElement('div');
            header.className = 'card-header';
            header.innerHTML = '<h3 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">' + title + '</h3>';
            var body = document.createElement('div');
            body.className = 'card-body';
            body.style.cssText = 'padding: 1rem;';
            var chartContainer = document.createElement('div');
            chartContainer.id = key + '-chart';
            chartContainer.style.cssText = 'height:320px;';
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
                    labels: { style: { color: auColors.greyText, fontSize: '10px' } }
                },
                yAxis: {
                    title: { text: null },
                    gridLineColor: '#e2e8f0',
                    lineColor: '#e2e8f0',
                    labels: { style: { color: auColors.greyText } },
                    min: 0
                },
                legend: { enabled: chartType !== 'pie', itemStyle: { color: auColors.greyText } },
                plotOptions: {
                    bar: { colorByPoint: true, borderRadius: 4, dataLabels: { enabled: false } },
                    pie: { allowPointSelect: true, cursor: 'pointer', dataLabels: { style: { color: '#0f172a', fontWeight: '600', fontSize: '10px' } }, colors: africaCDCColors },
                    line: { marker: { fillColor: auColors.corporateGreen, radius: 3 } }
                },
                series: [{
                    name: title,
                    color: africaCDCColors[0],
                    data: chartType === 'pie' ? labels.map(function(label, index) {
                        return { name: label, y: values[index], color: africaCDCColors[index % africaCDCColors.length] };
                    }) : chartType === 'line' ? values.map(function(value, index) {
                        return { name: labels[index], y: value };
                    }) : values.map(function(value) {
                        return { y: value };
                    })
                }],
                tooltip: { shared: chartType !== 'pie', backgroundColor: auColors.white, borderColor: auColors.corporateGreen, borderRadius: 8 }
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
            if (selected) countrySelect.value = selected;
        }

        renderVisitsTimeline(jsonData, false);
        renderSignupsTimeline(jsonData, false);
        bindAdminMapUi(jsonData);

        if (adminMapMode === 'indicators') {
            var kpiId = parseInt((document.getElementById('adminMapIndicatorSelect') || {}).value || '0', 10);
            if (kpiId > 0) fetchAdminKpiMap(kpiId, adminKpiRegionId);
            else if (window.__adminInitialKpiMap) renderKpiMap(window.__adminInitialKpiMap);
        } else {
            renderVisitsMap(jsonData);
        }

        lastLiveParams = options.liveParams || null;
        window.startLiveMetricsPolling(lastLiveParams);
    };

    window.updateLiveMetricsCharts = function(payload) {
        if (!payload || !payload.chart_data) return;
        renderVisitsTimeline(payload.chart_data, true);
        renderSignupsTimeline(payload.chart_data, true);
        if (payload.summary) updateKpiSummary(payload.summary);
    };

    window.startLiveMetricsPolling = function(params) {
        if (livePollTimer) {
            clearInterval(livePollTimer);
            livePollTimer = null;
        }
        lastLiveParams = params || lastLiveParams || {};
        var liveUrl = window.__metricsLiveUrl;
        if (!liveUrl) return;

        livePollTimer = setInterval(function() {
            if (!document.getElementById('visits-over-time-chart')) return;
            var url = liveUrl;
            var q = [];
            if (lastLiveParams.from) q.push('from=' + encodeURIComponent(lastLiveParams.from));
            if (lastLiveParams.to) q.push('to=' + encodeURIComponent(lastLiveParams.to));
            if (lastLiveParams.country) q.push('country=' + encodeURIComponent(lastLiveParams.country));
            if (q.length) url += (url.indexOf('?') === -1 ? '?' : '&') + q.join('&');
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function(r) { return r.json(); })
                .then(function(data) { window.updateLiveMetricsCharts(data); })
                .catch(function() { /* silent */ });
        }, 60000);
    };

    window.stopLiveMetricsPolling = function() {
        if (livePollTimer) {
            clearInterval(livePollTimer);
            livePollTimer = null;
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
