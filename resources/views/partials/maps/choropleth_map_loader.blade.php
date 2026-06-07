{{-- Provider-aware choropleth loader (Highcharts, Fusion, generic TopoJSON). --}}
<script>
(function (global) {
    if (global.KhChoroplethMap && global.KhChoroplethMap._ready) {
        return;
    }

    var settings = global.__khAfricaMapSettings || global.__khChoroplethMapSettings || {};
    var loadPromise = null;
    var topologyCache = null;

    function provider() {
        return (settings.provider || 'highcharts').toLowerCase();
    }

    function joinKey() {
        return settings.joinBy || settings.isoProperty || 'iso-a3';
    }

    function isoProperty() {
        return settings.isoProperty || joinKey();
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

    function load() {
        if (settings.type === 'topojson_url' || settings.type === 'geojson_url') {
            return loadTopologyMap();
        }
        return loadScriptMap();
    }

    function mapPoint(row) {
        row = row || {};
        var point = { value: row.value };
        var key = joinKey();

        if (row.name) point.country_name = row.name;
        if (row.country_name) point.country_name = row.country_name;
        if (row.display_value) point.display_value = row.display_value;
        if (row.period) point.period = row.period;
        if (row.detail_url) point.detail_url = row.detail_url;
        if (row.country_id) point.country_id = row.country_id;
        if (row.unit_id) point.unit_id = row.unit_id;

        if (row[key]) {
            point[key] = row[key];
        } else if (row['iso-a3']) {
            point['iso-a3'] = row['iso-a3'];
        } else if (row['hc-key']) {
            point['hc-key'] = row['hc-key'];
        } else if (row['iso-a2']) {
            point['iso-a2'] = row['iso-a2'];
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

    function renderHighcharts(containerId, mapAsset, seriesData, chartOptions) {
        chartOptions = chartOptions || {};
        if (typeof global.Highcharts === 'undefined' || !global.Highcharts.mapChart) {
            throw new Error('Highcharts map module not loaded');
        }
        return global.Highcharts.mapChart(containerId, Object.assign({
            chart: {
                map: chartMapOption(mapAsset),
                backgroundColor: '#f8f9fa',
                height: chartOptions.height || 480
            },
            title: { text: chartOptions.title || null },
            credits: {
                enabled: true,
                text: chartOptions.credits || creditsPrefix(),
                style: { fontSize: '10px', color: '#94a3b8' }
            },
            mapNavigation: { enabled: true },
            colorAxis: chartOptions.colorAxis || {},
            legend: { enabled: false },
            series: [{
                type: 'map',
                name: chartOptions.seriesName || 'Value',
                mapData: seriesMapData(mapAsset),
                data: seriesData,
                joinBy: joinKey(),
                dataLabels: dataLabels(),
                tooltip: chartOptions.tooltip || {}
            }]
        }, chartOptions.extra || {}));
    }

    global.KhChoroplethMap = {
        _ready: true,
        settings: settings,
        provider: provider,
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
