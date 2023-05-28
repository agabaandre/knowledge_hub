
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
</script>