<div class="row">

<div class="col-md-12">
    
<?php require_once 'partials/top_summary_widgets.php'; ?>

<?php require_once 'partials/large_charts.php'; ?>

</div>

</div>

<script src="<?php echo base_url(); ?>assets/js/pages/chart-highchart-custom.js"></script>

<!-- am chart js -->
<script src="<?php echo base_url(); ?>assets/plugins/chart-am4/js/core.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chart-am4/js/charts.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chart-am4/js/animated.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chart-am4/js/worldLow.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/chart-am4/js/continentsLow.js"></script>

<!-- dashboard-custom js -->
<script src="<?php echo base_url(); ?>assets/js/pages/dashboard-sale.js"></script>



<script type="text/javascript">

    var data = "<?php echo json_encode($countries_summary); ?>";
    console.log(data);

    $(document).ready(function() {
     // [ line-basic-chart ] Start
        Highcharts.chart('countries_summary', {
            chart: {
                type: 'spline',
            },
            colors: ['#0288d1', '#3949AB', '#463699'],
            title: {
                text: 'Country-Wise Analysis'
            },
            subtitle: {
                text: ' ' 
            },
            yAxis: {
                title: {
                    text: 'KPI Value'
                }
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: 2010
                }
            },
            series: [{
                name: 'Installation',
                data: [5, 25, 15, 35, 25, 35, 45, 75]
            }, {
                name: 'Manufacturing',
                data: [25, 35, 45, 75, 5, 25, 15, 35, ]
            }, {
                name: 'Sales & Distribution',
                data: [45, 75, 25, 5, 15, 55, 5, 25]
            }],
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
    });
</script>
