@extends('layouts.dash')

@section('styles')

@endsection

@section('content')

<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>KPI Comparative Analysis</h5>
                <br>
                <form class="filter_form">
                    <div class="row">
                        <div class="form-group col-md-7">
                            <br>
                            <label>KPI Filter</label>
                            <select class="form-control select2" name="kpi_id" onchange="fetchData()">
                                <option value="">All</option>
                                <?php foreach ($kpis as $kpi) : ?>
                                    <option value="<?php echo $kpi->id; ?>"><?php echo $kpi->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <br>
                            <label>Period</label>
                            <select class="form-control select2" name="period_year" onchange="fetchData()">
                                <option value="">All</option>
                                <?php foreach ($years as $year) : ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <br>
                            <label>Switch Graph</label>
                            <select class="form-control select2" onchange="switchChartType($(this).val())">
                                <option value="spline">Line Chart</option>
                                <option value="bar">Bar Graph</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <div id="countries_summary" style="width: 100%; height: 350px;"></div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

@include('common.select2')

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

        //showLoader();

        $.ajax({
            data: $('.filter_form').serialize(),
            url: "{{ url('dashboards/kpi_comparison_data') }}",
            success: function(response) {
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

        fetchData();
    });

</script>

@endsection