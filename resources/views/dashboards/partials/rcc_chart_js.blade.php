
<script type="text/javascript">
    var chartType = '<?php echo $filter['chart_type'] ?? 'bar'; ?>';
    var seriesData = null;
    var reRender = false;
    var chart = null;
    var charts = []; //to contain charts for future modifications

    function switchChartType(type) {

        charts.forEach(function(chart) {

            chart.series.forEach(function(item) {
                item.update({
                    type: type
                });
            });

        });
    }

    function fetchData(subjectAreaId='') {

        $.ajax({
            data: $('.filter_form').serialize(),
            url: `{{ url('dashboards/kpi_comparison_data/')}}?subject_area=${subjectAreaId}`,
            success: function(response) {
                
                console.log('data response',response);
                seriesData = JSON.parse(response);
                
                if(seriesData.data.length>0){
                 renderChart(subjectAreaId);
                }
                //hideLoader();
            },
            error: function(error) {
                console.log(error);
                //hideLoader();
                alert("Server error stopped the operation");
            }
        });

    }


    function renderChart(subjectAreaId) {

        // [ line-basic-chart ] Start
        chart = Highcharts.chart(`countries_summary${subjectAreaId}`, {
            chart: {
                type: chartType,
                height: 760,
            },
            colors: Array(seriesData.data.length).fill().map(() => `#${Math.floor(Math.random()*16777215).toString(16)}`),
            title: {
                text: 'Indicator Analysis'
            },
            subtitle: {
                text: 'Source: Africa CDC'
            },
            yAxis: {
                title: {
                    text: 'Indicator Value'
                }
            },
            xAxis: {
                categories: seriesData.labels
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle'
            },
            credits: {
            enabled: false
            },
         
            series: seriesData.data,
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });

        charts.push(chart);
    }


    $(document).ready(function() {

        var widget_data = JSON.parse('<?php echo json_encode($years_data); ?>');

        widgets = widget_data;

        console.log('years_data',widget_data);

        widget_data.forEach((subjectArea)=>{

          if(subjectArea.data.length>0)
          fetchData(subjectArea.subject_area_id);

        });

    });

     Highcharts.setOptions({
      colors: ['#4B5430','#6B4C24','#782C2D', '#C45B39Y','#8085e9','#000000', '#f15c80', '#e4d354', '#2b908f', '#f45b5b', '#91e8e1',  '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525',  '#a6c96a', '#4572A7', '#AA4643', '#89A54E', '#80699B',  '#3D96AE', '#DB843D', '#92A8CD', '#A47D7C', '#B5CA92',  '#058DC7', '#50B432', '#ED561B', '#DDDF00']

    });

</script>
