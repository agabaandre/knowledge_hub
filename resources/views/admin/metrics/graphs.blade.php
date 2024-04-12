
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">System Metrics</h3>
                    </div>
                    <div class="card-body">

                       <div id="chart-container" class="row"></div>
    
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="{{ asset('assets/plugins/highcharts/highcharts.js') }}"></script>
<script>
    // Parse the JSON data
    const jsonData = @json($chart_data);

   // Function to render the chart
   function renderChart(key) {
      var data = jsonData[key];
      const chartType = data.chartType;
      const labels = data.labels;
      const values = data.values;
      const title = key.replaceAll('_', ' ').toUpperCase();

      // Create a container for the chart
      const chartContainer = document.createElement('div');
      chartContainer.id = key + '-chart';
      chartContainer.className = "col-lg-6 mt-3";
      chartContainer.style = "margin-top:50px!important;";
      document.getElementById('chart-container').appendChild(chartContainer);

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
        xAxis: {
          categories: categories, // Use categories for bar chart
        },
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
        }]
      });
    }

    // Render charts for each dataset
    Object.keys(jsonData).forEach(key => {
      renderChart(key);
    });

  </script>
