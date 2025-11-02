


            <div class="col-md-12">
                <div class="card" style="border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 1.5rem;">
                    <div class="card-header d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
                        <h3 class="card-title mb-0">System Metrics</h3>
                        <div class="filters-toolbar">
                            <div class="filter-item">
                                <i class="fa fa-calendar filter-icon"></i>
                                <input type="text" id="fromDate" class="filter-control datepicker" placeholder="From" />
                            </div>
                            <div class="filter-item">
                                <i class="fa fa-calendar filter-icon"></i>
                                <input type="text" id="toDate" class="filter-control datepicker" placeholder="To" />
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
                    <div class="card-body" style="padding: 1.5rem;">

                       <div id="chart-container" class="row" style="margin-left: -15px; margin-right: -15px;"></div>

                       <div class="row" style="margin-left: -15px; margin-right: -15px; margin-top: 1.5rem;">
                           <div class="col-12" style="padding-left: 15px; padding-right: 15px;">
                               <div class="card" style="border: 1px solid #e2e8f0; border-radius: 0;">
                                   <div class="card-header d-flex align-items-center justify-content-between" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;">
                                       <h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">Visits by Country</h3>
                                   </div>
                                   <div class="card-body" style="padding: 1.5rem;">
                                       <div id="world-map" style="width:100%;height:504px;"></div>
                                   </div>
                               </div>
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

    // AU (African Union) color palette from settings (global scope for use in charts and map)
    const auColors = {
        red: '{{ settings()->au_red ?? "#9F2241" }}',
        gold: '{{ settings()->au_gold ?? "#B4A269" }}',
        corporateGreen: '{{ settings()->au_corporate_green ?? "#1A5632" }}',
        green: '{{ settings()->au_green ?? "#1A5632" }}',
        plum: '{{ settings()->au_plum ?? "#522B39" }}',
        greyText: '{{ settings()->au_grey_text ?? "#58595B" }}',
        white: '{{ settings()->au_white ?? "#FFFFFF" }}'
    };

    // Helper function to lighten colors (global scope)
    function lightenColor(color, amount) {
        if (color.startsWith('#')) {
            const num = parseInt(color.replace('#', ''), 16);
            const r = Math.min(255, (num >> 16) + Math.round(amount * 255));
            const g = Math.min(255, ((num >> 8) & 0x00FF) + Math.round(amount * 255));
            const b = Math.min(255, (num & 0x0000FF) + Math.round(amount * 255));
            return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
        }
        return color;
    }

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

      // Create a card wrapper for the chart with proper spacing
      const col = document.createElement('div');
      col.className = 'col-xl-6 col-lg-6 col-md-12 mb-4';
      col.style.cssText = 'padding-left: 15px; padding-right: 15px;';
      
      const card = document.createElement('div'); 
      card.className = 'card h-100';
      card.style.cssText = 'border: 1px solid #e2e8f0; border-radius: 0; margin-bottom: 0;';
      
      const header = document.createElement('div'); 
      header.className = 'card-header'; 
      header.style.cssText = 'background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.5rem;';
      header.innerHTML = `<h3 class="card-title mb-0" style="font-size: 1rem; font-weight: 600;">${title}</h3>`;
      
      const body = document.createElement('div'); 
      body.className = 'card-body';
      body.style.cssText = 'padding: 1.5rem;';
      
      const chartContainer = document.createElement('div');
      chartContainer.id = key + '-chart';
      chartContainer.style.cssText = 'height:360px;';
      
      body.appendChild(chartContainer); 
      card.appendChild(header); 
      card.appendChild(body); 
      col.appendChild(card);
      document.getElementById('chart-container').appendChild(col);

      // Prepare x-axis categories for bar chart
      let categories = null;
      if (chartType === 'bar' || chartType === 'line' ) {
        categories = labels;
      }

      const africaCDCColors = [
        auColors.corporateGreen,  // AU Corporate Green
        auColors.red,              // AU Red
        auColors.gold,             // AU Gold
        auColors.green,            // AU Green
        auColors.plum,             // Agenda 2063 Plum
        auColors.greyText,         // AU Grey Text
        // Additional shades for variety (lightened versions of AU colors)
        lightenColor(auColors.corporateGreen, 0.3),  // Lightened Corporate Green
        lightenColor(auColors.red, 0.3),             // Lightened Red
        lightenColor(auColors.gold, 0.3),            // Lightened Gold
        lightenColor(auColors.green, 0.3)            // Lightened Green
      ];

      // Create the chart based on the specified type
      Highcharts.chart(chartContainer.id, {
        chart: {
          type: chartType,
          backgroundColor: 'transparent'
        },
        colors: africaCDCColors,
        title: {
          text: null  // Title is shown in card header instead
        },
        credits: { enabled: false },
        xAxis: {
          categories: categories, // Use categories for bar chart
          lineColor: '#e2e8f0',
          tickColor: '#e2e8f0',
          labels: {
            style: {
              color: auColors.greyText
            }
          }
        },
        yAxis: {
          title: { text: null },
          gridLineColor: '#e2e8f0',
          lineColor: '#e2e8f0',
          labels: {
            style: {
              color: auColors.greyText
            }
          }
        },
        legend: { 
          enabled: chartType !== 'pie',
          itemStyle: {
            color: auColors.greyText
          }
        },
        plotOptions: {
          bar: {
            colorByPoint: true,
            dataLabels: {
              style: {
                color: '#0f172a',
                fontWeight: '600'
              }
            }
          },
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
              style: {
                color: '#0f172a',
                fontWeight: '600'
              }
            },
            colors: africaCDCColors
          },
          line: {
            marker: {
              fillColor: auColors.corporateGreen,
              lineColor: auColors.plum,
              lineWidth: 2
            },
            dataLabels: {
              style: {
                color: '#0f172a',
                fontWeight: '600'
              }
            }
          }
        },
        series: [{
          name: title,
          color: africaCDCColors[0], // Default to primary color for single series
          data: chartType === 'pie' ? labels.map((label, index) => ({
            name: label,
            y: values[index],
            color: africaCDCColors[index % africaCDCColors.length]
          })) : chartType === 'line' ? values.map((value, index) => ({
            name: labels[index], // Use labels for line chart
            y: value,
            color: africaCDCColors[index % africaCDCColors.length]
          })) : values.map((value, index) => ({
            y: value,
            color: africaCDCColors[index % africaCDCColors.length]
          })), // Use values with colors for bar chart
          dataLabels: {
            enabled: true,
            format: chartType === 'pie' ? '{point.name}: {point.percentage:.1f}%' : '{point.y}',
            style: {
              color: '#0f172a',
              fontWeight: '600'
            }
          }
        }],
        tooltip: { 
          shared: chartType !== 'pie',
          backgroundColor: auColors.white,
          borderColor: auColors.corporateGreen,
          borderRadius: 8,
          style: {
            color: '#0f172a'
          }
        }
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
        const mapContainer = document.getElementById('world-map');
        
        if(!data || !data.labels || !data.values) {
            if(mapContainer) {
                mapContainer.innerHTML = '<div class="text-muted text-center p-4">No visit data available</div>';
            }
            return;
        }

        if(!mapContainer) {
            console.error('Map container not found');
            return;
        }

        // Show loading state
        mapContainer.innerHTML = '<div class="text-muted text-center p-4"><i class="fa fa-spinner fa-spin"></i> Loading map...</div>';

        function loadLeaflet(callback){
            if (window.L && typeof window.L.map === 'function') { 
                callback(); 
                return; 
            }
            
            // Check if Leaflet CSS is already loaded
            if (!document.querySelector('link[href*="leaflet"]')) {
                var css = document.createElement('link'); 
                css.rel='stylesheet'; 
                css.href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(css);
            }
            
            // Check if Leaflet script is already loading
            if (document.querySelector('script[src*="leaflet"]')) {
                var existingScript = document.querySelector('script[src*="leaflet"]');
                existingScript.onload = callback;
                return;
            }
            
            var s = document.createElement('script');
            s.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            s.onload = callback;
            s.onerror = function(){ 
                console.error('Leaflet failed to load');
                mapContainer.innerHTML = '<div class="text-danger text-center p-4">Failed to load map library. Please refresh the page.</div>';
            };
            document.body.appendChild(s);
        }

        loadLeaflet(function(){
            try {
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
                    'https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson',
                    'https://cdn.jsdelivr.net/npm/geojson-world@1/world.geo.json',
                    'https://raw.githubusercontent.com/johan/world.geo.json/master/countries.geo.json'
                ];

                function loadGeo(idx){
                    if (idx >= sources.length){ 
                        throw new Error('No geojson sources available'); 
                    }
                    return fetch(sources[idx], { mode:'cors' }).then(r=>{
                        if(!r.ok) throw new Error('Bad response');
                        return r.json();
                    }).catch((err)=>{
                        console.warn(`GeoJSON source ${idx + 1} failed:`, err);
                        return loadGeo(idx+1);
                    });
                }

                loadGeo(0).then(geo => {
                    // AU color scale helper (using AU colors from global scope)
                    function colorFor(v){
                      // Create gradient using AU colors from high to low values
                      if (!auColors) {
                          console.error('auColors not defined');
                          return '#f0f0f0';
                      }
                      return v>10000?auColors.plum:v>5000?auColors.corporateGreen:v>1000?auColors.green:v>100?auColors.gold:v>0?auColors.red:'#f0f0f0';
                    }

                    function getVal(props){
                      const iso2 = (props.iso_a2 || props.ISO_A2 || props.iso2 || props.cca2 || props.ISO2 || props['ISO-2'] || props.ADM0_A3 || '').toString().toLowerCase();
                      const name = (props.name || props.ADMIN || props.admin || props.COUNTRY || props.NAME || '').toString().toLowerCase();
                      return (valueByISO2[iso2] ?? valueByName[name] ?? 0);
                    }

                    // Check if geo has features array (GeoJSON) or is a TopoJSON
                    let features = [];
                    if (geo.type === 'FeatureCollection' && geo.features) {
                        features = geo.features;
                    } else if (geo.type === 'Topology' && geo.objects) {
                        // Convert TopoJSON to GeoJSON features
                        const topojson = window.topojson || null;
                        if (topojson) {
                            const objectKey = Object.keys(geo.objects)[0];
                            features = topojson.feature(geo, geo.objects[objectKey]).features;
                        } else {
                            throw new Error('TopoJSON detected but topojson library not available');
                        }
                    } else {
                        throw new Error('Invalid GeoJSON format');
                    }

                    if (features.length === 0) {
                        throw new Error('No features found in GeoJSON');
                    }

                    const layer = L.geoJSON(features, {
                      style: f => ({ color:'#e2e8f0', weight:1, fillColor: colorFor(getVal(f.properties)), fillOpacity: 0.9 }),
                      onEachFeature: function (feature, lyr) {
                        const v = getVal(feature.properties);
                        const name = feature.properties.name || feature.properties.NAME || feature.properties.ADMIN || 'Unknown';
                        lyr.bindTooltip(`${name}: <b>${v}</b>`,{sticky:true});
                      }
                    }).addTo(map);
                    
                    if (layer.getBounds().isValid()) {
                        map.fitBounds(layer.getBounds(), { padding:[10,10] });
                    }

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
                  .catch((err)=>{
                    console.error('Map rendering error:', err);
                    mapContainer.innerHTML = '<div class="text-muted text-center p-4">Map unavailable. Please try refreshing the page.</div>';
                  });
            } catch (error) {
                console.error('Leaflet initialization error:', error);
                mapContainer.innerHTML = '<div class="text-danger text-center p-4">Error initializing map. Please refresh the page.</div>';
            }
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
.filters-toolbar .filter-control:focus{ border-color:#cbd5e1; background:#fff; box-shadow:0 0 0 3px rgba(26,86,50,.08); }
.filters-toolbar .filter-icon{ position:absolute; left:10px; top:9px; color:{{ settings()->au_grey_text ?? '#58595B' }}; font-size:14px; }
.btn-apply{ height:36px; border-radius:10px; background:{{ settings()->au_corporate_green ?? '#1A5632' }}; color:{{ settings()->au_white ?? '#FFFFFF' }}; padding:6px 14px; border:1px solid {{ settings()->au_corporate_green ?? '#1A5632' }}; }
.btn-apply:hover{ background:{{ settings()->au_plum ?? '#522B39' }}; border-color:{{ settings()->au_plum ?? '#522B39' }}; }
</style>
