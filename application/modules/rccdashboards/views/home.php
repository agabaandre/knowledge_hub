<div class="row">

    <div class="col-md-12">

        <form class="filter_form" method="get" action="<?php echo base_url("rccdashboards/index"); ?>">
            <div class="row">
                <div class="form-group col-md-12">
                    <br>
                    <label>Country</label>
                    <select class="form-control select2" name="country_id" onchange="$('.filter_form').submit();">
                        <option value="">All</option>
                        <?php foreach ($countries as $country) : ?>
                            <option <?php echo (isset($filter['country_id']) && $filter['country_id'] == $country->id) ? "selected" : ""; ?> value="<?php echo $country->id; ?>"><?php echo $country->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
        </form>

        <?php require_once 'partials/top_summary_widgets.php'; ?>

        <?php require_once 'partials/large_charts.php'; ?>

    </div>

</div>
<div class="data" style="display: none;">

    <?php echo $graph_data; ?>

</div>


<script type="text/javascript">
    $(document).ready(function() {

        var seriesData = JSON.parse($('.data').html());

        console.log(seriesData);

        Highcharts.chart('large_chart', {
            chart: {
                type: 'column'
            },
            colors: Array(seriesData.length).fill().map(() => `#${Math.floor(Math.random()*16444215).toString(16)}`),
            title: {
                text: 'Country General KPI values'
            },
            subtitle: {
                text: 'Source: Africa CDC'
            },
            xAxis: {
                categories: [
                    'Currenct Year'
                ],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'KPI Value'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            series: seriesData
        });

    });
</script>