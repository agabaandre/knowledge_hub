
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title mb-0">System Metrics</h3>
                        <div class="filters-toolbar">
                            <div class="filter-item">
                                <i class="fa fa-calendar filter-icon"></i>
                                <input type="date" id="fromDate" class="filter-control" placeholder="From" />
                            </div>
                            <div class="filter-item">
                                <i class="fa fa-calendar filter-icon"></i>
                                <input type="date" id="toDate" class="filter-control" placeholder="To" />
                            </div>
                            <div class="filter-item">
                                <i class="fa fa-globe filter-icon"></i>
                                <select id="countryFilter" class="filter-control" style="min-width:200px;">
                                    <option value="">All Countries</option>
                                </select>
                            </div>
                            <button id="applyFilters" class="btn btn-apply"><i class="fa fa-filter mr-1"></i>Apply</button>
                        </div>
                    </div>
                    <div class="card-body">

                       <div class="row">
                           <div class="col-12 mb-3">
                               <div class="card">
                                   <div class="card-header d-flex align-items-center justify-content-between">
                                       <strong>Visits by Country</strong>
                                   </div>
                                   <div class="card-body p-0">
                                       <div id="world-map" style="width:100%;height:420px;border-top:1px solid #e2e8f0;border-radius:0 0 10px 10px;"></div>
                                   </div>
                               </div>
                           </div>
                       </div>

                       <div id="chart-container" class="row" style="margin-left:-6px;margin-right:-6px;"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
<!-- Leaflet for OSM basemap -->
<!-- Note: when this view is injected via AJAX, remote <script> tags may not execute.
     We therefore dynamically load Leaflet below if not present. -->
<script>
    // Parse the JSON data
    const jsonData = @json($chart_data);

   // Populate country filter from visits_by_country labels
   (function initCountryFilter(){
       const select = document.getElementById('countryFilter');
       const visits = jsonData['visits_by_country'];
       if(!visits || !Array.isArray(visits.labels)) return;
       visits.labels.forEach(l=>{ const opt=document.createElement('option'); opt.value=l; opt.textContent=l; select.appendChild(opt); });
   })();

   // Function to render a chart card
   function renderChart(key) {
      var data = jsonData[key];
      const chartType = data.chartType;
      const labels = data.labels;
      const values = data.values;
      const title = key.replaceAll('_', ' ').toUpperCase();

      // Create a card wrapper for the chart
      const col = document.createElement('div');
      col.className = 'col-xl-6 col-lg-6 col-md-12 mb-3';
      const card = document.createElement('div'); card.className = 'card h-100';
      const header = document.createElement('div'); header.className = 'card-header'; header.innerHTML = `<strong>${title}</strong>`;
      const body = document.createElement('div'); body.className = 'card-body';
      const chartContainer = document.createElement('div'); chartContainer.id = key + '-chart'; chartContainer.style = 'height:360px;';
      body.appendChild(chartContainer); card.appendChild(header); card.appendChild(body); col.appendChild(card);
      document.getElementById('chart-container').appendChild(col);

      // Prepare x-axis categories for bar chart
      let categories = null;
      if (chartType === 'bar' || chartType === 'line' ) {
        categories = labels;
      }

      // Create the chart based on the specified type
      Highcharts.chart(chartContainer.id, {
        chart: {
          type: chartType
        },
        title: {
          text: title
        },
        credits: { enabled: false },
        xAxis: {
          categories: categories, // Use categories for bar chart
        },
        yAxis: {
          title: { text: null },
          gridLineColor: '#e2e8f0'
        },
        legend: { enabled: chartType !== 'pie' },
        series: [{
          name: title,
          data: chartType === 'pie' ? labels.map((label, index) => ({
            name: label,
            y: values[index]
          })) : chartType === 'line' ? values.map((value, index) => ({
            name: labels[index], // Use labels for line chart
            y: value
          })) : values, // Use values directly for bar chart
          dataLabels: {
            enabled: true,
            format: chartType === 'pie' ? '{point.name}: {point.percentage:.1f}%' : '{point.y}'
          }
        }],
        tooltip: { shared: chartType !== 'pie' }
      });
    }

   // Render charts for each dataset (ensure container exists)
   (function ensureAndRender(){
       var container = document.getElementById('chart-container');
       if (!container) {
           const root = document.createElement('div');
           root.id = 'chart-container';
           root.className = 'row';
           document.body.appendChild(root);
       } else {
           container.innerHTML = '';
       }
       Object.keys(jsonData).forEach(key => { renderChart(key); });
   })();

   // Render world map using visits_by_country
   (function renderWorldMap(){
        const data = jsonData['visits_by_country'];
        if(!data) return;

        function loadLeaflet(callback){
            if (window.L && typeof window.L.map === 'function') { callback(); return; }
            var css = document.createElement('link'); css.rel='stylesheet'; css.href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(css);
            var s = document.createElement('script');
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            s.onload = callback;
            s.onerror = function(){ console.warn('Leaflet failed to load'); };
            document.body.appendChild(s);
        }

        loadLeaflet(function(){
            // Create Leaflet map with OSM tiles
            const map = L.map('world-map', { scrollWheelZoom: false }).setView([20, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 6,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

        // Build value dictionaries for ISO2 and name
        const valueByISO2 = {}; const valueByName = {};
        data.labels.forEach((label,i)=>{
            const text = (label||'').toString();
            const lower = text.toLowerCase();
            if (text.length === 2) valueByISO2[lower] = data.values[i];
            valueByName[lower] = data.values[i];
        });

        // Fetch world GeoJSON (contains iso_a2 or similar; we try several props)
        // Try multiple GeoJSON sources (CORS-friendly) and use the first that loads
        const sources = [
            'https://cdn.jsdelivr.net/gh/datasets/geo-countries@master/data/countries.geojson',
            'https://cdn.jsdelivr.net/npm/geojson-world@1/world.geo.json',
            'https://unpkg.com/@geo-maps/countries-land-1m@1.0.3/countries-land-1m.geo.json'
        ];

        function loadGeo(idx){
            if (idx >= sources.length){ throw new Error('No geojson sources available'); }
            return fetch(sources[idx], { mode:'cors' }).then(r=>{
                if(!r.ok) throw new Error('Bad response');
                return r.json();
            }).catch(()=> loadGeo(idx+1));
        }

        loadGeo(0).then(geo => {
            // Color scale helper
            function colorFor(v){
              return v>10000?'#0b5':v>5000?'#1a7f4a':v>1000?'#3fae6c':v>100?'#8cd3a8':v>0?'#d9f2e5':'#eef6f2';
            }

            function getVal(props){
              const iso2 = (props.iso_a2 || props.ISO_A2 || props.iso2 || props.cca2 || props.ISO2 || props['ISO-2'] || '').toString().toLowerCase();
              const name = (props.name || props.ADMIN || props.admin || props.COUNTRY || '').toString().toLowerCase();
              return (valueByISO2[iso2] ?? valueByName[name] ?? 0);
            }

            const layer = L.geoJSON(geo, {
              style: f => ({ color:'#e2e8f0', weight:1, fillColor: colorFor(getVal(f.properties)), fillOpacity: 0.9 }),
              onEachFeature: function (feature, lyr) {
                const v = getVal(feature.properties);
                lyr.bindTooltip(`${feature.properties.name}: <b>${v}</b>`,{sticky:true});
              }
            }).addTo(map);
            map.fitBounds(layer.getBounds(), { padding:[10,10] });

            // Simple legend
            const legend = L.control({position:'bottomright'});
            legend.onAdd = function(){
               const div = L.DomUtil.create('div','info legend');
               const grades=[0,1,100,1000,5000,10000];
               div.style.background='#fff'; div.style.padding='8px 10px'; div.style.border='1px solid #e2e8f0'; div.style.borderRadius='8px';
               let html='<div style="font-weight:600;margin-bottom:4px;">Visits</div>';
               for (let i=0;i<grades.length;i++){
                  const from=grades[i], to=grades[i+1];
                  html += `<div><span style="display:inline-block;width:12px;height:12px;background:${colorFor(from+0.1)};margin-right:6px;border:1px solid #cbd5e1;"></span>${from}${to?('&ndash;'+to):'+'}</div>`;
               }
               div.innerHTML=html; return div;
            };
            legend.addTo(map);
          })
          .catch(()=>{
            document.getElementById('world-map').innerHTML = '<div class="text-muted">Map unavailable</div>';
          });
        });
   })();

   // Simple client-side filter hook (placeholder for future backend filters)
   document.getElementById('applyFilters').addEventListener('click', function(){
        // Currently acts as a no-op visual trigger; hook backend as needed
        // You can extend: fetch metrics with fromDate/toDate/country and rerender charts here
        alert('Filters applied (placeholder). Backend can be wired to return filtered metrics.');
   });

  </script>
<style>
/* Compact, modern filter UI */
.filters-toolbar{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.filters-toolbar .filter-item{ position:relative; }
.filters-toolbar .filter-control{
    height:36px; padding:6px 12px 6px 34px; border:1px solid #e2e8f0; border-radius:10px; background:#f8fafc; color:#0f172a; outline:none;
}
.filters-toolbar .filter-control:focus{ border-color:#cbd5e1; background:#fff; box-shadow:0 0 0 3px rgba(17,154,72,.08); }
.filters-toolbar .filter-icon{ position:absolute; left:10px; top:9px; color:#64748b; font-size:14px; }
.btn-apply{ height:36px; border-radius:10px; background:#1f2937; color:#fff; padding:6px 14px; border:1px solid #111827; }
.btn-apply:hover{ background:#111827; }
</style>
