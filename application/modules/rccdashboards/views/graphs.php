<?php require_once 'partials/top_summary_widgets.php'; ?>

<div class="row">
    <div class="card-body">
        <div id="countries_summary" style="width: 100%; height: 350px;"></div>
    </div>
</div>
<script type="text/javascript">
    var chartType = 'spline';
    var seriesData = null;
    var reRender = false;
    var chart = null;

    function switchChartType(type) {
        //var ui_chart = $('#countries_summary').highcharts();
        chart.series.forEach(function(item) {
            item.update({
                type: type
            });
        });
    }


    function fetchData() {


        $.ajax({
            data: $('.filter_form').serialize(),
            url: "<?php echo base_url(); ?>rccdashboards/kpi_comparison_data",
            success: function(response) {
                console.log(response);
                seriesData = JSON.parse(response);

                renderChart();
                hideLoader();
            },
            error: function(error) {
                console.log(error);
                hideLoader();
                alert("Server error stopped the operation");
            }
        });

    }


    function renderChart() {

        // [ line-basic-chart ] Start
        chart = Highcharts.chart('countries_summary', {
            chart: {
                type: chartType,
            },
            colors: Array(seriesData.data.length).fill().map(() => `#${Math.floor(Math.random()*16777215).toString(16)}`),
            title: {
                text: 'KPI Analysis'
            },
            subtitle: {
                text: 'Source: Africa CDC'
            },
            yAxis: {
                title: {
                    text: 'KPI Value'
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
    }


    $(document).ready(function() {

        showLoader();
        fetchData();

    });
</script>