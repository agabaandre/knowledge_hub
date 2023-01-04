<div class="row">

    <div class="col-md-12">

    <div class="card">
        <div class="card-body">

    
        <form class="filter_form" method="get" action="<?php echo base_url("rccdashboards/index"); ?>">
            <div class="row">
                <div class="form-group col-md-4">
                    <br>
                    <label>Region</label>
                    <select class="form-control select2" name="region_id" onchange="getCountries();">
                        <option value="">All</option>
                        <?php foreach ($regions as $region) : ?>
                            <option <?php echo (isset($filter['region_id']) && $filter['region_id'] == $region->id) ? "selected" : ""; ?> value="<?php echo $region->id; ?>"><?php echo $region->region_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <br>
                    <label>Member State</label>
                    <select class="form-control select2 countries" name="country_id" onchange="$('.filter_form').submit();">

                    </select>
                </div>

                <div class="form-group col-md-4">
                    <br>
                    <label>Subject Area</label>
                    <select class="form-control select2 countries" name="country_id" onchange="$('.filter_form').submit();">

                    </select>
                </div>

            <div class="form-group col-md-4">
                <br>
                <label>Data Indicator</label>
                <select class="form-control select2 countries" name="country_id" onchange="$('.filter_form').submit();">

                </select>
            </div>
            <div class="form-group col-md-4">
                <br>
                <label>Period</label>
                <select class="form-control select2" name="period_year" onchange="fetchData()">
                    <option value="">All</option>
                    <?php foreach ($years as $year) : ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <br>
                <label>Visualisation Type</label>
                <select class="form-control select2" onchange="switchChartType($(this).val())">
                    <option value="spline">Line Chart</option>
                    <option value="bar">Bar Graph</option>
                </select>
            </div>
            </div>
</form>

</div>
</div>
            
</div>
</div>


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
                //hideLoader();
            },
            error: function(error) {
                console.log(error);
                //hideLoader();
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

        fetchData();

    });
</script>