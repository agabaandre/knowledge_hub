{{-- Provider-aware choropleth loader (Highcharts, Fusion, generic TopoJSON). --}}
<script>
(function (global) {
    if (global.KhChoroplethMap && global.KhChoroplethMap._ready) {
        return;
    }

    var settings = global.__khAfricaMapSettings || global.__khChoroplethMapSettings || {};
    var loadPromise = null;
    var topologyCache = null;
    var mapModulePromise = null;

    function provider() {
        return (settings.provider || 'highcharts').toLowerCase();
    }

    function joinKey() {
        return settings.joinBy || settings.isoProperty || 'iso-a3';
    }

    function isoProperty() {
        return settings.isoProperty || joinKey();
    }

    function theme() {
        return global.__khMapTheme || {
            green: '#1A5632',
            gold: '#B4A269',
            red: '#9F2241',
            grey: '#58595B',
            light: '#f0f7f4',
            nullColor: '#f1f5f9'
        };
    }

    function normalizeAxisRange(min, max) {
        min = Number(min);
        max = Number(max);
        if (!isFinite(min)) min = 0;
        if (!isFinite(max)) max = min;
        if (min === max) {
            max = min + (min === 0 ? 1 : Math.abs(min) * 0.05 || 1);
        }
        return { min: min, max: max };
    }

    function colorAxisOptions(min, max) {
        var t = theme();
        var axis = normalizeAxisRange(min, max);
        return {
            min: axis.min,
            max: axis.max,
            minColor: t.light,
            maxColor: t.green,
            labels: { style: { color: '#475569', fontSize: '10px' } }
        };
    }

    function choroplethPlotOptions(variant) {
        var t = theme();
        var isWorld = variant === 'world';
        return {
            map: {
                nullColor: t.nullColor,
                borderColor: isWorld ? t.grey : '#ffffff',
                borderWidth: isWorld ? 1 : 0.5,
                states: {
                    hover: {
                        color: t.gold,
                        borderColor: t.red,
                        borderWidth: isWorld ? 1.5 : 1.2
                    }
                }
            }
        };
    }

    function dataLabels() {
        return {
            enabled: true,
            format: '{point.name}',
            style: {
                fontSize: '9px',
                fontWeight: 'bold',
                color: '#1e293b',
                textOutline: '2px #ffffff'
            },
            backgroundColor: 'rgba(255,255,255,0.82)',
            borderRadius: 3,
            padding: 2
        };
    }

    function defaultMapModuleUrl() {
        if (global.__khMapModuleUrl) {
            return global.__khMapModuleUrl;
        }
        var base = document.querySelector('script[src*="highcharts"]');
        if (base && base.src) {
            return base.src.replace(/highcharts(\.min)?\.js.*/, 'modules/map.js');
        }
        return '/assets/plugins/highcharts/modules/map.js';
    }

    function ensureMapModule(moduleUrl) {
        if (typeof global.Highcharts !== 'undefined' && typeof global.Highcharts.mapChart === 'function') {
            return Promise.resolve();
        }
        if (typeof global.Highcharts === 'undefined') {
            return Promise.reject(new Error('Highcharts core not loaded'));
        }
        if (mapModulePromise) {
            return mapModulePromise;
        }
        moduleUrl = moduleUrl || defaultMapModuleUrl();
        mapModulePromise = new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[data-kh-map-module="1"]');
            if (existing) {
                if (existing.getAttribute('data-loaded') === '1') {
                    resolve();
                    return;
                }
                existing.addEventListener('load', function () {
                    existing.setAttribute('data-loaded', '1');
                    resolve();
                });
                existing.addEventListener('error', reject);
                return;
            }
            var s = document.createElement('script');
            s.src = moduleUrl;
            s.setAttribute('data-kh-map-module', '1');
            s.onload = function () {
                s.setAttribute('data-loaded', '1');
                resolve();
            };
            s.onerror = function () {
                mapModulePromise = null;
                reject(new Error('Highcharts map module failed to load'));
            };
            document.head.appendChild(s);
        });
        return mapModulePromise;
    }

    function waitForScriptMap(timeoutMs) {
        timeoutMs = timeoutMs || 10000;
        return new Promise(function (resolve, reject) {
            var deadline = Date.now() + timeoutMs;
            (function poll() {
                if (global.Highcharts && global.Highcharts.maps && settings.key && global.Highcharts.maps[settings.key]) {
                    resolve(global.Highcharts.maps[settings.key]);
                    return;
                }
                if (Date.now() > deadline) {
                    reject(new Error('Map script data unavailable'));
                    return;
                }
                setTimeout(poll, 50);
            })();
        });
    }

    function loadScriptMap() {
        if (global.Highcharts && global.Highcharts.maps && settings.key && global.Highcharts.maps[settings.key]) {
            return Promise.resolve(global.Highcharts.maps[settings.key]);
        }
        if (loadPromise) {
            return loadPromise;
        }
        loadPromise = new Promise(function (resolve, reject) {
            var existing = document.querySelector('script[data-kh-map-script="1"]');
            if (existing) {
                waitForScriptMap().then(resolve).catch(reject);
                return;
            }
            var s = document.createElement('script');
            s.src = settings.scriptUrl;
            s.setAttribute('data-kh-map-script', '1');
            s.onload = function () { waitForScriptMap().then(resolve).catch(reject); };
            s.onerror = function () {
                loadPromise = null;
                reject(new Error('Map script failed to load'));
            };
            document.head.appendChild(s);
        });
        return loadPromise;
    }

    function loadTopologyMap() {
        if (topologyCache) {
            return Promise.resolve(topologyCache);
        }
        if (!settings.topologyUrl) {
            return Promise.reject(new Error('Topology URL missing'));
        }
        loadPromise = fetch(settings.topologyUrl, { mode: 'cors' })
            .then(function (r) {
                if (!r.ok) throw new Error('Map topology unavailable');
                return r.json();
            })
            .then(function (topology) {
                topologyCache = topology;
                return topology;
            })
            .catch(function (err) {
                loadPromise = null;
                throw err;
            });
        return loadPromise;
    }

    function configure(newSettings) {
        settings = newSettings || {};
        loadPromise = null;
        topologyCache = null;
        global.KhChoroplethMap.settings = settings;
    }

    function load() {
        if (settings.type === 'topojson_url' || settings.type === 'geojson_url') {
            return loadTopologyMap();
        }
        return loadScriptMap();
    }

    function mapPoint(row) {
        row = row || {};
        var point = {};
        var key = joinKey();
        if (row.value !== undefined && row.value !== null && row.value !== '') {
            point.value = Number(row.value);
        }

        if (row.name) point.name = row.name;
        if (row.country_name) point.country_name = row.country_name;
        if (row.display_value) point.display_value = row.display_value;
        if (row.period) point.period = row.period;
        if (row.detail_url) point.detail_url = row.detail_url;
        if (row.country_id) point.country_id = row.country_id;
        if (row.unit_id) point.unit_id = row.unit_id;

        ['iso-a3', 'hc-key', 'iso-a2'].forEach(function (prop) {
            if (row[prop] !== undefined && row[prop] !== null && row[prop] !== '') {
                point[prop] = row[prop];
            }
        });

        if (!point[key]) {
            if (row['iso-a3']) point['iso-a3'] = row['iso-a3'];
            if (row['hc-key']) point['hc-key'] = row['hc-key'];
            if (row['iso-a2']) point['iso-a2'] = row['iso-a2'];
        }

        point.id = row.id || point[key] || point['iso-a3'] || point['hc-key'] || point['iso-a2'];
        return point;
    }

    function fusionSeriesData(points) {
        return (points || []).map(function (row) {
            var point = mapPoint(row);
            var id = point.id || point[joinKey()] || point['iso-a3'] || point['hc-key'];
            return {
                id: id,
                value: point.value,
                displayValue: point.display_value || point.value,
                toolText: (point.country_name || row.name || id) + ', ' + (point.display_value || point.value)
            };
        });
    }

    function chartMapOption(mapData) {
        if (settings.type === 'topojson_url' || settings.type === 'geojson_url') {
            return mapData;
        }
        return settings.key;
    }

    function seriesMapData(mapData) {
        if (settings.type === 'topojson_url' || settings.type === 'geojson_url') {
            return undefined;
        }
        return mapData;
    }

    function creditsPrefix() {
        var providerLabel = provider();
        return 'Map © Natural Earth (' + providerLabel + ' v' + (settings.version || '1.0') + ')';
    }

    function buildChoroplethChartOptions(mapAsset, seriesData, config) {
        config = config || {};
        var t = theme();
        var joinBy = config.joinBy || joinKey();
        var series = {
            type: 'map',
            name: config.seriesName || 'Value',
            mapData: seriesMapData(mapAsset),
            data: seriesData,
            joinBy: joinBy,
            colorAxis: 0,
            colorKey: 'value',
            nullColor: t.nullColor,
            dataLabels: config.dataLabels !== undefined ? config.dataLabels : dataLabels(),
            tooltip: config.tooltip || { pointFormat: '<b>{point.name}</b><br/>{point.value}' }
        };
        if (config.point) {
            series.point = config.point;
        }

        var titleOption = { text: null };
        if (config.title === null) {
            titleOption = { text: null };
        } else if (config.title !== undefined) {
            titleOption = {
                text: config.title,
                style: { fontSize: '14px', color: '#64748b', fontWeight: config.titleWeight || '600' }
            };
        } else if (config.seriesName) {
            titleOption = {
                text: config.seriesName,
                style: { fontSize: '14px', color: '#64748b' }
            };
        }

        return {
            chart: {
                map: chartMapOption(mapAsset),
                backgroundColor: '#f8f9fa',
                height: config.height || 480,
                style: { fontFamily: 'inherit' }
            },
            title: titleOption,
            credits: {
                enabled: true,
                text: config.credits || creditsPrefix(),
                style: { fontSize: '10px', color: '#94a3b8' }
            },
            mapNavigation: config.mapNavigation || {
                enabled: true,
                buttonOptions: { verticalAlign: 'bottom', align: 'right' }
            },
            colorAxis: config.colorAxis || colorAxisOptions(config.min, config.max),
            legend: { enabled: false },
            plotOptions: choroplethPlotOptions(config.plotVariant || 'africa'),
            series: [series]
        };
    }

    function renderHighcharts(containerId, mapAsset, seriesData, chartOptions) {
        chartOptions = chartOptions || {};
        if (typeof global.Highcharts === 'undefined' || !global.Highcharts.mapChart) {
            throw new Error('Highcharts map module not loaded');
        }
        var options = buildChoroplethChartOptions(mapAsset, seriesData, chartOptions);
        if (chartOptions.extra) {
            Object.assign(options, chartOptions.extra);
        }
        return global.Highcharts.mapChart(containerId, options);
    }

    global.KhChoroplethMap = {
        _ready: true,
        settings: settings,
        configure: configure,
        provider: provider,
        theme: theme,
        normalizeAxisRange: normalizeAxisRange,
        colorAxisOptions: colorAxisOptions,
        choroplethPlotOptions: choroplethPlotOptions,
        buildChoroplethChartOptions: buildChoroplethChartOptions,
        ensureMapModule: ensureMapModule,
        load: load,
        dataLabels: dataLabels,
        joinKey: joinKey,
        isoProperty: isoProperty,
        mapPoint: mapPoint,
        fusionSeriesData: fusionSeriesData,
        chartMapOption: chartMapOption,
        seriesMapData: seriesMapData,
        creditsPrefix: creditsPrefix,
        renderHighcharts: renderHighcharts
    };

    global.KhAfricaMap = global.KhChoroplethMap;
})(window);
</script>
